<?php

foreach ($data as $item) {
	$offset = 0;
	$fmd02_count = count($item->fmd02s);
	if ($fmd02_count) {
		$rowspan = ' rowspan="2"';
		$colspan = ' colspan="' . ($fmd02_count + 1) . '"';
	} else {
		$rowspan = '';
		$colspan = '';
	}
	?>
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title"><?= $item->fmd01->fmd0104 ?></h3>
		</div>
		<div class="box-body">
			<?php if ($item->iso) : ?>
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th<?= $rowspan ?>><?=lang('Globe.f_common_no')?></th>
						<th<?= $rowspan ?>><?=lang('Missing.f_date')?></th>
						<th<?= $colspan ?>><?=lang('Missing.f_error_count')?></th>
						<th<?= $colspan ?>><?=lang('Missing.f_miss_count')?></th>
					</tr>
					<?php if ($fmd02_count): ?>
						<tr>
							<?php foreach ($item->fmd02s as $fmd02) {
								echo "<th>{$fmd02->fmd0204}</th>";
							} ?>
							<th><?=lang('Missing.f_all_count')?></th>
							<?php foreach ($item->fmd02s as $fmd02) {
								echo "<th>{$fmd02->fmd0204}</th>";
							} ?>
							<th><?=lang('Missing.f_all_count')?></th>
						</tr>
					<?php endif ?>
					<?php foreach ($item->iso as $d) : ?>
						<tr>
							<td><?php echo ++$offset; ?></td>
							<td><?= anchor('query_report/index/' . $item->fmd01->fmd0101 . '-' . $d->id, $d->date) ?></td>
							<?php foreach ($item->fmd02s as $fmd02) {
								$fn = 'error' . $fmd02->fmd0203;
								echo "<th>{$d->$fn}</th>";
							} ?>
							<td><?= $d->error_count ?></td>
							<?php foreach ($item->fmd02s as $fmd02) {
								$fn = 'miss' . $fmd02->fmd0203;
								echo "<th>{$d->$fn}</th>";
							} ?>
							<td><?= $d->miss_count ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			<?php else : ?>
				<span class="text-red"><?=lang('Missing.not_data_hint')?></span>
			<?php endif ?>
		</div>
	</div>
<?php } ?>
