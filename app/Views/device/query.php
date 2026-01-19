<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_common_no')?></th>
				<th><?=lang('f_dev0104')?></th>
				<th><?=lang('f_dev0105')?></th>
				<th>Serial</th>
				<th><?=lang('f_ent1004')?></th>
				<th><?=lang('f_dev0111')?></th>
				<th><?=lang('f_dev0110')?></th>
				<th><?=lang('f_dev0106')?></th>
				<th><?=lang('f_dev01z2')?></th>
				<th><?=lang('f_dev0109')?></th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<?php
					$serial = '';
					if (!empty($d->dev0113) && ($dev0113s = json_decode($d->dev0113))) {
						$serial = $dev0113s->serial ?? '';
					}
				?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><a href="#" onclick='detail(<?=$d->dev0101 ?>,"<?= $d->dev0104 ?>")'><?= $d->dev0104 ?></a></td>
					<td><?= $d->dev0105 ?></td>
					<td><?= $serial ?></td>
					<td><?= $d->ent1004 ?></td>
					<td><?= $d->dev0111 ?></td>
					<td><?= $d->dev0110 ?></td>
					<td><?= $dev0106_opt[$d->dev0106] ?></td>
					<td><?= $d->dev01z2 ?></td>
					<td><?= $d->dev0109 ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
