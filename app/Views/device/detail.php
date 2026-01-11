<div class="row toolbar">
    <div class="col-sm-8">
        <a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->dev0101 ?>">
            <i class="fa fa-edit"></i> <?= lang('toolbar_edit') ?></a>
        <?php if ($user->isAdmin): ?>
            <button class="btn btn-primary" onclick="UploadDatabase(<?= $data->dev0101 ?>)">上传数据库</button>
            <button class="btn btn-primary" onclick="ShowSqlDialog(<?= $data->dev0101 ?>)">執行SQL</button>
        <?php endif ?>
    </div>
    <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-default" onclick="close_detail()"><i
                    class="fa fa-close"></i> <?= lang('toolbar_close') ?></a>
    </div>
</div>
<div class="row">
    <div class="form-horizontal col-sm-6">
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0104') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev0104 ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_ent1004') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->ent1004 ?></div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0110') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev0110 ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0106') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $dev0106_opt[$data->dev0106] ?></div>
            </div>
        </div>
    </div>
    <div class="form-horizontal col-sm-6">
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0105') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev0105 ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0107') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev0107 ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0111') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev0111 ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev01z2') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev01z2 ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('label_dev0109') ?></label>
            <div class="col-sm-8">
                <div class="form-control"><?= $data->dev0109 ?></div>
            </div>
        </div>
    </div>
</div>
<?php if ($user->isAdmin and isset($files) and $files): ?>
    <div class="box">
        <div class="box-header">
            設備上傳檔案
        </div>
        <div class="box-body">
            <?php
            foreach ($files as $file) {
                echo "<a class='btn btn-default' href=" . base_url('data\\upload\\' . $data->dev0101 . '\\' . $file) . ">$file</a> ";
            }
            ?>
        </div>
    </div>
<?php endif ?>

<script type='text/javascript'>
    function UploadDatabase(dev0101) {
        ajax_load_view(base_url + folder + controller + '/uploaddatabase/' + dev0101,
            function (data) {
                var dataObj = json_decode(data);
                if (dataObj.message == 'OK') {
                    layer.msg('指令已經發送,請稍後自行檢查data\\upload\\' + dev0101 + '目錄');
                }
            });
    }

    function ShowSqlDialog(dev0101) {
        layer.prompt({title: '輸入SQL語句：', formType: 2}, function (text, index) {
            execsql(dev0101, text);
            layer.close(index);
        });
    }

    function execsql(dev0101, sql) {
        ajax_post_view(base_url + folder + controller + '/exec_sql/' + dev0101,
            'sql=' + sql,
            function (data) {
                var dataObj = json_decode(data);
                if (dataObj.message == 'OK') {
                    layer.msg('指令已經發送');
                }
            });
    }

</script>