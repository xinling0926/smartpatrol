<div class="box box-primary" id="report_result">
	<div class="box-header with-border">
		<h3 class="box-title"><?=lang('box_result')?></h3>
	</div>
	<div class="box-body">
		<div id="container" style="width:100%; height:400px;"></div>
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_common_no')?></th>
				<th class="text-center">
					<?=lang('f_name_1')?><span class="pull-right"><?=lang('f_name_2')?></span><br><span class="pull-left"><?=lang('f_name_3')?></span>
				</th>
				<?php
				foreach ($title as $t) {
					echo "<th>{$t}</th>";
				}
				?>
			</tr>
			<?php
			$offset = 1;
			foreach ($detail as $item) {
				echo "<tr><td>{$offset}</td>";
				echo "<td>{$item->name}</td>";
				$i = 1;
				foreach ($title as $t) {
					$fn = 'data' . $i;
					if (isset($item->$fn)) {
						echo "<td>{$item->$fn}</td>";
					} else {
						echo "<td></td>";
					}
					$i++;
				}
				echo "</tr>";
				$offset++;
			}
			?>
			</tbody>
		</table>
	</div>
</div>

<?php assets_js('highcharts', 'Highchart') ?>
<?php assets_js('modules/exporting', 'Highchart') ?>
<script type='text/javascript'>

	$(function () {
		$('#container').highcharts({
			title: {
				text: '<?=lang('highcharts_title')?>',
				x: -20 //center
			},
			xAxis: {
				categories: ['<?= implode("','", $title)?>']
			},
			yAxis: {
				title: {
					text: '<?=lang('highcharts_yAxis_title')?>'
				}
			},
			series: <?= json_encode($summery) ?>
		});
	});

</script>