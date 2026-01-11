<div class="row toolbar">
	<div class="col-sm-8">
		<?php if($data->pad0513==2){ ?>
		<a href="#" class="btn btn-primary" onclick="ajax_load_view('repair_to/addpad06/<?= $data->pad0501 ?>', bt_add_view);" data-id=""><i class="fa fa-plus-square-o"></i> <?=lang('add_repair_btn')?></a>
		<a href="#" class="btn btn-danger" onclick="ajax_load_view('repair_to/jiean/<?= $data->pad0501 ?>', bt_jiean);" data-id=""><i class="fa fa-check"></i> <?=lang('finish_repair_btn')?></a>
		<?php }elseif($data->pad0513==1){ ?>
		<a href="#" class="btn btn-danger" onclick="ajax_load_view('repair_to/jiedan/<?= $data->pad0501 ?>', js_jiedian);" data-id=""><i class="fa fa-check"></i> <?=lang('receive_repair_btn')?></a>
		<?php }elseif($data->pad0513==4){ ?>
			<a href="#" class="btn btn-success" onclick="js_closed(<?= $data->pad0501 ?>);" data-id=""><i class="fa fa-check"></i> <?=lang('close_repair_btn')?></a>
		<?php } ?>
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-5">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0502')?></label>
				<div class="col-sm-9">
					<div class="form-control" id="pad0502"><?= $data->pad0502 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0504')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0504 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0503')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0503 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_ent1004')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->ent1004 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_sys0103')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->sys0103 ?><?= $data->sys0104 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0509')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0509 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_cod0204')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->cod0204 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0505')?></label>
				<div class="col-sm-9">
					<div class="form-control" style="height:auto;min-height:34px;"><?= $data->pad0505 ?></div>
				</div>
			</div>
			<?php
			if(!empty($data->pad0506)){
				$image	= explode(",", $data->pad0506);
			?>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_image')?></label>
				<div class="col-sm-9">
					<div class="form-control">
						<?php foreach ($image as $v2) { ?>
							<a href="javascript:show_image('<?php echo $v2; ?>');"><i class="fa fa-picture-o"></i></a>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0511')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $pad0511_opt[$data->pad0511] ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0513')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?php if($data->pad0513==1){echo lang('v_pad0513_1');}elseif($data->pad0513==2){echo lang('v_pad0513_2');}elseif($data->pad0513==3){echo lang('v_pad0513_3');}elseif($data->pad0513==4){echo lang('v_pad0513_4');}elseif($data->pad0513==5){echo lang('v_pad0513_5');} ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0518')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0518 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0515')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0515 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0516')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0516 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0517')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0517 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0512')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?php if(isset($pad0512_opt[$data->pad0512])){ echo $pad0512_opt[$data->pad0512];} ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0514')?></label>
				<div class="col-sm-9">
					<div class="form-control" style="height:auto;min-height:34px;"><?= $data->pad0514 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0519')?></label>
				<div class="col-sm-9">
					<div class="form-control"><?= $data->pad0519 ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-7">
		<div class="box box-solid box-success">
			<div class="box-header with-border"><?=lang('label_repair_log')?></div>
			<div class="box-body">
				<table class="table table-bordered">
					<tbody>
						<tr>
							<th style="width:10px">#</th>
							<th style="width:90px"><?=lang('f_sys0104')?></th>
							<th><?=lang('f_pad0604')?></th>
							<th style="width:160px"><?=lang('f_pad0605')?></th>
							<th style="width:100px"><?=lang('f_pad0606')?></th>
						</tr>
						<?php $offset = 0;
							foreach($pad06s as $v1){
						?>
						<tr>
							<td><?php echo ++$offset; ?>.</td>
							<td><?php echo $v1->sys0103; ?><?php echo $v1->sys0104; ?></td>
							<td><?php echo $v1->pad0604; ?></td>
							<td><?php echo $v1->pad0605; ?></td>
							<td>
								<?php
								if(!empty($v1->pad0606))
								{
									$pad0606	= explode(",", $v1->pad0606);
									foreach($pad0606 as $v2){
										echo "<a href=\"javascript:show_image('{$v2}');\"><i class=\"fa fa-picture-o\"></i></a>";
									}
								}
								?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function bt_add_view(data)
{
	layer.open({
		type: 1,
		title: '<?=lang('dialog_title_add')?>',
		skin: 'layui-layer-rim', //加上边框
		area: ['800px', '540px'], //宽高
		content: data
	});
}
function js_jiedian(data)
{
	layer.open({
		type: 1,
		title: '<?=lang('dialog_title_receive')?>',
		skin: 'layui-layer-rim', //加上边框
		area: ['800px', '330px'], //宽高
		content: data
	});
}
function bt_jiean(data)
{
	layer.open({
		type: 1,
		title: '<?=lang('dialog_title_finish')?>',
		skin: 'layui-layer-rim', //加上边框
		area: ['800px', '500px'], //宽高
		content: data
	});
}
function js_closed(id)
{
	layer.confirm('<?=lang('confirm_close')?>', {
	  btn: ['<?=lang('toolbar_ok')?>','<?=lang('toolbar_close')?>']
	}, function(){
		$.ajax({
			url: 'repair_to/closed/' + id,
			type: "GET",
			beforeSend: function () {
				show_loading();
			},
			error: function (request) {
				close_loading();
				layer.alert(request.responseText, {
				  skin: 'layui-layer-molv' //样式类名
				  ,closeBtn: 0
				  ,title:'<?=lang('alert_hint_title')?>'
				});
			},
			success: function (data) {
				close_loading();
				var dataObj = json_decode(data);
				layer.alert(dataObj.info, {
					skin: 'layui-layer-molv' //样式类名
					,closeBtn: 0
					,title: '<?=lang('alert_hint_title')?>'
				}, function(){
					layer.closeAll();
					if(dataObj.status == 'success')
					{
						close_detail();
						query();
					}
				});
			}
		});
	});
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