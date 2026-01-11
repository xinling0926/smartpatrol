<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_common_no')?></th>
				<th><?=lang('f_q_ent1004')?></th>
				<th><?=lang('f_q_fmd0103')?></th>
				<th><?=lang('f_q_fmd0104')?></th>
				<th><?=lang('f_q_fmd0107')?></th>
				<th><?=lang('f_q_fmd0105')?></th>
				<th><?=lang('f_q_fmd0108')?></th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><?= $dep_opt[$d->fmd0102] ?></td>
					<td class="a" onclick='detail(<?= $d->fmd0101 ?>,"<?= $d->fmd0103 ?>")'><?= $d->fmd0103 ?></td>
					<td class="a" onclick='detail(<?= $d->fmd0101 ?>,"<?= $d->fmd0104 ?>")'><?= $d->fmd0104 ?></td>
					<td class="a" onclick="showFromHistory(<?= $d->fmd0101 ?>)"><?= $d->fmd0107 ?></td>
					<td><?= $fmd0105_opt[$d->fmd0105] ?></td>
					<td><?= $fmd0108_opt[$d->fmd0108] ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>

