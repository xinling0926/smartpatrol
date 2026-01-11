<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?=lang('detail_heading')?></h3>
    </div>
    <div class="box-body">
        <div class="row toolbar">
            <div class="col-sm-10">
                <a href="#" class="btn btn-primary" onclick="edit2(this)" data-id="<?= $data->ent1001 ?>"> <i class="fa fa-edit"></i> <?=lang
                ('toolbar_edit')?></a>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('f_ent1004')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->ent1004 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('f_ent1003')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->ent1003 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('f_ent1007')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= lang('v_ent1007_'.$data->ent1007); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>