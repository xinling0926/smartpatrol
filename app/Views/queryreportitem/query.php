<div class="box box-primary" id="report_content">
	<div class="box-header">
		<h3 class="box-title"><?=lang('Globe.box_result')?></h3>
	</div>
	<div class="box-body scroll">
		<table class="table table-striped table-hover">
			<tbody>
			<tr>
				<?php
				if ($fmd02s) {
					$field_count = count($fmd05s);
					echo "<th rowspan=\"2\">".lang('QueryReportItem.f_common_no')."</th>";
					echo "<th rowspan=\"2\">".lang('QueryReportItem.f_date')."</th>";
					foreach ($fmd03s as $fmd03) {
						echo "<th rowspan=\"2\">{$fmd03->fmd0304}</th>";
					}
					if ($field_count == 1) {
						$colspan = " colspan=\"" . count($fmd02s) . "\"";
						foreach ($fmd05s as $d) {
							echo "<th{$colspan}>{$d->fmd0504}</th>";
						}
						echo '</tr><tr>';
						foreach ($fmd02s as $fmd02) {
							echo "<th>{$fmd02->fmd0204}</th>";
						}
					} else {
						$colspan = " colspan=\"{$field_count}\"";
						foreach ($fmd02s as $fmd02) {
							echo "<th{$colspan}>{$fmd02->fmd0204}</th>";
						}
						echo '</tr><tr>';
						foreach ($fmd02s as $fmd02) {
							foreach ($fmd05s as $d) {
								echo "<th>{$d->fmd0504}</th>";
							}
						}
					}
				} else {
					echo "<th>".lang('QueryReportItem.f_common_no')."</th>";
					echo "<th>".lang('QueryReportItem.f_date')."</th>";
					foreach ($fmd03s as $d) {
						echo "<th>{$d->fmd0304}</th>";
					}
					foreach ($fmd05s as $d) {
						echo "<th>{$d->fmd0504}</th>";
					}
				} ?>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?= ++$offset ?></td>
					<td><?= $d->date ?></td>
					<?php
					foreach ($fmd03s as $fmd03) {
						$fn = "item{$fmd03->fmd0303}_name";
						echo "<td>{$d->$fn}</td>";
					}
					if ($fmd02s) {
						foreach ($fmd02s as $fmd02) {
							foreach ($fmd05s as $fmd05) {
								$fn_data = 'data' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
								$fn_err = 'err' . $fmd02->fmd0203 . '_' . $fmd05->fmd0503;
								if ($d->$fn_err == 1) {
									echo '<td class="bg-red">';
								} else if ($d->$fn_err == 2) {
									echo '<td class="bg-gray">';
								} else {
									echo '<td>';
								}
								echo $d->$fn_data;
								echo '</td>';
							}
						}
					} else {
						foreach ($fmd05s as $fmd05) {
							$fn_data = 'data1_' . $fmd05->fmd0503;
							$fn_err = 'err1_' . $fmd05->fmd0503;
							if ($d->$fn_err == 1) {
								echo '<td class="bg-red">';
							} else if ($d->$fn_err == 2) {
								echo '<td class="bg-gray">';
							} else {
								echo '<td>';
							}
							echo $d->$fn_data;
							echo '</td>';
						}
					} ?>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<?= view('layout/data_table_footer_box', get_defined_vars()) ?>
</div>
