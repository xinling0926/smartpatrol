<div class="">
	<?php echo form_open('',array('id'=>'edit_form',"class"=>"form-horizontal")); ?>
		<input type="hidden" id="pad0501" name="pad0501" class="form-control" value="<?php echo $pad0501; ?>" />
		<div class="col-md-12">
			<div class="box" style="margin-top:10px;">
				<div class="form-group" style="margin-top:10px;">
					<label class="col-sm-2 control-label"><?=lang('label_pad0517')?></label>
					<div class="col-sm-8"><?= form_text_field('pad0517', null) ?></div>
				</div>
				<div class="form-group" style="margin-top:10px;">
					<label class="col-sm-2 control-label"><?=lang('label_pad0512')?></label>
					<div class="col-sm-8"><?= form_dropdown_input('pad0512', $pad0512_opt) ?></div>
				</div>
				<div class="form-group" style="margin-top:10px;">
					<label class="col-sm-2 control-label"><?=lang('label_pad0514')?></label>
					<div class="col-sm-8"><?= form_textarea_field('pad0514', null) ?></div>
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
<script type="text/javascript">
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
			url: 'repair_to/save_jiean/' + <?php echo $pad0501; ?>,
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
							query();
					});
				}
			}
		});
	}
}
</script>