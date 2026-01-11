<?php
		$locale = service('request')->getLocale();
		if ($locale == 'zh-CN') {
			$field = 'sys0402';
		} else {
			$field = 'sys0412';
		}
?>
<div class="box box-warning">
	<div class="box-header with-border">
		<h3 class="box-title">查詢結果</h3>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th>序號</th>
						<th>帳號</th>
						<th>姓名</th>
						<th>操作功能</th>
						<th>IP地址</th>
						<th>操作時間</th>
					</tr>
					<?php foreach ($data as $d) : ?>
						<tr>
							<td><?= ++$offset ?></td>
							<td><?= $d->sys0102 ?? '' ?></td>
							<td><?= ($d->sys0103 ?? '') . ($d->sys0104 ?? '') ?></td>
							<td><?= $d->{$field} ?? '' ?></td>
							<td><?= $d->log0303 ?? '' ?></td>
							<td><?= $d->log0302 ?? '' ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?= view('layout/data_table_footer_box', get_defined_vars()) ?>
</div>
