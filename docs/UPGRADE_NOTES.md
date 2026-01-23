# SmartPatrol 系統升級說明

## 升級概要

| 項目 | 升級前 | 升級後 |
|------|--------|--------|
| 主機 IP | - | 61.216.3.63 |
| PHP 版本 | 5.6 | 8.3 |
| 後台框架 | CodeIgniter 3 | CodeIgniter 4 |
| 推播服務 | 百度推送 | Google FCM |
| APP 支援版本 | - | Android 7-13 |

---

## 升級項目詳細說明

### 1. PHP 版本升級 (5.6 → 8.3)

**升級內容：**
- PHP 版本從 5.6 升級至 8.3
- 支援最新的 PHP 語法特性與效能優化
- 強化安全性設定

**效益：**
- 效能提升約 2-3 倍
- 支援最新安全修補
- 支援現代化 Composer 套件

---

### 2. 後台框架升級 (CodeIgniter 3 → CodeIgniter 4)

**升級內容：**
- 框架從 CodeIgniter 3 升級至 CodeIgniter 4
- 完整重構程式碼以符合 CI4 架構
- 採用命名空間 (Namespace) 組織程式碼
- 支援 PSR-4 自動載入標準

**效益：**
- 更好的程式碼組織與維護性
- 支援依賴注入 (Dependency Injection)
- 強化 MVC 架構
- 更好的單元測試支援

**注意事項：**
- URL 路由規則已更新
- Session 處理方式變更
- 設定檔格式更新為 PHP 類別

---

### 3. 推播服務更換 (百度推送 → Google FCM)

**升級內容：**
- 將推播服務從百度雲推送 (Baidu Push) 更換為 Google Firebase Cloud Messaging (FCM)
- 使用 FCM HTTP v1 API
- 採用 Service Account 認證方式

**效益：**
- 推播更加穩定可靠
- 支援全球性推播服務
- 更好的推播成功率追蹤
- 支援訊息已讀回報功能

**技術細節：**
- 使用 `kreait/firebase-php` 套件 (v8.0)
- Service Account JSON 檔案存放於 `writable/firebase-service-account.json`
- 支援單一裝置推播與群發推播

**相關 API：**
- `POST /webapi/messageAck/receive` - 回報訊息已接收
- `POST /webapi/messageAck/read` - 回報訊息已讀取

---

### 4. 自動更新功能

**升級內容：**
- 新增 GitHub Webhook 自動部署功能
- 當程式碼推送至 GitHub 後，伺服器自動拉取更新

**運作流程：**
1. 開發者推送程式碼至 GitHub
2. GitHub 發送 Webhook 至伺服器
3. 伺服器驗證簽章後建立部署旗標
4. Cron Job 每分鐘檢查並執行部署

**相關檔案：**
- `public/webhook.php` - Webhook 接收端點
- `deploy.sh` - 自動部署腳本
- `writable/deploy.flag` - 部署觸發旗標

---

### 5. APP 支援 Android 7-13

**升級內容：**
- APP 最低支援版本：Android 7.0 (API Level 24)
- APP 最高支援版本：Android 13 (API Level 33)
- 優化不同 Android 版本的相容性

**效益：**
- 涵蓋大多數使用中的 Android 裝置
- 支援最新 Android 安全特性
- 優化電池使用與背景執行

---

### 6. APP 支援自訂 Port

**升級內容：**
- APP 登入設定支援自訂伺服器 Port
- 可透過設定畫面輸入非標準 Port

**使用方式：**
- 在 APP 設定中輸入完整的伺服器位址，包含 Port
- 格式：`https://server.domain.com:8443/patrol/`

**效益：**
- 支援非標準 Port 部署
- 增加部署彈性
- 可配合防火牆規則使用

---

## 系統需求

### 伺服器端
- Rocky Linux 9.x
- PHP 8.3+
- MySQL 8.0+
- Apache 2.4+ (含 mod_rewrite, mod_headers)
- Composer 2.x

### APP 端
- Android 7.0 - 13 (API Level 24-33)
- 需要網路連線
- 需要推播通知權限

---

## 升級後驗證項目

### 後台功能驗證
- [ ] 登入功能正常
- [ ] 各功能模組可正常操作
- [ ] 資料查詢與新增/修改/刪除正常
- [ ] 報表產出正常
- [ ] 檔案上傳功能正常

### 推播功能驗證
- [ ] 後台發送推播訊息成功
- [ ] APP 可正常接收推播
- [ ] 訊息接收/已讀回報正常
- [ ] 推播日誌記錄正常

### APP 功能驗證
- [ ] APP 登入功能正常
- [ ] 巡檢功能正常操作
- [ ] 照片上傳功能正常
- [ ] 自訂 Port 連線正常
- [ ] 推播接收正常

---

## 相關文件

- [安裝指南](INSTALL_ROCKY_LINUX_9.md) - Rocky Linux 9 完整安裝步驟
- [API 文件](API.md) - API 端點說明 (如有)

---

## 聯絡資訊

如有任何問題，請聯絡系統管理員。

---

*文件更新日期：2026-01-23*
