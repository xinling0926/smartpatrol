<div class="row">
	<?php if (isset($approval_count)) : ?>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box <?= ($approval_count) ? "bg-red" : "bg-orange" ?>">
				<div class="inner">
					<h3><?= $approval_count; ?></h3>
					<p><?=lang('Home.approval_count')?></p>
				</div>
				<div class="icon">
					<i class="fa fa-check-square-o"></i>
				</div>
				<a href="<?= base_url('approve') ?>" class="small-box-footer"><?=lang('Home.view_more_msg')?> <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
	<?php endif ?>
	<?php if (isset($hpw_report1_count)) : ?>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-aqua">
				<div class="inner">
					<h3 style="font-size:30px;"><?= $hpw_report1_count; ?></h3>
					<p><?=lang('Home.hpw_report1_count')?></p>
				</div>
				<div class="icon">
					<i class="ion ion-pie-graph"></i>
				</div>
				<a href="<?= base_url('hpw/hpw_report4') ?>" class="small-box-footer"><?=lang('Home.view_more_msg')?> <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
	<?php endif ?>
	<?php if (isset($hpw_report2_count)) : ?>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-yellow">
				<div class="inner">
					<h3><?= $hpw_report2_count; ?></h3>
					<p><?=lang('Home.hpw_report2_count')?></p>
				</div>
				<div class="icon">
					<i class="ion ion-stats-bars"></i>
				</div>
				<a href="<?= base_url('hpw/hpw_report2') ?>" class="small-box-footer"><?=lang('Home.view_more_msg')?> <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
	<?php endif ?>
	<div class="col-lg-3 col-xs-6">
		<div class="small-box bg-green">
			<div class="inner">
				<h3>ＡＰＰ</h3>
				<p><?= lang('Home.app_version') ?><?= $setting->item('app_version') ?></p>
			</div>
			<div class="icon">
				<i class="fa fa-android"></i>
			</div>
			<a href="<?= base_url($setting->item('app_download_url')) ?>" class="small-box-footer"><?= lang('Home.click_download') ?> <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-6">
        <!-- 今日當班漏檢與異常 -->
        <button class="btn btn-default" onclick="window.location.href='<?=base_url("home/index?toggle_date={$today}&onduty=true") ?>'"><i class="fa fa-fw <?= $fa_toggle_1 ?>"></i> <?= $btnToggleString_1 ?></button>
        <!-- 今日漏檢與異常 -->
        <button class="btn btn-default" onclick="window.location.href='<?=base_url("home/index?toggle_date={$today}") ?>'"><i class="fa fa-fw <?= $fa_toggle_2 ?>"></i> <?= $btnToggleString_2 ?></button>
        <!-- 昨日漏檢與異常 -->
        <button class="btn btn-default" onclick="window.location.href='<?=base_url("home/index?toggle_date={$yesterday}") ?>'"><i class="fa fa-fw <?= $fa_toggle_3 ?>"></i> <?= $btnToggleString_3 ?></button>
    </div>
</div>

<div class="row">
	<?php if (isset($rejected_list) and $rejected_list): ?>
		<div class="col-lg-6">
			<?= view('home/rejected_list', get_defined_vars()) ?>
		</div>
	<?php endif ?>

	<?php
	$permissions = session('permissions') ?? '';
	if (is_array($permissions)) {
		$permArray = $permissions;
	} else {
		$permArray = $permissions ? explode(",", $permissions) : [];
	}
	if (isset($repair) && (in_array(23, $permArray) || in_array(30, $permArray) || in_array(31, $permArray))): ?>
		<div class="col-lg-6">
			<?= view('home/repair_list', get_defined_vars()) ?>
		</div>
	<?php endif; ?>
</div>

