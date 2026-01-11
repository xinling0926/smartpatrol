<?php assets_js('jquery.ui.widget', 'jQuery-File-Upload/vendor') ?>
<?php assets_js('jquery.iframe-transport', 'jQuery-File-Upload') ?>
<?php assets_js('jquery.fileupload', 'jQuery-File-Upload') ?>
<?php assets_css('jquery.fileupload', 'jQuery-File-Upload/css') ?>
<?php assets_css('jquery.fileupload-ui', 'jQuery-File-Upload/css') ?>

<div class="row toolbar">
    <div class="col-sm-6 col-sm-offset-6 text-right">
        <a href="#" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></a>
        <a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-12"><div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div></div>
    <?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
    <?php if ($data) echo form_hidden('sys0101', $data->sys0101) ?>
    <?php if ($data) echo form_hidden('sys0201', $data->sys0201) ?>
    <div class="col-md-6">
        <?php if ($identity_column == 'sys0102'): ?>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0102')?></label>
                <div class="col-sm-8"><?= form_text_field('sys0102', $data, '', '', true) ?></div>
            </div>
        <?php endif ?>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_users_name')?></label>
            <div class="col-sm-4"><?= form_text_field('sys0103', $data) ?></div>
            <div class="col-sm-4"><?= form_text_field('sys0104', $data) ?></div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0110')?></label>
            <div class="col-sm-8"><?= form_dropdown_field('sys0110', $dept, $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0119')?></label>
            <div class="col-sm-8"><?= form_dropdown_field('sys0119',$jobtitle, $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0107')?></label>
            <div class="col-sm-8"><?= form_text_field('sys0107', $data, '', '', $identity_column == 'sys0107') ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0111')?></label>
            <div class="col-sm-8"><?= form_text_field('sys0111', $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0204')?></label>
            <div class="col-sm-8"><?= form_dropdown_field('sys0204', $role, $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0108')?></label>
            <div class="col-sm-8">
                <div class="radio">
                    <label>
                        <input type="radio" name="sys0205" id="sys0205" value="0"<?php if ($data && $data->sys0205 == 0) echo " checked" ?>>
                        <?=lang('Auth.v_sys0108_0')?>
                    </label>
                    <label>
                        <input type="radio" name="sys0205" id="sys0205" value="1"<?php if ($data == null || $data->sys0205 == 1) echo " checked" ?>>
	                    <?=lang('Auth.v_sys0108_1')?>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">AD認證</label>
            <div class="col-sm-8">
                <div class="radio">
                    <label>
                        <input type="radio" name="sys0121" id="sys0121" value="0"<?php if ($data == null || $data->sys0121 == 0) echo " checked" ?>>
	                    <?=lang('Auth.v_sys0108_0')?>
                    </label>
                    <label>
                        <input type="radio" name="sys0121" id="sys0121" value="1"<?php if ($data && $data->sys0121 == 1) echo " checked" ?>>
	                    <?=lang('Auth.v_sys0108_1')?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0120')?></label>
            <div class="col-sm-8">
                <?php echo form_hidden('sys0120', '') ?>
                <img id="img_sys0120"
                     src="<?= ($data && $data->sys0120) ? base_url("data/sign/" . $data->sys0101 . '/' . $data->sys0120) : base_url("assets/img/no_sign.png") ?>"
                     class="form-control" alt="<?=lang('Auth.f_sys0120')?>" style="width:100%;height:auto">
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span><?=lang('Auth.edit_sys0120_btn')?></span>
                    <input id="file_upload" type="file" name="files[]" data-url="<?= base_url('account/upload_sys0120') ?>" multiple>
                </span>
                <span id="upload_error"><?=lang('Auth.edit_sys0120_hint')?></span>
                <div class="progress progress-striped" style="display: none">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0105')?></label>
            <div class="col-sm-8"><?= form_password_input('sys0105','','',($data)?'placeholder='.lang('Auth.edit_sys0105_hint'):'') ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?=lang('Auth.f_users_password_confirm')?></label>
            <div class="col-sm-8"><?= form_password_input('sys0105_2','','',($data)?'placeholder='.lang('Auth.edit_sys0105_hint'):'') ?></div>
        </div>
    </div>
    </form>
</div>

<script>
    $(function () {
        $('#file_upload').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        $('#upload_error').html(file.error);
                    } else {
                        $('input[name="sys0120"]').val(file.name);
                        $('#img_sys0120').attr('src', file.url);
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
