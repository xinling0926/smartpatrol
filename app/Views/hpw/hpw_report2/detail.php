<div class="window_wrapper">
	<?php $offset = 1; ?>
	<?php foreach ($pad01s as $pad01) : ?>
		<div class="box box-solid box-success">
			<div class="box-header with-border"><?= $offset++ . '. ' . $pad01->fmd0703 ?></div>
			<div class="box-body form-horizontal">
				<?php
				$prompt = '';
				foreach ($pad01->pad0107 as $k1 => $val) {
					if ($prompt != $val['prompt']) { ?>
						<div class="row patrol_item_group_header"><?= $val['prompt'] ?></div>
					<?php } ?>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?= $val['fmd0504']; ?></label>
						<div class="col-sm-4">
							<div class="form-control"<?php if (count($val['value']) && array_values($val['value'][0])[0] == 1) {
								echo 'style="color:red;"';
							} ?>>
								<?php echo count($val['value']) ? array_keys($val['value'][0])[0] : ''; ?></div>
						</div>
						<div class="col-sm-4">
							<div style="padding-top: 7px;">
								<?php if(isset($val['files']))
								foreach ($val['files'] as $v2) { ?>
									<a href="javascript:show_image('<?php echo $v2; ?>');"><i class="fa fa-picture-o"></i></a>
								<?php } ?>
								<?php if(isset($val['info'])){ echo $val['info'];} ?>
							</div>
						</div>
					</div>
					<?php $prompt = $val['prompt']; ?>
				<?php } ?>
			</div>
		</div>
	<?php endforeach ?>
</div>