<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_ent1004')?></th>
				<th><?=lang('f_dev0104')?></th>
				<th><?=lang('f_fmd0804')?></th>
				<th><?=lang('f_fmd0204')?></th>
				<th><?=lang('f_fmd0908')?></th>
				<th><?=lang('f_fmd0703')?></th>
				<th><?=lang('f_sys0104')?></th>
				<th style="width:150px;text-align:center;"><?=lang('f_pad0109')?></th>
				<th style="width:150px;text-align:center;"><?=lang('f_pad0112')?></th>
			</tr>
			<?php if(isset($data))
			foreach ($data as $d) : ?>
				<tr>
					<td><?= $d->ent1004 ?></td>
					<td><?= $d->dev0104 ?></td>
					<td><?= $d->fmd0804 ?></td>
					<td><?= $d->fmd0204 ?></td>
					<td><?= $d->fmd0908 ?></td>
					<td><a href="javascript:detail('<?= $d->pad0101 ?>','<?= $d->fmd0703 ?>');"><?= $d->fmd0703 ?></a></td>
					<td><?= $d->sys0103 ?><?= $d->sys0104 ?></td>
					<td><?= $d->pad0109 ?></td>
					<td><?= $d->pad0112 ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>

