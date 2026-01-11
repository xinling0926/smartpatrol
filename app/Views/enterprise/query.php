<div class="row">
    <div class="col-sm-12">
        <table class="table table-striped table-hover dataTable">
            <tbody>
            <tr>
                <th><?= lang('f_ent0103') ?></th>
                <th><?= lang('f_ent0102') ?></th>
                <th><?= lang('f_ent0104') ?></th>
            </tr>
			<?php foreach ($data as $d) : ?>
                <tr>
                    <td><a href="#" onclick='d(<?= $d->ent0101 ?>,"<?= $d->ent0103 ?>")'><?= $d->ent0103 ?></a></td>
                    <td><?= $d->ent0102 ?></td>
                    <td class="<?php if ($d->ent0104) {
						echo "text-green";
					} else {
						echo "text-red";
					} ?>"><?php if ($d->ent0104) {
							echo lang('v_ent0104_1');
						} else {
							echo lang('v_ent0104_0');
						} ?></td>
                </tr>
			<?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>
<script type='text/javascript'>
    function d(id, title) {
        close_tab('ent02');
        detail(id, title);
    }
</script>