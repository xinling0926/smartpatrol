<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
		<tbody>
		<tr>
			<th><?=lang('f_ent2003')?></th>
			<th><?=lang('f_ent2004')?></th>
			<th><?=lang('f_ent2005')?></th>
		</tr>
		<?php foreach ($data as $d) : ?>
			<tr>
				<td><a href="#" onclick='detail(<?=$d->ent2001 ?>,"<?= $d->ent2003 ?>")'><?= $d->ent2003 ?></a></td>
				<td><?= $d->ent2004 ?></td>
				<td class="<?php if ($d->ent2005=='1') { echo "text-green"; } else { echo "text-red"; } ?>"><?php if ($d->ent2005=='0') { echo lang("v_ent2005_0"); } else { echo lang("v_ent2005_1"); } ?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
