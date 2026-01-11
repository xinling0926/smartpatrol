<!DOCTYPE html>
<html>
<body>
    <div style="font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: large">
        <h2><a href="<?= $base_url ?>" style="color: #444;font-size: 35px;"><?= $site_title ?></a></h2>
        <ul>
            <li>巡檢報表名稱： <?= $report_name ?></li>
            <li>報表日期： <?= $iso_date ?></li>
            <li>事件： <?= $notification ?></li>
        </ul>
        <p><?=now()?></p>
    </div>
</body>
</html>