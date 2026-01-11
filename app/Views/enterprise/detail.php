<div class="row toolbar">
    <div class="col-sm-8">
        <a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->ent0101 ?>"> <i class="fa fa-edit"></i> <?= lang('toolbar_edit')
			?></a>
    </div>
    <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?= lang('toolbar_close') ?></a>
    </div>
</div>
<div class="form-horizontal">
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0103') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?= $data->ent0103 ?></div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0102') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?= $data->ent0102 ?></div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0104') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?php if ($data->ent0104) {
					echo lang('v_ent0104_1');
				} else {
					echo lang('v_ent0104_0');
				} ?></div>
        </div>
    </div>
</div>
<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title"><?= lang('ent02_heading') ?></h3>
        <button class="btn btn-default pull-right" onclick="edit(this)" data-item='ent02' data-parent='<?= $data->ent0101 ?>'><i
                    class="fa fa-fw fa-plus-square-o"></i><?= lang('add_ent02_btn') ?></button>
    </div>
    <div class="box-body" id="pane_detail_list">
        <table class="table table-striped table-hover dataTable">
            <tbody>
            <tr>
                <th><?= lang('no') ?></th>
                <th><?= lang('f_ent02z2') ?></th>
                <th><?= lang('f_ent0203') ?></th>
                <th><?= lang('f_ent0204') ?></th>
                <th><?= lang('f_ent0205') ?></th>
            </tr>
			<?php $i = 0; ?>
			<?php foreach ($ent02s as $d) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $d->ent02z2 ?></td>
                    <td><a href="javascript:void(0)" onclick="sub_detail(this)" data-id="<?= $d->ent0201 ?>" data-item="ent02"><?= $d->ent0203
							?></a></td>
                    <td><?= $d->ent0204 ?></td>
                    <td><?php if ($d->ent0205) {
							echo $d->ent0205;
							if ($d->ent0205 < today()) echo lang('ent0205_expiry');
						} else {
							echo lang('ent0205_is_null');
						}
						?></td>
                </tr>
			<?php endforeach ?>
            <tr>
                <td colspan="6"><?=lang('smart_patrol_form')?>=<?= $smart_patrol_form ?>, <?=lang('smart_patrol_device')?>=<?= $smart_patrol_device
                    ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>