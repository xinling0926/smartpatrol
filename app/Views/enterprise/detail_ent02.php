<div class="row toolbar">
    <div class="col-sm-8">
        <a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->ent0201 ?>" data-item="ent02" data-cuid="<?=
		csrf_hash() ?>">
            <i class="fa fa-trash-o"></i> <?= lang('toolbar_del') ?></a>
    </div>
    <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-default" onclick="close_tab('ent02')"><i class="fa fa-close"></i> <?= lang('toolbar_close') ?></a>
    </div>
</div>
<div class="form-horizontal">
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0203') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?= $data->ent0203 ?></div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0204') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?= $data->ent0204 ?></div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0205') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?php if ($data->ent0205) {
					echo $data->ent0205;
				} else {
					echo lang('ent0205_is_null');
				} ?></div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= lang('f_ent0207') ?></label>
        <div class="col-sm-4">
            <div class="form-control" style="word-break: break-all;height: auto;"><?= $data->ent0207 ?></div>
        </div>
    </div>
</div>
