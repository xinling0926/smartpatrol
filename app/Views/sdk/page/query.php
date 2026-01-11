<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th>序</th>
				<th>Title</th>
				<th>Folder</th>
				<th>Controller</th>
				<th>Action</th>
				<th>权限设定</th>
				<th>系统管理员权限</th>
				<th>功能模组</th>
				<th>状态</th>
				<th width="80px">顺序调整</th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><a href="#" onclick='detail(<?=$d->sys0401 ?>,"<?= $d->sys0402 ?>")'><?= $d->sys0402 ?></a></td>
					<td><?= $d->sys0409 ?></td>
					<td><?= $d->sys0403 ?></td>
					<td><?= $d->sys0404 ?></td>
					<td><?= $sys0406_opt[$d->sys0406] ?></td>
					<td><?php if($d->sys0410) echo 'V'; ?></td>
					<td><?php if(is_null($d->sys0411)) { echo '标准版'; } else { echo $of[$d->sys0411];} ?></td>
					<td class="<?php if ($d->sys0408) { echo "text-green"; } else { echo "text-red"; } ?>"><?php if ($d->sys0408) { echo "启用"; } else { echo "停用"; } ?></td>
					<td>
						<button class="fa fa-arrow-down" onclick="order(<?=$d->sys0401?>,1)"></button>
						<button class="fa fa-arrow-up" onclick="order(<?=$d->sys0401?>,-1)"></button>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
