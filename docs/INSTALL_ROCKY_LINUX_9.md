# SmartPatrol 安裝指南 - Rocky Linux 9

## 系統需求
- Rocky Linux 9.x
- 最少 2GB RAM
- 最少 20GB 硬碟空間
- MySQL 8.0+ (已安裝)

---

## 第一部分：系統準備

### 1.1 更新系統
```bash
sudo dnf update -y
sudo dnf install -y epel-release
```

### 1.2 設定防火牆
```bash
# 開啟必要的 port
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload

# 確認設定
sudo firewall-cmd --list-all
```

### 1.3 停用 SELinux（或設定為 permissive）
```bash
# 檢查目前狀態
getenforce

# 設定為 permissive（建議先用這個測試）
sudo setenforce 0

# 永久設定（編輯設定檔）
sudo sed -i 's/SELINUX=enforcing/SELINUX=permissive/' /etc/selinux/config
```

> **注意**：如需保持 SELinux 為 enforcing，請參考第六部分的 SELinux 設定。

---

## 第二部分：安裝 Apache

### 2.1 安裝 Apache
```bash
sudo dnf install -y httpd mod_ssl
```

### 2.2 啟動並設定開機自動啟動
```bash
sudo systemctl start httpd
sudo systemctl enable httpd
sudo systemctl status httpd
```

### 2.3 Apache 安全設定
```bash
# 備份原設定
sudo cp /etc/httpd/conf/httpd.conf /etc/httpd/conf/httpd.conf.bak

# 編輯設定
sudo tee -a /etc/httpd/conf.d/security.conf << 'EOF'
# 隱藏 Apache 版本資訊
ServerTokens Prod
ServerSignature Off

# 停用目錄瀏覽
<Directory />
    Options -Indexes -FollowSymLinks
    AllowOverride None
    Require all denied
</Directory>

# 防止 Clickjacking
Header always set X-Frame-Options "SAMEORIGIN"

# 防止 XSS
Header always set X-XSS-Protection "1; mode=block"

# 防止 MIME 類型嗅探
Header always set X-Content-Type-Options "nosniff"

# 啟用 HSTS（如果使用 HTTPS）
# Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

# 限制 HTTP 方法
<LimitExcept GET POST HEAD>
    Require all denied
</LimitExcept>
EOF
```

### 2.4 啟用必要模組
```bash
# 確認 mod_rewrite 已啟用（CI4 需要）
sudo dnf install -y mod_ssl
```

---

## 第三部分：安裝 PHP 8.3

### 3.1 安裝 Remi Repository
```bash
# 安裝 Remi repo
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm

# 啟用 PHP 8.3
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.3 -y
```

### 3.2 安裝 PHP 及擴展
```bash
sudo dnf install -y php php-cli php-common php-fpm \
    php-mysqlnd php-pdo \
    php-gd php-mbstring php-xml php-json \
    php-curl php-zip php-intl \
    php-opcache php-bcmath \
    php-pecl-apcu
```

### 3.3 驗證安裝
```bash
php -v
php -m | grep -E "(mysql|gd|mbstring|curl|intl|json)"
```

### 3.4 PHP 安全設定
```bash
# 備份原設定
sudo cp /etc/php.ini /etc/php.ini.bak

# 編輯 PHP 設定
sudo tee /etc/php.d/99-security.ini << 'EOF'
; === 安全設定 ===

; 隱藏 PHP 版本
expose_php = Off

; 停用危險函數
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,eval

; Session 安全
session.cookie_httponly = On
session.cookie_secure = On
session.use_strict_mode = On
session.cookie_samesite = Strict

; 上傳設定
file_uploads = On
upload_max_filesize = 50M
post_max_size = 50M
max_file_uploads = 10

; 記憶體與時間限制
memory_limit = 256M
max_execution_time = 300
max_input_time = 300

; 錯誤處理（正式環境）
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; 時區
date.timezone = Asia/Taipei
EOF
```

### 3.5 重啟 Apache
```bash
sudo systemctl restart httpd
```

---

## 第四部分：安裝 Composer

```bash
# 下載並安裝 Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer

# 驗證
composer --version
```

---

## 第五部分：部署 SmartPatrol

### 5.1 建立目錄結構
```bash
# 建立應用程式目錄
sudo mkdir -p /var/www/patrol/{releases,shared}
sudo mkdir -p /var/www/patrol/shared/{writable,data}
sudo mkdir -p /var/www/patrol/shared/writable/{cache,logs,session,uploads}

# 設定權限
sudo chown -R apache:apache /var/www/patrol
sudo chmod -R 755 /var/www/patrol
```

### 5.2 上傳程式碼

**方式 A：使用 Git（推薦）**
```bash
cd /var/www/patrol/releases
sudo -u apache git clone https://github.com/xinling0926/smartpatrol.git $(date +%Y%m%d_%H%M%S)

# 取得最新的 release 目錄名稱
RELEASE_DIR=$(ls -1t /var/www/patrol/releases | head -1)
```

