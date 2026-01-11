<div class="window_wrapper">
    <table class="table table-striped table-hover dataTable">
        <tbody>
        <tr>
            <th><?= lang('f_q_fmd0107') ?></th>
            <th><?= lang('f_q_fmd01z1') ?></th>
            <th><?= lang('f_q_fmd01z2') ?></th>
            <th><?= lang('f_q_fmd0111') ?></th>
        </tr>
		<?php foreach ($fmd01s as $d) : ?>
            <tr>
                <td><?= $d->fmd0107 ?></td>
                <td><?= $user->name($d->fmd01z1) ?></td>
                <td><?= $d->fmd01z2 ?></td>
                <td><?= $d->fmd0111 ?></td>
            </tr>
		<?php endforeach ?>
        </tbody>
    </table>
</div>

