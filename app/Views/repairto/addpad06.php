<div class="">
	<?php echo form_open('',array('id'=>'edit_form',"class"=>"form-horizontal")); ?>
		<input type="hidden" id="pad0501" name="pad0501" class="form-control" value="<?php echo $pad0501; ?>" />
		<input type="hidden" id="pad0606" name="pad0606" class="form-control" value="" />
		<div class="col-md-12">
			<div class="box" style="margin-top:10px;">
				<div class="form-group" style="margin-top:10px;">
					<label class="col-sm-2 control-label"><?=lang('label_pad0507')?></label>
					<div class="col-sm-9"><?= form_dropdown_input('pad0507', $ent10s, $current_user->sys0110, '', array('onchange' => 'select_sys01(this.value);')) ?></div>
				</div>
				<div class="form-group" style="margin-top:10px;">
					<label class="col-sm-2 control-label"><?=lang('label_pad0508')?></label>
					<div class="col-sm-9"><?= form_dropdown_input('pad0508', $sys01s) ?></div>
				</div>
				<div class="form-group" style="margin-top:10px;">
					<label class="col-sm-2 control-label"><?=lang('label_pad0505')?></label>
					<div class="col-sm-9"><?= form_textarea_field('pad0505', null) ?></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><?=lang('label_image')?></label>
					<div class="col-sm-9" style="margin-top:6px;">
						<span class="btn btn-success fileinput-button">
							<i class="glyphicon glyphicon-plus"></i>
							<span><?=lang('update_image_span')?></span>
							<input id="file_upload" name="files" data-url="repair_to/upload" multiple="" type="file">
						</span>
						<span id="upload_error"><?=lang('update_image_hint')?></span>
						<div class="progress progress-striped" style="display: none">
							<div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
						</div>
					</div>
				</div>
				<div class="box-body">
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	<?=form_close()?>
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<div class="">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="javascript:;" class="btn btn-success" onclick="view_edit_submit();"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>&nbsp;
		<a href="javascript:;" class="btn btn-default" onclick="layer.closeAll();"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>&nbsp;
	</div>
</div>
<?php assets_js('jquery.ui.widget', 'jQuery-File-Upload/vendor') ?>
<?php assets_js('jquery.iframe-transport', 'jQuery-File-Upload') ?>
<?php assets_js('jquery.fileupload', 'jQuery-File-Upload') ?>
<?php assets_css('jquery.fileupload', 'jQuery-File-Upload/css') ?>
<?php assets_css('jquery.fileupload-ui', 'jQuery-File-Upload/css') ?>
<script type="text/javascript">
    $(function () {
        $('#file_upload').fileupload({
            dataType: 'json',
            done: function (e, data) {
				if(data.result.status == 'success') {
					$("#pad0606").val(data.result.info);
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
function select_sys01(id)
{
	$("#pad0508").empty();
	$.ajax({
        url: 'repair_to/select_sys01/' + id,
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
function view_edit_submit()
{
	if($("#pad0508").val() == "")
	{
		$("#message").text("<?=lang('rules_pad0508_hint')?>").show();
	}
	else if($("#pad0505").val() == "")
	{
		$("#message").text("<?=lang('rules_pad0505_hint')?>").show();
	}
	else
	{
		$.ajax({
			url: 'repair_to/save_addpad06/' + <?php echo $pad0501; ?>,
			data: $('#edit_form').serialize(),
			type: "POST",
			beforeSend: function () {
				$("#message").text("<?=lang('rules_load_hint')?>").show();
			},
			error: function (request) {
				$("#message").text(request.responseText).show();
			},
			success: function (data) {
				var dataObj = json_decode(data);
				$("#message").text(dataObj.info).show();
				if(dataObj.status == "success")
				{
					layer.closeAll();
					layer.alert(dataObj.info, {
						title: '<?=lang('alert_hint_title')?>',
						skin: 'layui-layer-molv' //样式类名
						,closeBtn: 0
					}, function(){
							layer.closeAll();
							detail('<?php echo $pad0501; ?>', $("#pad0502").text());
					});
				}
			}
		});
	}
}
</script>