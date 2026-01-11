<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save();"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="cancel_edit();"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-8">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
	<?php echo form_open_multipart('repair_from/sendto_save',array('id'=>'edit_form',"class"=>"form-horizontal")); ?>
		<?php if ($data) echo form_hidden('pad0501',$data->pad0501)?>
		<input type="hidden" id="pad0520" name="pad0520" value="0" />
		<input type="hidden" id="pad0506" name="pad0506" value="<?php echo isset($data) ? $data->pad0506 : ''; ?>" />
		<div class="col-md-8">
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0502')?></label>
				<div class="col-sm-8"><?= form_text_field('pad0502', $data, isset($data) ? $data->pad0502 : $pad0502) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0504')?></label>
				<div class="col-sm-8"><?= form_text_field('pad0504', $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0503')?></label>
				<div class="col-sm-8"><?= form_text_field('pad0503', $data) ?></div>
			</div>
			<div class="form-group hide">
				<label class="col-sm-2 control-label"><?=lang('label_ent1004')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0507', $ent10s, isset($data) ? $data->pad0507 : $current_user->sys0110, '', array('onchange' => 'select_sys01(this.value);')) ?></div>
			</div>
			<div class="form-group hide">
				<label class="col-sm-2 control-label"><?=lang('label_sys0103')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0508', $sys01s, isset($data) ? $data->pad0508 : $current_user->sys0101) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_cod0204')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0510', $pad0510_opt, isset($data) ? $data->pad0510 : '') ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0505')?></label>
				<div class="col-sm-8"><?= form_textarea_field('pad0505', $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0511')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0511', $pad0511_opt, isset($data) ? $data->pad0511 : '') ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0512')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0512', $pad0512_opt, isset($data) ? $data->pad0512 : '') ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_image')?></label>
				<div class="col-sm-8" style="margin-top:6px;">
					<span class="btn btn-success fileinput-button">
						<i class="glyphicon glyphicon-plus"></i>
						<span><?=lang('update_image_span')?></span>
						<input id="file_upload" name="files" data-url="repair_from/upload" multiple="" type="file">
					</span>
					<span id="upload_error"><?=lang('update_image_hint')?></span>
					<div class="progress progress-striped" style="display: none">
						<div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
					</div>
					<br/>
					<?php foreach($pad0506s as $v1){ ?>
						<a href="javascript:show_image('<?php echo $v1; ?>');"><i class="fa fa-picture-o"></i></a>
					<?php } ?>
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
<script type="text/javascript">
function select_sys01(id)
{
	$("#pad0508").empty();
	$.ajax({
        url: 'repair_from/select_sys01/' + id,
        beforeSend: function () {
            show_loading();
        },
        error: function (request) {
            close_loading();
            show_error(request.responseText);
        },
        success: function (data) {
            close_loading();
			var dataObj = json_decode(data);
			$.each(dataObj, function(index, item){
				$("#pad0508").append('<option value="' + item.sys0101 + '">' + item.sys0103 + '</option>');
			});
        }
    });
}
function save_repair()
{
	ajax_post_view('repair_from/sendto_save', $("#edit_form").serialize(),
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_tab('detail');
            } else {
                $("#message").html(dataObj.message);
                $("#message").show();
            }
        }
    );
}
	function show_image(img) {
		var html = "<p style='margin-top:20px;margin-left:10px;margin-bottom:10px;'><center><a href=\"" + img + "\" target=\"_blank\"><img src=\"" + img + "\" style=\"max-width:500px;max-height:440px;\"/></a></center></p><p style='margin:10px;'>";
		layer.open({
			type: 1,
			title: '<?=lang('dialog_image_title')?>',
			skin: 'layui-layer-demo', //样式类名
			closeBtn: 1, //不显示关闭按钮
			shift: 2,
			shadeClose: true, //开启遮罩关闭
			area: ['600px', '520px'],
			content: html
		});
	}
    $(function () {
        $('#file_upload').fileupload({
            dataType: 'json',
            done: function (e, data) {
				if(data.result.status == 'success') {
					$("#pad0506").val(data.result.info);
					$('#upload_error').html("<a href=\"javascript:;\" onclick=\"show_image('" + data.result.info + "');\">" + data.result.info + "</a>");
				} else {
					$('#upload_error').html(data.result.info);
				}
                $('.progress').hide();
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