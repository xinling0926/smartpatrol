<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save_repair()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="close_tab('sendto');"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12"><div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div></div>
	<?php echo form_open('',array('id'=>'edit_form',"class"=>"form-horizontal")); ?>
		<input type="hidden" name="pad0520" value="<?php echo $pad0301; ?>" />
		<input type="hidden" name="pad0506" value="<?php echo $pad0506; ?>" />
		<div class="col-md-8">
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0502')?></label>
				<div class="col-sm-8"><?= form_text_field('pad0502', $data, sprintf('%s-%s',$pad03->ent1003, date('YmdHi', time())), '', true) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0504')?></label>
				<div class="col-sm-8"><?= form_text_field('pad0504', $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0503')?></label>
				<div class="col-sm-8"><?= form_text_field('pad0503', $data, $pad03->pad0305) ?></div>
			</div>
			<div class="form-group hide">
				<label class="col-sm-2 control-label"><?=lang('label_ent1004')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0507', $ent10s, $current_user->sys0110, '', array('onchange' => 'select_sys01(this.value);')) ?></div>
			</div>
			<div class="form-group hide">
				<label class="col-sm-2 control-label"><?=lang('label_sys0103')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0508', $sys01s, $current_user->sys0101) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_cod0204')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0510', $pad0510_opt) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0505')?></label>
				<div class="col-sm-8"><?= form_textarea_field('pad0505', null, $pad03->pad0505) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0511')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0511', $pad0511_opt) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_pad0512')?></label>
				<div class="col-sm-8"><?= form_dropdown_input('pad0512', $pad0512_opt) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_image')?></label>
				<div class="col-sm-8" style="margin-top:6px;">
					<?php foreach($pad04s as $v1){ ?>
						<a href="javascript:show_image('<?php echo $v1->pad0403; ?>');"><i class="fa fa-picture-o"></i></a>
					<?php } ?>
				</div>
			</div>
		</div>
	<?= form_close() ?>
</div>
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
                close_tab('sendto');
				detail('<?php echo $pad03->pad0301; ?>', '<?php echo $pad03->pad0303; ?>');
				query();
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
</script>