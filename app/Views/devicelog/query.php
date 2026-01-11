<style>
	.dev0206 {
		word-break: break-all;
	}
</style>
<div class="box box-warning">
	<div class="box-header with-border">
		<h3 class="box-title"><?= lang('box_result') ?></h3>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12" style="overflow: auto">
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th style="width: 150px"><?=lang('f_dev0207')?></th>
						<th style="width: 150px"><?=lang('f_dev0203')?></th>
						<th style="width: 75px"><?=lang('f_dev0104')?></th>
						<th style="width: 75px"><?=lang('f_dev0204')?></th>
						<th><?=lang('f_dev0206')?></th>
					</tr>
					<?php foreach ($data as $d) : ?>
						<tr>
							<td><?= $d->dev0207 ?></td>
							<td><?= $d->dev0203 ?></td>
							<td><?= $d->dev0104 ?></td>
							<td><?php if($d->dev0204 == 3){echo '<font color="red">'.lang('v_dev0204_3').'</font>';}elseif($d->dev0204==2){echo lang('v_dev0204_2');}else{echo lang('v_dev0204_1');}
							?></td>
							<td class="dev0206"><?= str_replace(["\r\n","\r","\n"],'<br>', $d->dev0206) ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?= view('layout/data_table_footer_box', get_defined_vars()) ?>
</div>
