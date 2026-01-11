<div class="window_wrapper">
    <table class="table table-striped table-hover dataTable">
        <tbody>
        <tr>
            <th><?=lang('v_version')?></th>
            <th><?=lang('v_createDate')?></th>
            <th><?=lang('v_updateUser')?></th>
            <th><?=lang('v_updateTime')?></th>
        </tr>
		<?php foreach ($fmd30s as $d) : ?>
            <tr>
                <td><?= $d->fmd3008 ?></td>
                <td><?= $d->fmd30z2 ?></td>
                <td><?= $user->name($d->fmd30z3) ?></td>
                <td><?= $d->fmd30z4 ?></td>
            </tr>
		<?php endforeach ?>
        </tbody>
    </table>
</div>

