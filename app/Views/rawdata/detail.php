<div class="row toolbar">
	<div class="col-sm-8">
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_ent1004')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->ent1004 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_dev0104')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->dev0104 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_fmd0804')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->fmd0804 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_fmd0204')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->fmd0204 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_fmd0908')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->fmd0908 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_fmd0703')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->fmd0703 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0109')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->pad0109 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_sys0103')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->sys0103 ?><?= $data->sys0104 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_juli')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->juli ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_pad0112')?></label>
				<div class="col-sm-7">
					<div class="form-control"><?= $data->pad0112 ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-solid box-success">
			<div class="box-header with-border"><?=lang('data_box_result_title')?></div>
			<div class="box-body form-horizontal">
				<?php
				$prompt = '';
				foreach ($data->pad0107 as $k1 => $val) {
					if ($prompt != $val['prompt']) { ?>
						<div class="row patrol_item_group_header"><?= $val['prompt'] ?></div>
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?= $val['fmd0504']; ?></label>
						<div class="col-sm-4">
							<div class="form-control"<?php if (count($val['value']) && array_values($val['value'][0])[0] == 1) { echo 'style="color:red;"';} ?>>
								<?php echo count($val['value']) ? array_keys($val['value'][0])[0] : ''; ?></div>
						</div>
						<div class="col-sm-4">
							<div style="padding-top: 7px;">
								<?php if(isset($val['files']))foreach ($val['files'] as $v2) { ?>
									<a href="javascript:show_image('<?php echo $v2; ?>');"><i class="fa fa-picture-o"></i></a>
								<?php } ?>
								<?php if(isset($val['info'])){ ?><textarea cols="18" rows="2"><?= $val['info'] ?></textarea><?php } ?>
							</div>
						</div>					
					</div>
					<?php $prompt = $val['prompt']; ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-solid box-default">
			<div class="box-header with-border"><?=lang('f_comments')?></div>
			<div class="box-body form-horizontal">
				<button class="btn btn-default" onclick="show_rawdata_add_comment('<?= $param ?>')"><i class="fa fa-fw fa-plus-square-o"></i> <?=lang('f_add_comments')?></button>
				<button class="btn btn-default" onclick="show_rawdata_auto_comment_edit('<?= $param ?>')"><i class="fa fa-fw fa-sticky-note-o"></i> <?=lang('f_man_auto_comments')?></button>
				<ui class="list-group"><?php foreach($commentRaws as $comment) : ?>
					<li class="list-group-item"><?= $comment ?></li>
				<?php endforeach; ?></ui>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function show_image(img) {
		var html = "<p style='margin-top:20px;margin-left:10px;margin-bottom:10px;'><center><a href=\"" + img + "\" target=\"_blank\"><img src=\"" + img + "\" style=\"max-width:500px;max-height:440px;\"/></a></center></p><p style='margin:10px;'>";
		layer.open({
			type: 1,
			title: '<?=lang('dialog_title_img')?>',
			skin: 'layui-layer-demo', //样式类名
			closeBtn: 1, //不显示关闭按钮
			shift: 2,
			shadeClose: true, //开启遮罩关闭
			area: ['600px', '520px'],
			content: html
		});
	}
</script>