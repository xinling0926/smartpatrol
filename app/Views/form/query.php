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
				<th><?=lang('f_common_action')?></th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><?= $d->ent1004 ?></td>
					<td class="a" onclick='d(<?= $d->fmd0101 ?>,"<?= $d->fmd0103 ?>")'><?= $d->fmd0103 ?></td>
					<td class="a" onclick='d(<?= $d->fmd0101 ?>,"<?= $d->fmd0104 ?>")'><?= $d->fmd0104 ?></td>
					<td><?= $d->fmd0107 ?></td>
					<td><?= $fmd0105_opt[$d->fmd0105] ?></td>
					<td><?= $fmd0108_opt[$d->fmd0108] ?></td>
					<td><?php
						switch ($d->fmd0108) {
							case 0:
								echo '<a href="#" class="btn btn-xs btn-primary" onclick="q(' . $d->fmd0101 . ')">';
								echo '<i class="fa fa-play"></i>'.lang('action_btn_start').'</a> ';
								echo '<a href="#" class="btn btn-xs btn-danger" onclick="del(this)" data-id="' . $d->fmd0101 . " data-cuid=" .
									csrf_hash() . '">';
								echo '<i class="fa fa-trash-o"></i>'.lang('toolbar_del').'</a>';
								break;
							case 2:
								echo '<a href="#" class="btn btn-xs btn-danger" onclick="a(' . $d->fmd0101 . ',4)">';
								echo '<i class="fa fa-stop"></i>'.lang('action_btn_stop').'</a>';
								break;
							case 4:
								echo '<a href="#" class="btn btn-xs btn-primary" onclick="a(' . $d->fmd0101 . ',2)">';
								echo '<i class="fa fa-play"></i>'.lang('action_btn_start').'</a>';
								break;
						} ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>

