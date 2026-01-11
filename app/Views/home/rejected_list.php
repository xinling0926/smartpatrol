<div class="box box-warning">
	<div class="box-header">
		<h3 class="box-title"><?=lang('Home.rejected_list_title')?></h3>
	</div>
	<div class="box-body">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('Home.f_fmd0104_1')?></th>
				<th colspan="5"><?=lang('Home.f_date')?></th>
			</tr>
			<?php
				$has_gr=(strpos($main_menu,'generate_report')!==false);
			?>
			<?php foreach ($rejected_list as $fmd01) : ?>
				<?php $line = ceil(count($fmd01->data) / 5);
				if ($line > 1) {
					$rowspan = " rowspan='{$line}'";
				} else {
					$rowspan = '';
				}
				$i = 0;
				?>
				<tr>
					<td<?= $rowspan ?>><?= $fmd01->fmd0104 ?></td>
					<?php foreach ($fmd01->data as $d) {
						if ($i == 5) {
							echo "</tr><tr>";
							$i = 0;
						}
						echo '<td><a href="' . base_url('query_report/index/' . $fmd01->fmd0101 . '-' . $d->id) . '?approve">' . $d->date;
						if ($d->c04 > 0) {
							echo '&nbsp;' . $fmd21s[$d->c04];
						}
						echo "</a>";
						if ($has_gr){
							echo '<a href="'.base_url("generate_report/index/".$fmd01->fmd0101.'-'.$d->id."?approve").'"> (重新送簽)</a>';
						}						
						echo "</td>";
						$i++;
					}
					for ($j = $i; $j < 5; $j++) {
						echo '<td></td>';
					}
					?>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>