**方式 B：使用 SCP/SFTP 上傳**
```bash
# 在本機執行（打包）
cd /Users/randylin/www/cgmh_ci4
tar -czf smartpatrol.tar.gz \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='writable/cache/*' \
    --exclude='writable/logs/*' \
    --exclude='writable/session/*' \
    --exclude='public/data/*' \
    .

# 上傳到伺服器
scp smartpatrol.tar.gz user@your-server:/tmp/

# 在伺服器執行（解壓）
RELEASE_DIR=$(date +%Y%m%d_%H%M%S)
sudo mkdir -p /var/www/patrol/releases/${RELEASE_DIR}
sudo tar -xzf /tmp/smartpatrol.tar.gz -C /var/www/patrol/releases/${RELEASE_DIR}
sudo rm /tmp/smartpatrol.tar.gz
```

### 5.3 安裝依賴
```bash
cd /var/www/patrol/releases/${RELEASE_DIR}
sudo -u apache composer install --no-dev --optimize-autoloader
```

### 5.4 設定環境檔案
```bash
# 複製範本
sudo cp /var/www/patrol/releases/${RELEASE_DIR}/env /var/www/patrol/shared/.env

# 編輯設定
sudo nano /var/www/patrol/shared/.env
```

**編輯 .env 內容：**
```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://your-domain.com/patrol/'
app.indexPage = ''

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = smartpatrol
database.default.username = patrol_user
database.default.password = YOUR_STRONG_PASSWORD
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.savePath = '/var/www/patrol/shared/writable/session'

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------
encryption.key = hex2bin:YOUR_32_BYTE_HEX_KEY
```

**生成加密金鑰：**
```bash
php -r "echo 'hex2bin:' . bin2hex(random_bytes(32)) . PHP_EOL;"
```

### 5.5 建立 Symbolic Links
```bash
RELEASE_DIR=$(ls -1t /var/www/patrol/releases | head -1)

# 連結 .env
sudo rm -f /var/www/patrol/releases/${RELEASE_DIR}/.env
sudo ln -s /var/www/patrol/shared/.env /var/www/patrol/releases/${RELEASE_DIR}/.env

# 連結 writable
sudo rm -rf /var/www/patrol/releases/${RELEASE_DIR}/writable
sudo ln -s /var/www/patrol/shared/writable /var/www/patrol/releases/${RELEASE_DIR}/writable

# 連結 data
sudo rm -rf /var/www/patrol/releases/${RELEASE_DIR}/public/data
sudo ln -s /var/www/patrol/shared/data /var/www/patrol/releases/${RELEASE_DIR}/public/data

# 建立 current link
sudo rm -f /var/www/patrol/current
sudo ln -s /var/www/patrol/releases/${RELEASE_DIR} /var/www/patrol/current
```

### 5.6 設定權限
```bash
sudo chown -R apache:apache /var/www/patrol
sudo chmod -R 755 /var/www/patrol/shared/writable
sudo chmod -R 755 /var/www/patrol/shared/data
sudo chmod 640 /var/www/patrol/shared/.env
```

### 5.7 設定 Apache VirtualHost
```bash
sudo tee /etc/httpd/conf.d/patrol.conf << 'EOF'
# SmartPatrol Virtual Host Configuration

Alias /patrol "/var/www/patrol/current/public"

<Directory "/var/www/patrol/current/public">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted

    # 啟用 URL 重寫
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteBase /patrol/
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php/$1 [L]
    </IfModule>
</Directory>

# 禁止存取敏感檔案
<FilesMatch "^\.">
    Require all denied
</FilesMatch>

<FilesMatch "\.(env|ini|log|sh)$">
    Require all denied
</FilesMatch>
EOF
```

### 5.8 測試並重啟 Apache
```bash
# 測試設定
sudo apachectl configtest

# 重啟
sudo systemctl restart httpd
```

---

## 第六部分：資料庫設定

### 6.1 建立資料庫和使用者
```bash
sudo mysql -u root -p << 'EOF'
-- 建立資料庫
CREATE DATABASE IF NOT EXISTS smartpatrol CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 建立使用者（使用強密碼）
CREATE USER 'patrol_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';

-- 授予權限
GRANT ALL PRIVILEGES ON smartpatrol.* TO 'patrol_user'@'localhost';
FLUSH PRIVILEGES;

-- 顯示結果
SHOW DATABASES LIKE 'smartpatrol';
SELECT User, Host FROM mysql.user WHERE User = 'patrol_user';
EOF
```

### 6.2 匯入資料庫結構
```bash
# 如果有 SQL 檔案
mysql -u patrol_user -p smartpatrol < /path/to/database.sql

# 或執行 CI4 遷移
cd /var/www/patrol/current
sudo -u apache php spark migrate --all
```

