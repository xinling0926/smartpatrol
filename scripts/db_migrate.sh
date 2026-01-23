#!/bin/bash
#
# SmartPatrol 資料庫遷移腳本
# 功能：分表匯出/匯入資料庫，避免大型資料庫一次性操作失敗
#
# 使用方法：
#   匯出：./db_migrate.sh export
#   匯入：./db_migrate.sh import
#   傳輸：./db_migrate.sh transfer user@remote_host
#   完整遷移：./db_migrate.sh migrate user@remote_host
#

set -e

# =====================================================
# 設定區域 - 請根據實際情況修改
# =====================================================

# 來源資料庫設定
SRC_HOST="localhost"
SRC_USER="patrol_user"
SRC_PASS="YOUR_SOURCE_PASSWORD"
SRC_DB="smartpatrol"

# 目標資料庫設定（用於遠端匯入）
DST_HOST="localhost"
DST_USER="patrol_user"
DST_PASS="YOUR_DESTINATION_PASSWORD"
DST_DB="smartpatrol"

# 匯出目錄
EXPORT_DIR="/tmp/db_export_${SRC_DB}"

# 壓縮選項 (gzip 或 none)
COMPRESSION="gzip"

# 平行處理數量 (匯出時同時處理的表數量)
PARALLEL_JOBS=4

# =====================================================
# 顏色輸出
# =====================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# =====================================================
# 函數定義
# =====================================================

# 取得所有資料表列表
get_tables() {
    mysql -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" -N -e \
        "SELECT table_name FROM information_schema.tables WHERE table_schema='$SRC_DB' AND table_type='BASE TABLE' ORDER BY table_name;" 2>/dev/null
}

