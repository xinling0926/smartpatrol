<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('Photograph.v_serial')?></th>
				<th><?=lang('Photograph.v_thumbnail')?></th>
				<th><?=lang('Photograph.v_ent1004')?></th>
				<th><?=lang('Photograph.v_dev0104')?></th>
				<th><?=lang('Photograph.v_sys0103_sys0104')?></th>
				<th><?=lang('Photograph.v_shootingDate')?></th>
				<th><?=lang('Photograph.v_description')?></th>
				<?php if ($is_admin | $is_local_admin) : ?>
				<th><?=lang('Photograph.v_action')?></th>
				<?php endif ?>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<?php $_title = lang('Photograph.v_dev0104') . ': ' . $d->dev0104 . ' (' . $d->pad0707 . ')'; ?>
					<td class='a' onclick='detail(<?= $d->pad0701 ?>,"<?= $_title ?>")'><img data-id='<?= 'imageThumbnail_' . $d->pad0701 ?>'/></td>
					<td><?= $dep_opt[$d->pad0702] ?></td>
					<td><?= $d->dev0104 ?></td>
                    <td><?= $d->sys0103_sys0104 ?></td>
					<td><?= $d->pad0707 ?></td>
					<td><div class="text-nowrap" style="overflow: hidden;text-overflow: ellipsis; width: 300px;"><?= $d->pad0705 ?></div></td>
					<?php if ($is_admin | $is_local_admin) : ?>
					<td>
					<a href="#" class="btn btn-xs btn-danger" onclick="del(this)" data-id="<?= $d->pad0701 ?>" data-cuid="<?= csrf_hash() ?>">
					<i class="fa fa-trash-o"></i><?= lang('Photograph.v_pad0701_del') ?></a>
					</td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
<script type='text/javascript'>
$(function() {
	$("img[data-id^='imageThumbnail_']").each(function() {
		var pad0701 = $(this).attr('data-id').match(/\d+/)[0];
		//$(this).html('<img src="'+ base_url + folder + controller + '/getPhotograph/' + pad0701 + '/TRUE' + '"/>'); 
		$(this).attr('src', base_url + folder + controller + '/getPhotograph/' + pad0701 + '/1'); 
	})
})
</script>