<?php if (isset($miss)) : ?>
	<div class="row">
		<?php foreach ($miss as $ent10) : ?>
			<div class="col-lg-6">
				<div class="box box-warning">
					<div class="box-header">
						<h3 class="box-title"><?= $ent10->ent1004 ?> <?=lang('Home.table_title')?></h3>
					</div>
					<div class="box-body">
						<table class="table">
							<thead style="white-space: nowrap;">
							<tr>
								<th><?=lang('f_fmd0104')?></th>
								<th><?=lang('f_fmd0105')?></th>
								<th><?=lang('Home.f_date')?></th>
								<th><?=lang('Home.f_error_count')?></th>
								<th><?=lang('Home.f_error_rate')?></th>
								<th><?=lang('Home.f_miss_count')?></th>
								<th><?=lang('Home.f_miss_rate')?></th>
							</tr>
							</thead>
							<tbody>
							<?php if (is_array($ent10->fmd01s) && count($ent10->fmd01s)) : ?>
							<?php foreach ($ent10->fmd01s as $fmd01) : ?>
								<tr>
									<td><?= $fmd01->fmd0104 ?></td>
									<td style="white-space: nowrap;"><?= $fmd0105_opt[$fmd01->fmd0105] ?? '' ?></td>
									<?php if (isset($fmd01->iso)) : ?>
										<td style="white-space: nowrap;"><a href="<?= base_url('query_report/index/' . $fmd01->fmd0101 . '-' . $fmd01->iso->id) ?>">
												<?= $fmd01->iso->date ?></a></td>
										<td class=" text-right"><?= $fmd01->iso->error_count ?></td>
										<td class=" text-right"><?= $fmd01->error_rate ?>%</td>
										<td class="text-right"><?= $fmd01->iso->miss_count ?></td>
										<td class="text-right"><?= $fmd01->miss_rate ?>%</td>
									<?php else : ?>
										<td style="white-space: nowrap;"><?= $fmd01->date ?></td>
										<td colspan="4" class="bg-warning"><?=lang('Home.not_data')?></td>
									<?php endif ?>
								</tr>
							<?php endforeach ?>
							<?php else : ?>
								<tr>
									<td colspan="7" class="bg-warning"><?=lang('Home.not_data')?></td>
								</tr>
							<?php endif ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php endforeach ?>
	</div>
<?php endif ?>

<?php if (!empty($chartpermission)):?>
<?php $defY=date('Y')-1911; $defM=(int)date('m');?>
<div class="row">
	<div class="col-lg-6">
	    <div class="box box-warning">
		<div>
			<select id="cbeuiY">
				<?php for($y=$defY-2; $y<=$defY; $y++): ?>
					<option <?=($defY==$y)?'selected':'';?>><?=$y;?></option>
				<?php endfor; ?>
			</select>年
			<select id="cbeuiM">
				<?php for($i=1;$i<=12;$i++):?>
					<option <?=($defM==$i)?'selected':'';?>><?=str_pad($i,2,'0',STR_PAD_LEFT);?></option>
				<?php endfor;?>
			</select>月
			<button id="btnReloadEUI" class="btn btn-sm btn-danger">更新</button>
			<a id="btnExportEUI" download="Chart_EUI.jpg" href="" class="btn btn-sm btn-success float-right bg-flat-color-1"><i class="fa fa-download"></i></a>
		</div>
		<canvas id="canvasEUI"></canvas>
		</div>
    </div>

	<div class="col-lg-6">
	    <div class="box box-warning">
		<div>
			<select id="cberrY">
				<?php for($y=$defY-2; $y<=$defY; $y++): ?>
					<option <?=($defY==$y)?'selected':'';?>><?=$y;?></option>
				<?php endfor; ?>
			</select>年
			<select id="cberrM">
				<?php for($i=1;$i<=12;$i++):?>
					<option <?=($defM==$i)?'selected':'';?>><?=str_pad($i,2,'0',STR_PAD_LEFT);?></option>
				<?php endfor;?>
			</select>月
			<button id="btnReloadERR" class="btn btn-sm btn-danger">更新</button>
			<a id="btnExportERR" download="Chart_ERR.jpg" href="" class="btn btn-sm btn-success float-right bg-flat-color-1"><i class="fa fa-download"></i></a>
		</div>
		<canvas id="canvasERR"></canvas>
		</div>
    </div>

	<div class="col-lg-6">
	    <div class="box box-warning">
		<div>
			<select id="cbmissY">
				<?php for($y=$defY-2; $y<=$defY; $y++): ?>
					<option <?=($defY==$y)?'selected':'';?>><?=$y;?></option>
				<?php endfor; ?>
			</select>年
			<select id="cbmissM">
				<?php for($i=1;$i<=12;$i++):?>
					<option <?=($defM==$i)?'selected':'';?>><?=str_pad($i,2,'0',STR_PAD_LEFT);?></option>
				<?php endfor;?>
			</select>月
			<button id="btnReloadMISS" class="btn btn-sm btn-danger">更新</button>
			<a id="btnExportMISS" download="Chart_MISS.jpg" href="" class="btn btn-sm btn-success float-right bg-flat-color-1"><i class="fa fa-download"></i></a>
		</div>
		<canvas id="canvasMISS"></canvas>
		</div>
    </div>

