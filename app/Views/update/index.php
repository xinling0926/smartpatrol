<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-cloud-upload"></i> 系統程式更新</h3>
    </div>
    <div class="box-body">
        <div class="callout callout-warning">
            <h4>注意事項</h4>
            <ul>
                <li>請先在開發環境測試確認程式無誤再上傳</li>
                <li>更新前系統會自動備份目前版本到 writable 目錄</li>
                <li>zip 檔案應包含 <code>app/</code> 和 <code>public/</code> 目錄</li>
                <li>更新過程中請勿關閉瀏覽器</li>
            </ul>
        </div>

        <form id="upload_form" enctype="multipart/form-data">
            <div class="form-group">
                <label>選擇 zip 檔案：</label>
                <input type="file" name="zipfile" id="zipfile" accept=".zip" required>
            </div>
            <button type="button" class="btn btn-primary" onclick="doUpload()">
                <i class="fa fa-upload"></i> 開始更新
            </button>
        </form>

        <div id="result" style="margin-top: 20px; display: none;">
            <h4>執行結果：</h4>
            <pre id="result_log" style="max-height: 400px; overflow-y: auto;"></pre>
        </div>
    </div>
</div>

<script>
function doUpload() {
    var fileInput = document.getElementById('zipfile');
    if (!fileInput.files.length) {
        alert('請選擇檔案');
        return;
    }

    if (!confirm('確定要更新系統嗎？建議先備份資料庫。')) {
        return;
    }

    var formData = new FormData();
    formData.append('zipfile', fileInput.files[0]);

    document.getElementById('result').style.display = 'block';
    document.getElementById('result_log').textContent = '上傳中...請稍候';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', base_url + 'update/upload', true);
    xhr.onload = function() {
        try {
            var res = JSON.parse(xhr.responseText);
            var log = res.data && res.data.log ? res.data.log.join('\n') : '';
            if (res.message === 'OK') {
                log += '\n\n✓ 更新成功！備份位置: ' + (res.data.backup || '');
            } else {
                log += '\n\n✗ 更新失敗: ' + (res.data.description || '');
            }
            document.getElementById('result_log').textContent = log;
        } catch (e) {
            document.getElementById('result_log').textContent = '回應錯誤:\n' + xhr.responseText;
        }
    };
    xhr.onerror = function() {
        document.getElementById('result_log').textContent = '連線錯誤';
    };
    xhr.send(formData);
}
</script>
