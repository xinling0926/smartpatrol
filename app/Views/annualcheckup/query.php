<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('v_serial')?></th>
				<th><?=lang('v_ent1004')?></th>
				<th><?=lang('v_fmd0103')?></th>
				<th><?=lang('v_fmd0104')?></th>
				<th><?=lang('v_beginDate')?></th>
				<th><?=lang('v_endDate')?></th>
				<th><?=lang('v_type')?></th>
				<th><?=lang('v_version')?></th>
				<th><?=lang('v_fmd3006')?></th>
				<th><?=lang('v_action')?></th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><?= $dep_opt[$d->fmd0102] ?></td>
					<td><?= $d->fmd0103 ?></td>
					<td class="a" onclick='detail(<?= $d->fmd3001 ?>,"<?= $d->fmd0104 ?>")'><?= $d->fmd0104 ?></td>
					<td><?= $d->fmd3003 ?></td>
					<td><?= $d->fmd3004 ?></td>
					<td><?= $fmd3005_opt[$d->fmd3005] ?></td>
					<td class="a" onclick="showFmd30History(<?= $d->fmd3001 ?>)"><?= $d->fmd3008 ?></td>
					<td><?= $fmd3006_opt[$d->fmd3006] ?></td>
					<td><?php
					switch($d->fmd3006) {
						case 0:
							echo '&nbsp;';
							break;
						case 1:
							echo '<a href="#" class="btn btn-xs btn-primary" onclick="q(' . $d->fmd3001 . ')">';
							echo '<i class="fa fa-play"></i>'.lang('v_fmd3006_2').'</a> ';
							echo '<a href="#" class="btn btn-xs btn-danger" onclick="del(this)" data-id="' . $d->fmd3001 . " data-cuid=" . csrf_hash() . '">';
							echo '<i class="fa fa-trash-o"></i>'.lang('v_fmd3006_del').'</a>';
							break;
						case 2:
							echo '<a href="#" class="btn btn-xs btn-danger" onclick="a(' . $d->fmd3001 . ',0)">';
							echo '<i class="fa fa-stop"></i>'.lang('v_fmd3006_0').'</a>';
							break;
						case 3:
							echo '&nbsp;';
							break;
					}
					?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
<script type='text/javascript'>
function showFmd30History(fmd3001) {
    if ($(document.body)[0].clientWidth < 769) {
        var width = '90%';
    } else {
        var width = '600px';
    }
    var height = '50%';
    var url = base_url + 'annual_checkup/form_history/' + fmd3001;

    if (langType == 'zh-CN') {
        var title = '版本资讯';
    } else {
        var title = '版本資訊';
    }
    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            skin: 'layui-layer-rim', //加上边框
            area: [width, height], //宽高
            content: data
        });
    });
}
</script>