</div>
<?php assets_js('2.93/Chart', 'chartjs') ?>
<script type="text/javascript">
		function colorize(opaque, ctx) {
			var v = ctx.dataset.data[ctx.dataIndex];
			var old = ctx.chart.data.datasets[0].data[ctx.dataIndex];
			if (v > old) {
				return '#d61e20';
			} else {
				return '#f5a209';
			}
		}
		function getdata(chart, url){
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(data){
					chart.data.labels = ['信義', '寶慶', '板橋', '新站', '桃園', '新竹', '台中', '嘉義', '台南(成功)', '台南(公園)', '高雄', '花蓮'];
					chart.data.datasets = data;
					chart.update();
				},
				error: function(xhr, status, error){
					console.error('Chart data load error:', error);
				}
			});
		}
		function reloadEUI(){
			var y=$('#cbeuiY').val();
			var m=$('#cbeuiM').val();
			var url="<?php echo base_url('Home/axGetEUI/')?>"+y+"/"+m;
			getdata(charteui,url);
		}
		function reloadERR(){
			var y=$('#cberrY').val();
			var m=$('#cberrM').val();
			var url="<?php echo base_url('Home/axGetERR/')?>"+y+"/"+m;
			getdata(charterr,url);
		}
		function reloadMISS(){
			var y=$('#cbmissY').val();
			var m=$('#cbmissM').val();
			var url="<?php echo base_url('Home/axGetMISS/')?>"+y+"/"+m;
			getdata(chartmiss,url);
		}
		window.onload = function() {
			var ctxEUI = document.getElementById('canvasEUI').getContext('2d');
			window.charteui = new Chart(ctxEUI, {
				type: 'bar',
				data: [],
				options: {
					title: {
						display: true,
						text: 'EUI統計'
					},
					tooltips: {
						mode: 'index',
						intersect: false
					},
					responsive: true,
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true
						}
					}
				}
			});
			reloadEUI();

			var ctxERR = document.getElementById('canvasERR').getContext('2d');
			window.charterr = new Chart(ctxERR, {
				type: 'bar',
				data: [],
				options: {
					title: {
						display: true,
						text: '合格率統計'
					},
					tooltips: {
						mode: 'index',
						intersect: false
					},
					responsive: true,
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true
						}
					}
				}
			});
			reloadERR();

			var ctxMISS = document.getElementById('canvasMISS').getContext('2d');
			window.chartmiss = new Chart(ctxMISS, {
				type: 'bar',
				data: [],
				options: {
					title: {
						display: true,
						text: '準點率統計表'
					},
					tooltips: {
						mode: 'index',
						intersect: false
					},
					responsive: true,
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true
						}
					}
				}
			});
			reloadMISS();

			document.getElementById("btnReloadEUI").addEventListener('click', function(){
				reloadEUI();
			});			

			document.getElementById("btnReloadERR").addEventListener('click', function(){
				reloadERR();
			});			

			document.getElementById("btnReloadMISS").addEventListener('click', function(){
				reloadMISS();
			});			

			document.getElementById("btnExportEUI").addEventListener('click', function(){
			  var url_base64jp = window.charteui.toBase64Image();
			  var a =  document.getElementById("btnExportEUI");
  			  a.href = url_base64jp;
			});			
			document.getElementById("btnExportERR").addEventListener('click', function(){
			  var url_base64jp = window.charterr.toBase64Image();
			  var a =  document.getElementById("btnExportERR");
  			  a.href = url_base64jp;
			});			
			document.getElementById("btnExportMISS").addEventListener('click', function(){
			  var url_base64jp = window.chartmiss.toBase64Image();
			  var a =  document.getElementById("btnExportMISS");
  			  a.href = url_base64jp;
			});			
		};
</script>
<?php endif;?>