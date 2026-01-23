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

# 確認 mod_headers 已載入（安全標頭需要）
# Rocky Linux 9 的 Apache 預設已包含，但需確認已啟用
httpd -M | grep headers
# 如果沒有輸出，編輯 /etc/httpd/conf.modules.d/00-base.conf 確認有這行：
# LoadModule headers_module modules/mod_headers.so
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
sudo mkdir -p /var/www/smartpatrol/ci4
sudo mkdir -p /var/www/smartpatrol/ci4/writable/{cache,logs,session,uploads}
sudo mkdir -p /var/www/smartpatrol/ci4/public/data

# 設定權限
sudo chown -R apache:apache /var/www/smartpatrol
sudo chmod -R 755 /var/www/smartpatrol/ci4
```

### 5.2 上傳程式碼

**方式 A：使用 Git（推薦）**
```bash
cd /var/www/smartpatrol
sudo -u apache git clone https://github.com/xinling0926/smartpatrol.git ci4
cd ci4
```

**方式 B：使用 SCP/SFTP 上傳**
```bash
# 在本機執行（打包）
cd /path/to/local/smartpatrol
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
sudo mkdir -p /var/www/smartpatrol/ci4
sudo tar -xzf /tmp/smartpatrol.tar.gz -C /var/www/smartpatrol/ci4
sudo rm /tmp/smartpatrol.tar.gz
```

### 5.3 安裝依賴
```bash
cd /var/www/smartpatrol/ci4
sudo -u apache composer install --no-dev --optimize-autoloader
```

### 5.4 設定環境檔案
```bash
# 複製範本
sudo cp /var/www/smartpatrol/ci4/env /var/www/smartpatrol/ci4/.env

# 編輯設定
sudo nano /var/www/smartpatrol/ci4/.env
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
session.savePath = '/var/www/smartpatrol/ci4/writable/session'

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------
encryption.key = hex2bin:YOUR_32_BYTE_HEX_KEY
```

**生成加密金鑰：**
```bash
php -r "echo 'hex2bin:' . bin2hex(random_bytes(32)) . PHP_EOL;"
```

### 5.5 設定權限
```bash
sudo chown -R apache:apache /var/www/smartpatrol/ci4
sudo chmod -R 755 /var/www/smartpatrol/ci4/writable
sudo chmod -R 755 /var/www/smartpatrol/ci4/public/data
sudo chmod 640 /var/www/smartpatrol/ci4/.env
```

### 5.7 設定 Apache VirtualHost (HTTP)

此設定用於未啟用 SSL 的情況，或作為 HTTPS 跳轉使用：

```bash
sudo tee /etc/httpd/conf.d/patrol.conf << 'EOF'
# SmartPatrol HTTP Configuration
# 若已啟用 SSL，此設定會自動跳轉到 HTTPS

Alias /patrol "/var/www/smartpatrol/ci4/public"

<Directory "/var/www/smartpatrol/ci4/public">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted

    # 安全標頭
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"

    # CSP 設定 (jstree 等元件需要 unsafe-inline/unsafe-eval)
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' blob:; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self';"

    # 啟用 URL 重寫
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteBase /patrol/
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php/$1 [L]
    </IfModule>

    # webapi 使用 HTTP/1.0 (Android APP 相容性)
    <If "%{REQUEST_URI} =~ m#/webapi#">
        SetEnv downgrade-1.0
        SetEnv force-response-1.0
    </If>
</Directory>

# 禁止存取敏感目錄
<Directory "/var/www/smartpatrol/ci4/app">
    Require all denied
</Directory>
<Directory "/var/www/smartpatrol/ci4/writable">
    Require all denied
</Directory>
<Directory "/var/www/smartpatrol/ci4/vendor">
    Require all denied
</Directory>

# 禁止存取敏感檔案
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
<FilesMatch "\.(env|ini|log|sh|json)$">
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
cd /var/www/smartpatrol/ci4
sudo -u apache php spark migrate --all
```

---

## 第七部分：SELinux 設定（如需保持 enforcing）

```bash
# 允許 Apache 連接資料庫
sudo setsebool -P httpd_can_network_connect_db 1

# 允許 Apache 發送外部網路請求（FCM 推送、Webhook 等必要）
sudo setsebool -P httpd_can_network_connect 1

