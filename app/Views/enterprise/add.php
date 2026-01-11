<div class="row toolbar">
    <div class="col-sm-4 col-sm-offset-8 text-right">
        <a href="#" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> <?= lang('toolbar_save') ?></a>
        <a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> <?= lang('toolbar_cancel') ?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
    </div>
	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><?= lang('ent01_heading') ?></h3>
            </div>
            <div class="box-body">
                <input type="hidden" id="ent0105" name="ent0105" value=""/>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_ent0103') ?></label>
                    <div class="col-sm-8"><?= form_text_field('ent0103') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_ent0102') ?></label>
                    <div class="col-sm-8"><?= form_text_field('ent0102') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_ent0105') ?></label>
                    <div class="col-sm-8">
						<span class="btn btn-success fileinput-button">
							<i class="glyphicon glyphicon-plus"></i>
							<span><?= lang('upload_logo') ?></span>
							<input id="file_upload" name="files[]" data-url="enterprise/upload_logo" multiple="" type="file">
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
                                <input type="radio" name="ent0104" id="ent01040" value="0">
								<?= lang('v_ent0104_0') ?>
                            </label>
                            <label>
                                <input type="radio" name="ent0104" id="ent01041" value="1" checked>
								<?= lang('v_ent0104_1') ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><?= lang('sys01_heading') ?></h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_sys0102') ?></label>
                    <div class="col-sm-8"><?= form_text_field('sys0102') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_user_name') ?></label>
                    <div class="col-sm-4"><?= form_text_field('sys0103') ?></div>
                    <div class="col-sm-4"><?= form_text_field('sys0104') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_sys0105') ?></label>
                    <div class="col-sm-8"><?= form_password_field('sys0105') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_sys0105_2') ?></label>
                    <div class="col-sm-8"><?= form_password_field('sys0105_2') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?= lang('f_sys0107') ?></label>
                    <div class="col-sm-8"><?= form_text_field('sys0107') ?></div>
                </div>
                <div class="col-sm-8 col-sm-offset-4">
					<?= form_checkbox_field('add_me', 1, lang('add_to_admin')) ?>
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
