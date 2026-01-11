<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('no')?></th>
				<th><?=lang('f_ent1004')?></th>
				<th><?=lang('f_fmd0103')?></th>
				<th><?=lang('f_fmd0104')?></th>
				<th><?=lang('f_fmd0107')?></th>
				<th><?=lang('f_fmd0105')?></th>
				<th><?=lang('f_fmd2003')?></th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><?= $d->ent1004 ?></td>
					<td class="a" onclick='detail(<?= $d->fmd0101 ?>,"<?= $d->fmd0103 ?>")'><?= $d->fmd0103 ?></td>
					<td class="a" onclick='detail(<?= $d->fmd0101 ?>,"<?= $d->fmd0104 ?>")'><?= $d->fmd0104 ?></td>
					<td><?= $d->fmd0107 ?></td>
					<td><?= $fmd0105_opt[$d->fmd0105] ?></td>
					<td><?php if (is_null($d->fmd20)) {
							echo lang('no_setting');
						} else {
							echo $fmd2003_opt[$d->fmd20->fmd2003];
						} ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>

