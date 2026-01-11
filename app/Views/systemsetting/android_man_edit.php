<script>
function close_dialog2() {
    var url = base_url + folder + 'system_setting/android_man';
    layer.closeAll();
    document.location.href = url;
}
</script>
<?php assets_js('jquery.ui.widget', 'jQuery-File-Upload/vendor') ?>
<?php assets_js('jquery.iframe-transport', 'jQuery-File-Upload') ?>
<?php assets_js('jquery.fileupload', 'jQuery-File-Upload') ?>
<?php assets_css('jquery.fileupload', 'jQuery-File-Upload/css') ?>
<?php assets_css('jquery.fileupload-ui', 'jQuery-File-Upload/css') ?>
<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6">

		</div>
		<div class="col-sm-6 text-right">
			<span class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span> UPLOAD APK FILE </span>
				<input id="file_upload" type="file" name="files[]" data-url="<?= base_url('system_setting/android_man_upload_apk') ?>" multiple>
			</span>
			<button class="btn btn-default" onclick="close_dialog2()"><i class="fa fa-undo"></i> <?=lang('toolbar_close')?></button>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
	<div class="form-group">
		<div class="col-sm-8">
			<div class="progress progress-striped" style="display: none">
				<div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
			</div>
			<span id="upload_error"></span>
		</div>
	</div>
	<?= form_close() ?>
</div>

<script>
    $(function () {
        $('#file_upload').fileupload({
            dataType: 'json',
            done: function (e, data) {
				$('#upload_error').html('Done!');
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        $('#upload_error').html(file.error);
                    } else {
						$('#upload_error').html('Done!<br>Total ' + file.size + ' bytes uploaded.' + "<br><?= lang('relogin_hint') ?>" );
						
					}
                });
				$('.progress').hide();
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
				if(progress != 100) {
					$('#upload_error').html(progress + '%');
				}
            },
            start: function (e) {
                $('#upload_error').html('');
                $('.progress').show();
				$('#file_upload').attr('disabled','disabled');
				$('.btn-success').css(
					'background-color','#999999'
				);
            }
        });
    });
</script>