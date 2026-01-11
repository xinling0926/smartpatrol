<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
		<tbody>
		<tr>
			<th><?=lang('f_rol0103')?></th>
			<th><?=lang('f_rol0104')?></th>
			<th><?=lang('f_rol0106')?></th>
		</tr>
		<?php foreach ($data as $d) : ?>
			<tr>
				<td><a href="#" onclick='detail(<?=$d->rol0101 ?>,"<?= $d->rol0103 ?>")'><?= $d->rol0103 ?></a></td>
				<td><?= $d->rol0104 ?></td>
				<td class="<?php if ($d->rol0106) { echo "text-green"; } else { echo "text-red"; } ?>"><?= $rol0106_option[$d->rol0106] ?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
