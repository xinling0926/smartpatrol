<div class="row">
	<div class="col-sm-12">
	</div>
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
		<tbody>
		<tr>
			<th><?=lang('f_sys1002')?></th>
			<th><?=lang('f_sys1003')?></th>
            <th><?=lang('f_sys1004')?></th>
		</tr>
		<?php foreach ($data as $d) : ?>
			<tr>
                <td><?= $d->sys1002 ?></td>
				<td><?= $d->sys1003 ?></td>
                <td><?= $d->sys1004 ?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>