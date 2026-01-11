<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable user_list">
		<tbody>
		<tr>
            <th></th>
            <th><?=lang('Auth.f_users_name')?></th>
			<?php if ($identity_column == 'sys0102'): ?><th><?=lang('Auth.f_sys0102')?></th><?php endif ?>
			<th><?=lang('Auth.f_sys0107')?></th>
			<th><?=lang('Auth.f_sys0110')?></th>
            <th><?=lang('Auth.f_sys0204')?></th>
            <th><?=lang('Auth.f_sys0108')?></th>
		</tr>
		<?php foreach ($data as $d) : ?>
			<tr>
                <td><img src="<?=base_url('data/avatar/'.($d->sys0117 ? $d->sys0101.'/'.$d->sys0117 : "man.png"))?>" alt="User Image" class="user-image"></td>
				<td><a href="#" onclick='detail(<?=$d->sys0201 ?>,"<?= user_display_name($d) ?>")'><?= user_display_name($d) ?></a></td>
			    <?php if ($identity_column == 'sys0102'): ?><td><?=$d->sys0102?></td><?php endif ?>
                <td><?=$d->sys0107?></td>
                <td><?php if($d->sys0110 && isset($dept[$d->sys0110])) { echo $dept[$d->sys0110];} ?></td>
                <td><?= $role[$d->sys0204] ?? '' ?></td>
                <td class="<?php if ($d->sys0205) { echo "text-green"; } else { echo "text-red"; } ?>"><?php if ($d->sys0205) { echo lang('Auth.v_sys0108_1'); } else { echo lang('Auth.v_sys0108_0'); } ?></td>
            </tr>
		<?php endforeach ?>
		</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
