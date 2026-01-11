<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <i class="fa fa-key"></i><h3 class="box-title"><?=lang('change_password_heading')?></h3>
            </div>
            <?php echo form_open('', array('id' => 'change_password_form', "class" => "form-horizontal")); ?>
            <div class="box-body">
                <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=lang('change_password_old_password_label')?></label>
                    <div class="col-sm-8"><?= form_password_input('password') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=lang('change_password_new_password_label')?></label>
                    <div class="col-sm-8"><?= form_password_input('sys0105') ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=lang('change_password_new_password_confirm_label')?></label>
                    <div class="col-sm-8"><?= form_password_input('sys0105_2') ?></div>
                </div>
            </div>
            <div class="box-footer">
                <a href='javascript:change_password_form.reset();' class="btn btn-default"><i class="fa fa-eraser"></i> <?= lang('toolbar_reset')
                    ?></a>
                <a href="#" class="btn btn-primary pull-right" onclick="change_password();"><i class="fa fa-key"></i> <?=lang('change_password_submit_btn')?></a>
            </div>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <i class="fa fa-key"></i><h3 class="box-title"><?=lang('upload_avatar_heading')?></h3>
            </div>
            <div class="box-body">
                <?php echo form_open('', ['id' => 'avatar_form']); ?>
                <img id="img_sys0117" src="<?= base_url("data/avatar/" . $user->avatar) ?>" alt="<?=lang('f_sys0117')?>" style="max-width: 100%;">
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span><?=lang('upload_avatar_btn')?></span>
                    <input id="file_upload" type="file" name="files[]" data-url="<?= base_url('account/upload_avatar') ?>" multiple>
                </span>
                <span id="upload_error"><?=lang('upload_avatar_hint')?></span>
                <div class="progress progress-striped" style="display: none">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function change_password(){
        $("#message").html('');
        $("#message").hide();
        var form = 'change_password_form';
        var url = base_url + folder + controller + '/change_password';
        ajax_post_view(url,
            $('#' + form).serialize(),
            function (data) {
                var obj = json_decode(data);
                if (obj.message=='OK'){
                    $("#message").removeClass('alert-danger');
                    $("#message").addClass('alert-success');
                    $("#message").html('');
                    $("#message").append('<h4><i class="icon fa fa-check"></i> <?=lang('change_password_success_heading')?></h4>');
                } else {
                    $("#message").removeClass('alert-success');
                    $("#message").addClass('alert-danger');
                    $("#message").html('');
                    $("#message").append("<h4><i class=\"icon fa fa-ban\"></i> <?=lang('change_password_fail_heading')?></h4>");
                }
                $("#message").append(obj.data);
                $("#message").show();
            }
        );
    }
</script>

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
                        $('#img_sys0117').attr('src', file.url);
                        $(".user img").attr('src', file.url);
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
