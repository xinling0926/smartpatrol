<div class="box box-primary" id="report_result">
	<div class="box-header">
		<h3 class="box-title"><?= lang('box_result') ?></h3>
	</div>
	<div class="box-body">
		<div id="container" style="width:100%; height:400px;"></div>
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?= lang('f_common_no') ?></th>
				<th class="text-center">
					<?= lang('f_name_1') ?><span class="pull-right"><?= lang('f_name_2') ?></span><br><span class="pull-left"><?= lang('f_name_3') ?></span>
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
						$id = urlencode("{$item->name}|{$start_date}|{$i}");
						echo "<td class=\"a\" onclick=\"dd('{$id}')\">{$item->$fn}</td>";
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
	<div class="box-footer">
		<button class="btn btn-default" onclick="export_excel()"><i class="fa fa-download"></i> 汇出Excel</button>
	</div>
</div>

<?php assets_js('highcharts', 'Highchart') ?>
<?php assets_js('modules/exporting', 'Highchart') ?>
<script type='text/javascript'>

	$(function () {
		$('#container').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '<?= lang('highcharts_title') ?>',
				x: -20 //center
			},
			xAxis: {
				categories: ['<?= implode("','", $summery['title'])?>']
			},
			yAxis: {
				min: 0,
				title: {
					text: '<?= lang('highcharts_yAxis_title') ?>'
				}
			},
			colors: ['#f39c12', '#0d233a', '#8bbc21', '#910000', '#1aadce',
				'#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
			series: [{
				name: '<?= lang('highcharts_series_name') ?>',
				data: <?= json_encode($summery['value']) ?>
			}]
		});
	});

	function export_excel() {
		<?php if (count($title)>7) :?>
		show_error('<?=lang('export_hint')?>');
		<?php else : ?>
		window.location.href = base_url + 'hpw/hpw_report2/export_excel';
		<?php endif ?>
	}

</script>