---

## 第七部分：SELinux 設定（如需保持 enforcing）

```bash
# 允許 Apache 連接資料庫
sudo setsebool -P httpd_can_network_connect_db 1

# 允許 Apache 讀寫目錄
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/patrol/shared/writable(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/patrol/shared/data(/.*)?"
sudo restorecon -Rv /var/www/patrol/shared/

# 確認
ls -lZ /var/www/patrol/shared/
```

---

## 第八部分：SSL/HTTPS 設定（強烈建議）

### 8.1 使用 Let's Encrypt（免費 SSL）
```bash
# 安裝 Certbot
sudo dnf install -y certbot python3-certbot-apache

# 取得憑證
sudo certbot --apache -d your-domain.com

# 自動更新測試
sudo certbot renew --dry-run

# 設定自動更新 cron
echo "0 3 * * * root certbot renew --quiet" | sudo tee /etc/cron.d/certbot
```

### 8.2 強制 HTTPS 重導向
```bash
sudo tee /etc/httpd/conf.d/redirect-https.conf << 'EOF'
<VirtualHost *:80>
    ServerName your-domain.com
    Redirect permanent / https://your-domain.com/
</VirtualHost>
EOF

sudo systemctl restart httpd
```

---

## 第九部分：驗證安裝

### 9.1 檢查服務狀態
```bash
# Apache
sudo systemctl status httpd

# PHP
php -v

# MySQL
sudo systemctl status mysqld
```

### 9.2 檢查日誌
```bash
# Apache 錯誤日誌
sudo tail -f /var/log/httpd/error_log

# PHP 錯誤日誌
sudo tail -f /var/log/php_errors.log

# SmartPatrol 日誌
sudo tail -f /var/www/patrol/shared/writable/logs/log-$(date +%Y-%m-%d).log
```

### 9.3 測試網站
```bash
# 本機測試
curl -I http://localhost/patrol/

# 或開啟瀏覽器訪問
# https://your-domain.com/patrol/
```

---

## 第十部分：維護指令

### 更新應用程式
```bash
# 下載新版本
cd /var/www/patrol/releases
RELEASE_DIR=$(date +%Y%m%d_%H%M%S)
sudo -u apache git clone https://github.com/xinling0926/smartpatrol.git ${RELEASE_DIR}
cd ${RELEASE_DIR}
sudo -u apache composer install --no-dev --optimize-autoloader

# 建立 symbolic links
sudo rm -f .env && sudo ln -s /var/www/patrol/shared/.env .env
sudo rm -rf writable && sudo ln -s /var/www/patrol/shared/writable writable
sudo rm -rf public/data && sudo ln -s /var/www/patrol/shared/data public/data

# 執行遷移
sudo -u apache php spark migrate --all

# 切換版本
sudo rm -f /var/www/patrol/current
sudo ln -s /var/www/patrol/releases/${RELEASE_DIR} /var/www/patrol/current

# 清除快取
sudo rm -rf /var/www/patrol/shared/writable/cache/*

# 清理舊版本（保留5個）
cd /var/www/patrol/releases && ls -1t | tail -n +6 | xargs -r rm -rf
```

### 回滾版本
```bash
# 查看可用版本
ls -lt /var/www/patrol/releases/

# 切換到指定版本
sudo rm -f /var/www/patrol/current
sudo ln -s /var/www/patrol/releases/20250111_120000 /var/www/patrol/current
sudo rm -rf /var/www/patrol/shared/writable/cache/*
```

### 清除快取
```bash
sudo rm -rf /var/www/patrol/shared/writable/cache/*
```

---

## 常見問題

### Q1: 403 Forbidden
```bash
# 檢查權限
ls -la /var/www/patrol/current/public/

# 檢查 SELinux
getenforce
sudo setenforce 0  # 暫時停用測試
```

### Q2: 500 Internal Server Error
```bash
# 檢查 Apache 日誌
sudo tail -50 /var/log/httpd/error_log

# 檢查 PHP 日誌
sudo tail -50 /var/log/php_errors.log

# 檢查 .env 設定
cat /var/www/patrol/shared/.env
```

### Q3: 資料庫連線失敗
```bash
# 測試連線
mysql -u patrol_user -p -e "SELECT 1"

# 檢查 .env 中的資料庫設定
grep database /var/www/patrol/shared/.env
```

---

## 安全檢查清單

- [ ] 防火牆只開啟必要 port (80, 443)
- [ ] SELinux 設定正確
- [ ] PHP 已停用危險函數
- [ ] Apache 已隱藏版本資訊
- [ ] 使用 HTTPS
- [ ] 資料庫使用強密碼
- [ ] .env 檔案權限為 640
- [ ] writable 目錄不可被直接存取
- [ ] 已設定自動備份