# 允許 Apache 讀寫目錄
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/smartpatrol/ci4/writable(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/smartpatrol/ci4/public/data(/.*)?"
sudo restorecon -Rv /var/www/smartpatrol/ci4/writable/
sudo restorecon -Rv /var/www/smartpatrol/ci4/public/data/

# 確認設定
getsebool httpd_can_network_connect
getsebool httpd_can_network_connect_db
ls -lZ /var/www/smartpatrol/ci4/writable/
```

> **重要**：`httpd_can_network_connect` 是 FCM 推播通知必須的設定，否則 PHP 無法連接 Google 伺服器。

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

### 8.2 設定 SSL VirtualHost

建立完整的 HTTPS 設定檔：

```bash
sudo tee /etc/httpd/conf.d/patrol-ssl.conf << 'EOF'
<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/html

    # SSL 憑證設定
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/your-domain.com/cert.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/your-domain.com/privkey.pem
    SSLCertificateChainFile /etc/letsencrypt/live/your-domain.com/chain.pem

    # SSL 安全設定
    SSLProtocol all -SSLv2 -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder on

    # SmartPatrol 應用程式
    Alias /patrol "/var/www/smartpatrol/ci4/public"

    <Directory "/var/www/smartpatrol/ci4/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        # 安全標頭
        Header always set X-Frame-Options "SAMEORIGIN"
        Header always set X-XSS-Protection "1; mode=block"
        Header always set X-Content-Type-Options "nosniff"
        Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

        # CSP 設定 (jstree 等元件需要 unsafe-inline/unsafe-eval)
        Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' blob:; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self';"

        # 啟用 URL 重寫
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /patrol/
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php/$1 [L]
        </IfModule>

        # webapi 使用 HTTP/1.0 (Android APP 相容性)
        <If "%{REQUEST_URI} =~ m#/webapi#">
            SetEnv downgrade-1.0
            SetEnv force-response-1.0
        </If>
    </Directory>

    # 禁止存取敏感目錄
    <Directory "/var/www/smartpatrol/ci4/app">
        Require all denied
    </Directory>
    <Directory "/var/www/smartpatrol/ci4/writable">
        Require all denied
    </Directory>
    <Directory "/var/www/smartpatrol/ci4/vendor">
        Require all denied
    </Directory>

    # 禁止存取敏感檔案
    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>
    <FilesMatch "\.(env|ini|log|sh|json)$">
        Require all denied
    </FilesMatch>
</VirtualHost>
EOF
```

### 8.3 設定 HTTP 自動跳轉 HTTPS

```bash
sudo tee /etc/httpd/conf.d/redirect-https.conf << 'EOF'
# HTTP 自動跳轉到 HTTPS
<VirtualHost *:80>
    ServerName your-domain.com

    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>
EOF
```

### 8.4 測試並重啟 Apache

```bash
# 測試設定
sudo apachectl configtest

# 重啟 Apache
sudo systemctl restart httpd

# 驗證 SSL
curl -I https://your-domain.com/patrol/
```

---

## 第九部分：Firebase Cloud Messaging (FCM) 設定

如需使用 APP 推播通知功能，請依照以下步驟設定：

### 9.1 取得 Firebase Service Account

1. 前往 [Firebase Console](https://console.firebase.google.com/)
2. 選擇您的專案（或建立新專案）
3. 進入 **專案設定** → **服務帳戶**
4. 點擊 **產生新的私密金鑰**
5. 下載 JSON 檔案

### 9.2 上傳 Service Account 檔案

```bash
# 上傳到伺服器的 writable 目錄
sudo cp firebase-service-account.json /var/www/smartpatrol/ci4/writable/

# 設定權限（只有 Apache 可讀取）
sudo chown apache:apache /var/www/smartpatrol/ci4/writable/firebase-service-account.json
sudo chmod 600 /var/www/smartpatrol/ci4/writable/firebase-service-account.json
```

### 9.3 確認 App.php 設定

檢查 `app/Config/App.php` 中的 FCM 設定：
```php
public string $fcmServiceAccountFile = 'firebase-service-account.json';
```

### 9.4 驗證 FCM 連線

```bash
# 確認 SELinux 允許外部連線
getsebool httpd_can_network_connect
# 應該顯示 httpd_can_network_connect --> on

# 檢查 PHP 是否能連接 Google
curl -I https://oauth2.googleapis.com
```

### 9.5 測試推播

在後台「消息推送」功能新增一則訊息，檢查日誌：
```bash
tail -f /var/www/smartpatrol/ci4/writable/logs/log-$(date +%Y-%m-%d).log | grep -i fcm
```

---

## 第十部分：GitHub Webhook 自動部署（選用）

### 10.1 設定 Webhook Secret

```bash
# 生成隨機 secret
openssl rand -hex 32
# 例如：883784c2eef562cd3d97712c2314a6de2a27a9cab7bb62ce587668d64e577f95
```

### 10.2 修改 webhook.php

編輯 `/var/www/smartpatrol/ci4/public/webhook.php`，更新 secret：
```php
$secret = 'YOUR_GENERATED_SECRET';
```

### 10.3 在 GitHub 設定 Webhook

1. 進入 GitHub repo → **Settings** → **Webhooks**
2. 點擊 **Add webhook**
3. 設定：
   - Payload URL: `https://your-domain.com/patrol/webhook.php`
   - Content type: `application/json`
   - Secret: 輸入上面生成的 secret
   - Events: 選擇 **Just the push event**

### 10.4 設定自動部署腳本

```bash
# 建立部署腳本
sudo tee /var/www/smartpatrol/ci4/deploy.sh << 'EOF'
#!/bin/bash
FLAG_FILE="/var/www/smartpatrol/ci4/writable/deploy.flag"
LOG_FILE="/var/www/smartpatrol/ci4/writable/logs/deploy.log"

if [ -f "$FLAG_FILE" ]; then
    echo "$(date '+%Y-%m-%d %H:%M:%S') - Starting deployment" >> $LOG_FILE

    cd /var/www/smartpatrol/ci4
    sudo -u apache git pull >> $LOG_FILE 2>&1
    sudo -u apache composer install --no-dev --optimize-autoloader >> $LOG_FILE 2>&1

    # 清除快取
    rm -rf /var/www/smartpatrol/ci4/writable/cache/*

    rm -f "$FLAG_FILE"
    echo "$(date '+%Y-%m-%d %H:%M:%S') - Deployment completed" >> $LOG_FILE
fi
EOF

sudo chmod +x /var/www/smartpatrol/ci4/deploy.sh
```

### 10.5 設定 Cron Job

```bash
# 每分鐘檢查是否有部署請求
echo "* * * * * root /var/www/smartpatrol/ci4/deploy.sh" | sudo tee /etc/cron.d/patrol-deploy
```

---

## 第十一部分：驗證安裝

### 11.1 檢查服務狀態
```bash
# Apache
sudo systemctl status httpd

# PHP
php -v

# MySQL
sudo systemctl status mysqld
```

### 11.2 檢查日誌
```bash
# Apache 錯誤日誌
sudo tail -f /var/log/httpd/error_log

# PHP 錯誤日誌
sudo tail -f /var/log/php_errors.log

# SmartPatrol 日誌
sudo tail -f /var/www/smartpatrol/ci4/writable/logs/log-$(date +%Y-%m-%d).log
```

### 11.3 測試網站
```bash
# 本機測試
curl -I http://localhost/patrol/

# 或開啟瀏覽器訪問
# https://your-domain.com/patrol/
```

---

## 第十二部分：維護指令

### 更新應用程式
```bash
cd /var/www/smartpatrol/ci4

# 拉取最新程式碼
sudo -u apache git pull

# 更新依賴套件
sudo -u apache composer install --no-dev --optimize-autoloader

# 執行遷移
sudo -u apache php spark migrate --all

# 清除快取
sudo rm -rf /var/www/smartpatrol/ci4/writable/cache/*
```

### 回滾版本
```bash
cd /var/www/smartpatrol/ci4

# 查看歷史版本
git log --oneline -10

# 切換到指定版本
sudo -u apache git checkout <commit-hash>

# 或回到上一版本
sudo -u apache git checkout HEAD~1

# 回到最新版本
sudo -u apache git checkout main

# 清除快取
sudo rm -rf /var/www/smartpatrol/ci4/writable/cache/*
```

### 清除快取
```bash
sudo rm -rf /var/www/smartpatrol/ci4/writable/cache/*
```

---

## 第十三部分：日誌管理

### 13.1 設定 Log Rotation

```bash
sudo tee /etc/logrotate.d/smartpatrol << 'EOF'
/var/www/smartpatrol/ci4/writable/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 apache apache
    sharedscripts
    postrotate
        /bin/systemctl reload httpd > /dev/null 2>&1 || true
    endscript
}
EOF

# 測試設定
sudo logrotate -d /etc/logrotate.d/smartpatrol
```

### 13.2 清理舊日誌

```bash
# 手動清理超過 30 天的日誌
find /var/www/smartpatrol/ci4/writable/logs -name "*.log" -mtime +30 -delete

# 清理舊的 session 檔案
find /var/www/smartpatrol/ci4/writable/session -type f -mtime +7 -delete
```

---

## 第十四部分：PHP 特殊設定

### 14.1 調整 disable_functions（如需要）

如果某些功能無法正常運作，可能需要調整 PHP 的 disable_functions：

```bash
sudo nano /etc/php.d/99-security.ini
```

**FCM 推播需要的函數：**
確保以下函數**不在** disable_functions 清單中：
- `curl_exec`
- `curl_multi_exec`

**修改後重啟 Apache：**
```bash
sudo systemctl restart httpd
```

### 14.2 增加 PHP 記憶體限制（大檔案上傳）

```bash
# 如果需要處理大型 APK 或檔案
sudo tee -a /etc/php.d/99-uploads.ini << 'EOF'
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 600
EOF

sudo systemctl restart httpd
```

---

## 常見問題

### Q1: 403 Forbidden
```bash
# 檢查權限
ls -la /var/www/smartpatrol/ci4/public/

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
cat /var/www/smartpatrol/ci4/.env
```

### Q3: 資料庫連線失敗
```bash
# 測試連線
mysql -u patrol_user -p -e "SELECT 1"

# 檢查 .env 中的資料庫設定
grep database /var/www/smartpatrol/ci4/.env
```

### Q4: FCM 推播失敗 - cURL error 7

**錯誤訊息：**
```
cURL error 7: Failed to connect to oauth2.googleapis.com
```

**解決方案：**
```bash
# 檢查 SELinux 設定
getsebool httpd_can_network_connect

# 如果顯示 off，啟用它
sudo setsebool -P httpd_can_network_connect 1
```

### Q5: FCM 推播失敗 - NotRegistered

**錯誤訊息：**
```
The registration token is not a valid FCM registration token
```

**原因：**
- FCM token 已失效（APP 被移除或重新安裝）
- Token 在資料庫中被截斷（欄位長度不足）

**解決方案：**
1. 確認資料庫 `dev01.dev0107` 欄位長度至少 255 字元
2. 讓使用者重新登入 APP 以更新 token

### Q6: Apache 安全標頭沒有生效

```bash
# 檢查 mod_headers 是否載入
httpd -M | grep headers

# 如果沒有，確認 modules 設定
cat /etc/httpd/conf.modules.d/00-base.conf | grep headers

# 檢查回應標頭
curl -I https://your-domain.com/patrol/
```

### Q7: 上傳大檔案失敗 (APK)

```bash
# 檢查 PHP 設定
php -i | grep -E "(upload_max|post_max|memory_limit)"

# 修改設定
sudo nano /etc/php.d/99-uploads.ini
# 加入：
# upload_max_filesize = 100M
# post_max_size = 100M

sudo systemctl restart httpd
```

---

## 安全檢查清單

### 基本安全
- [ ] 防火牆只開啟必要 port (80, 443)
- [ ] 使用 HTTPS (Let's Encrypt 或商業憑證)
- [ ] HTTP 自動重導向到 HTTPS

### SELinux
- [ ] SELinux 設定為 enforcing 或 permissive
- [ ] `httpd_can_network_connect_db = on` (資料庫連線)
- [ ] `httpd_can_network_connect = on` (FCM 推播)
- [ ] writable 和 data 目錄有正確的 SELinux context

### Apache
- [ ] 隱藏 Apache 版本 (`ServerTokens Prod`)
- [ ] 已載入 mod_headers 模組
- [ ] 已設定安全標頭 (X-Frame-Options, X-XSS-Protection 等)
- [ ] 禁止目錄瀏覽 (`Options -Indexes`)
- [ ] 已封鎖敏感檔案存取 (.env, .git 等)

### PHP
- [ ] 隱藏 PHP 版本 (`expose_php = Off`)
- [ ] 已停用危險函數 (但保留 FCM 需要的 curl 函數)
- [ ] Session 安全設定 (httponly, secure, samesite)
- [ ] 錯誤訊息不顯示在頁面 (`display_errors = Off`)

### 應用程式
- [ ] .env 檔案權限為 640
- [ ] writable 目錄不可被直接存取
- [ ] 資料庫使用強密碼
- [ ] Firebase Service Account JSON 權限為 600
- [ ] Webhook secret 已更換為隨機值

### 維護
- [ ] 已設定日誌輪替 (logrotate)
- [ ] 已設定自動備份
- [ ] 已設定 SSL 憑證自動更新 (certbot)

---

## 快速檢查指令

```bash
# SELinux 狀態
getenforce
getsebool httpd_can_network_connect
getsebool httpd_can_network_connect_db

# Apache 模組
httpd -M | grep -E "(headers|rewrite|ssl)"

# PHP 設定
php -i | grep -E "(expose_php|display_errors|disable_functions)"

# 檔案權限
ls -la /var/www/smartpatrol/ci4/.env
ls -la /var/www/smartpatrol/ci4/writable/firebase-service-account.json

# SELinux context
ls -lZ /var/www/smartpatrol/ci4/writable/

# 測試 HTTPS
curl -I https://your-domain.com/patrol/
```
