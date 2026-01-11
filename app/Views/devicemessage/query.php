<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_dev0104')?></th>
				<th><?=lang('f_sys0104')?></th>
				<th><?=lang('f_dev0304')?></th>
				<th><?=lang('f_dev0305')?></th>
				<th><?=lang('f_dev03z2')?></th>
				<th><?=lang('f_dev0308')?></th>
				<th><?=lang('f_dev0309')?></th>
			</tr>
			<?php if(isset($dev03s))foreach ($dev03s as $d) : ?>
				<tr>
					<td><?php if($d->dev0302==0){echo lang('all_device');}else{echo $d->dev0104;} ?></td>
					<td><?php if($d->dev0303==0){echo lang('all_user');}else{echo $d->sys0103.$d->sys0104;} ?></td>
					<td><a href="javascript:;" onclick='detail(<?= $d->dev0301 ?>,"<?= $d->dev0304 ?>")'><?= $d->dev0304 ?></a></td>
					<td><?= $d->dev0305 ?></td>
					<td><?= $d->dev03z2 ?></td>
					<td><?= $d->dev0308 ?></td>
					<td><?= $d->dev0309 ?></td>
					<td></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>

