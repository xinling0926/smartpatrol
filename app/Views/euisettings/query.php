<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
		<tbody>
		<tr>
			<th>分店</th>
			<th>統計年月</th>
			<th>統計數值</th>
			<th>自動計算數值</th>
		</tr>
		<?php foreach ($data as $d) : ?>
			<tr>
				<td><?= $d->fmd4003 ?></td>
				<td><a href="#" onclick='detail(<?= $d->fmd4101 ?>,"<?= $d->fmd4103 ?>")'><?= $d->fmd4103 ?></a></td>
				<td><?= $d->fmd4104 ?></td>
				<td><?= $d->fmd4105 ?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