# 取得資料表大小資訊
get_table_size() {
    local table=$1
    mysql -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" -N -e \
        "SELECT ROUND((data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
         FROM information_schema.tables
         WHERE table_schema='$SRC_DB' AND table_name='$table';" 2>/dev/null
}

# 匯出單一資料表
export_table() {
    local table=$1
    local size=$(get_table_size "$table")
    local output_file="${EXPORT_DIR}/${table}.sql"

    log_info "匯出資料表: $table (${size} MB)"

    if [ "$COMPRESSION" = "gzip" ]; then
        mysqldump -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" \
            --single-transaction \
            --quick \
            --lock-tables=false \
            --set-gtid-purged=OFF \
            "$SRC_DB" "$table" 2>/dev/null | gzip > "${output_file}.gz"

        if [ $? -eq 0 ]; then
            local file_size=$(du -h "${output_file}.gz" | cut -f1)
            log_info "  ✓ 完成: ${output_file}.gz ($file_size)"
        else
            log_error "  ✗ 失敗: $table"
            return 1
        fi
    else
        mysqldump -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" \
            --single-transaction \
            --quick \
            --lock-tables=false \
            --set-gtid-purged=OFF \
            "$SRC_DB" "$table" > "$output_file" 2>/dev/null

        if [ $? -eq 0 ]; then
            local file_size=$(du -h "$output_file" | cut -f1)
            log_info "  ✓ 完成: ${output_file} ($file_size)"
        else
            log_error "  ✗ 失敗: $table"
            return 1
        fi
    fi
}

# 匯出資料庫結構（不含資料）
export_schema() {
    log_step "匯出資料庫結構..."

    mysqldump -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" \
        --no-data \
        --routines \
        --triggers \
        --events \
        --set-gtid-purged=OFF \
        "$SRC_DB" 2>/dev/null > "${EXPORT_DIR}/_schema.sql"

    if [ $? -eq 0 ]; then
        log_info "✓ 結構匯出完成: ${EXPORT_DIR}/_schema.sql"
    else
        log_error "✗ 結構匯出失敗"
        return 1
    fi
}

# 匯出所有資料表
do_export() {
    log_step "開始匯出資料庫: $SRC_DB"

    # 建立匯出目錄
    mkdir -p "$EXPORT_DIR"

    # 匯出結構
    export_schema

    # 取得所有資料表
    tables=$(get_tables)
    total=$(echo "$tables" | wc -l | tr -d ' ')

    log_info "共 $total 個資料表需要匯出"
    echo ""

    # 建立資料表列表檔案
    echo "$tables" > "${EXPORT_DIR}/_tables.txt"

    # 計數器
    count=0
    failed=0

    # 逐一匯出
    for table in $tables; do
        count=$((count + 1))
        echo -e "${BLUE}[$count/$total]${NC}"

        if export_table "$table"; then
            :
        else
            failed=$((failed + 1))
        fi
        echo ""
    done

    # 建立完成標記
    echo "$(date '+%Y-%m-%d %H:%M:%S')" > "${EXPORT_DIR}/_completed.txt"
    echo "Total tables: $total" >> "${EXPORT_DIR}/_completed.txt"
    echo "Failed: $failed" >> "${EXPORT_DIR}/_completed.txt"

    echo ""
    log_step "匯出完成！"
    log_info "成功: $((total - failed)) / $total"
    if [ $failed -gt 0 ]; then
        log_warn "失敗: $failed"
    fi
    log_info "匯出目錄: $EXPORT_DIR"
    log_info "總大小: $(du -sh "$EXPORT_DIR" | cut -f1)"
}

# 匯入單一資料表
import_table() {
    local file=$1
    local table=$(basename "$file" .sql.gz)
    table=$(basename "$table" .sql)

    log_info "匯入資料表: $table"

    if [[ "$file" == *.gz ]]; then
        gunzip -c "$file" | mysql -h "$DST_HOST" -u "$DST_USER" -p"$DST_PASS" "$DST_DB" 2>/dev/null
    else
        mysql -h "$DST_HOST" -u "$DST_USER" -p"$DST_PASS" "$DST_DB" < "$file" 2>/dev/null
    fi

    if [ $? -eq 0 ]; then
        log_info "  ✓ 完成: $table"
    else
        log_error "  ✗ 失敗: $table"
        return 1
    fi
}

# 匯入所有資料表
do_import() {
    log_step "開始匯入資料庫: $DST_DB"

    if [ ! -d "$EXPORT_DIR" ]; then
        log_error "匯出目錄不存在: $EXPORT_DIR"
        exit 1
    fi

    # 先匯入結構
    if [ -f "${EXPORT_DIR}/_schema.sql" ]; then
        log_step "匯入資料庫結構..."
        mysql -h "$DST_HOST" -u "$DST_USER" -p"$DST_PASS" "$DST_DB" < "${EXPORT_DIR}/_schema.sql" 2>/dev/null
        if [ $? -eq 0 ]; then
            log_info "✓ 結構匯入完成"
        else
            log_error "✗ 結構匯入失敗"
        fi
        echo ""
    fi

    # 取得檔案列表
    if [ "$COMPRESSION" = "gzip" ]; then
        files=$(ls -1 "${EXPORT_DIR}"/*.sql.gz 2>/dev/null | grep -v '_schema')
    else
        files=$(ls -1 "${EXPORT_DIR}"/*.sql 2>/dev/null | grep -v '_schema')
    fi

    total=$(echo "$files" | wc -l | tr -d ' ')
    log_info "共 $total 個資料表需要匯入"
    echo ""

    count=0
    failed=0

    for file in $files; do
        count=$((count + 1))
        echo -e "${BLUE}[$count/$total]${NC}"

        if import_table "$file"; then
            :
        else
            failed=$((failed + 1))
        fi
        echo ""
    done

    echo ""
    log_step "匯入完成！"
    log_info "成功: $((total - failed)) / $total"
    if [ $failed -gt 0 ]; then
        log_warn "失敗: $failed"
    fi
}

# 傳輸到遠端
do_transfer() {
    local remote=$1

    if [ -z "$remote" ]; then
        log_error "請指定遠端主機，例如: ./db_migrate.sh transfer user@remote_host"
        exit 1
    fi

    if [ ! -d "$EXPORT_DIR" ]; then
        log_error "匯出目錄不存在: $EXPORT_DIR"
        log_error "請先執行: ./db_migrate.sh export"
        exit 1
    fi

    log_step "傳輸到遠端: $remote"
    log_info "來源目錄: $EXPORT_DIR"
    log_info "總大小: $(du -sh "$EXPORT_DIR" | cut -f1)"
    echo ""

    # 使用 rsync 傳輸（支援斷點續傳）
    rsync -avz --progress "$EXPORT_DIR" "${remote}:/tmp/"

    if [ $? -eq 0 ]; then
        log_info "✓ 傳輸完成"
        log_info "遠端目錄: /tmp/$(basename "$EXPORT_DIR")"
    else
        log_error "✗ 傳輸失敗"
        exit 1
    fi
}

# 完整遷移流程
do_migrate() {
    local remote=$1

    if [ -z "$remote" ]; then
        log_error "請指定遠端主機，例如: ./db_migrate.sh migrate user@remote_host"
        exit 1
    fi

    log_step "開始完整遷移流程"
    echo ""

    # Step 1: 匯出
    do_export
    echo ""

    # Step 2: 傳輸
    do_transfer "$remote"
    echo ""

    # Step 3: 遠端匯入
    log_step "在遠端執行匯入..."
    log_warn "請在遠端主機執行以下指令："
    echo ""
    echo "  cd /tmp/$(basename "$EXPORT_DIR")"
    echo "  # 編輯此腳本設定目標資料庫連線資訊"
    echo "  # 然後執行："
    echo "  ./db_migrate.sh import"
    echo ""
}

# 顯示資料庫狀態
show_status() {
    log_step "資料庫狀態: $SRC_DB"
    echo ""

    # 取得資料表數量
    table_count=$(mysql -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" -N -e \
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$SRC_DB';" 2>/dev/null)

    # 取得資料庫大小
    db_size=$(mysql -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" -N -e \
        "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2)
         FROM information_schema.tables WHERE table_schema='$SRC_DB';" 2>/dev/null)

    log_info "資料表數量: $table_count"
    log_info "資料庫大小: ${db_size} MB"
    echo ""

    # 顯示前 10 大資料表
    log_info "前 10 大資料表："
    mysql -h "$SRC_HOST" -u "$SRC_USER" -p"$SRC_PASS" -e \
        "SELECT
            table_name AS 'Table',
            ROUND((data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)',
            table_rows AS 'Rows'
         FROM information_schema.tables
         WHERE table_schema='$SRC_DB'
         ORDER BY (data_length + index_length) DESC
         LIMIT 10;" 2>/dev/null
}

# 顯示使用說明
show_help() {
    echo "SmartPatrol 資料庫遷移腳本"
    echo ""
    echo "使用方法："
    echo "  $0 <command> [options]"
    echo ""
    echo "指令："
    echo "  status              顯示資料庫狀態"
    echo "  export              匯出所有資料表到本地目錄"
    echo "  import              從本地目錄匯入所有資料表"
    echo "  transfer <host>     傳輸匯出檔案到遠端主機"
    echo "  migrate <host>      完整遷移流程 (匯出 + 傳輸)"
    echo "  help                顯示此說明"
    echo ""
    echo "範例："
    echo "  $0 status"
    echo "  $0 export"
    echo "  $0 transfer root@192.168.1.100"
    echo "  $0 migrate root@192.168.1.100"
    echo ""
    echo "設定："
    echo "  請編輯腳本開頭的設定區域，修改資料庫連線資訊"
    echo ""
    echo "匯出目錄："
    echo "  $EXPORT_DIR"
    echo ""
}

# =====================================================
# 主程式
# =====================================================

case "$1" in
    status)
        show_status
        ;;
    export)
        do_export
        ;;
    import)
        do_import
        ;;
    transfer)
        do_transfer "$2"
        ;;
    migrate)
        do_migrate "$2"
        ;;
    help|--help|-h|"")
        show_help
        ;;
    *)
        log_error "未知指令: $1"
        show_help
        exit 1
        ;;
esac
