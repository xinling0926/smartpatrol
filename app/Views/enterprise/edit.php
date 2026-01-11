<div class="row toolbar">
    <div class="col-sm-6 col-sm-offset-6 text-right">
        <a href="#" class="btn btn-success" onclick="save_enterprise()"><i
                    class="fa fa-save"></i> <?= lang('toolbar_save') ?></a>
        <a href="#" class="btn btn-default" onclick="cancel_edit()"><i
                    class="fa fa-undo"></i> <?= lang('toolbar_cancel') ?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
    </div>
    <?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
    <?php if ($data) echo form_hidden('ent0101', $data->ent0101) ?>
    <input type="hidden" id="ent0105" name="ent0105" value="<?php if ($data) {
        echo $data->ent0105;
    } ?>"/>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent0103') ?></label>
            <div class="col-sm-8"><?= form_text_field('ent0103', $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent0102') ?></label>
            <div class="col-sm-8"><?= form_text_field('ent0102', $data) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent0105') ?></label>
            <div class="col-sm-8">
					<span class="btn btn-success fileinput-button">
						<i class="glyphicon glyphicon-plus"></i>
						<span><?= lang('upload_logo') ?></span>
						<input id="file_upload" name="files[]" data-url="enterprise/upload_logo" multiple=""
                               type="file">
					</span>
                <span id="upload_error"><?= lang('upload_logo_label') ?></span>
                <div class="progress progress-striped" style="display: none">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent0104') ?></label>
            <div class="col-sm-8">
                <div class="radio">
                    <label>
                        <input type="radio" name="ent0104" id="ent01040"
                               value="0"<?php if ($data && $data->ent0104 == 0) echo " checked" ?>>
                        <?= lang('v_ent0104_0') ?>
                    </label>
                    <label>
                        <input type="radio" name="ent0104" id="ent01041"
                               value="1"<?php if ($data == null || $data->ent0104 == 1) echo " checked" ?>>
                        <?= lang('v_ent0104_1') ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <?= form_close() ?>
</div>

<?php assets_js('jquery.ui.widget', 'jQuery-File-Upload/vendor') ?>
<?php assets_js('jquery.iframe-transport', 'jQuery-File-Upload') ?>
<?php assets_js('jquery.fileupload', 'jQuery-File-Upload') ?>
<?php assets_css('jquery.fileupload', 'jQuery-File-Upload/css') ?>
<?php assets_css('jquery.fileupload-ui', 'jQuery-File-Upload/css') ?>

<script>
    $(function () {
        $('#file_upload').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        $('#upload_error').html(file.error);
                    } else {
                        $('#logo').attr('src', file.url);
                        $("#ent0105").val(file.name);
                    }
                    $('.progress').hide();
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            start: function (e) {
                $('#upload_error').html('');
                $('.progress').show();
            }
        });
    });
</script>
