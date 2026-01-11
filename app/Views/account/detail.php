<div class="row toolbar">
    <div class="col-sm-8">
        <a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->sys0201 ?>"><i class="fa fa-edit"></i> <?=lang('Globe.toolbar_edit')?></a>
    </div>
    <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('Globe.toolbar_close')?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-horizontal">
            <?php if ($identity_column == 'sys0102'): ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0102')?></label>
                    <div class="col-sm-8">
                        <div class="form-control"><?= $data->sys0102 ?></div>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_users_name')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= user_display_name($data) ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0110')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= ($dept)?$dept->ent1004:'' ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0119')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0119 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0107')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0107 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0111')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0111 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0204')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $role[$data->sys0204] ?? '' ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0108')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= lang('Auth.v_sys0108_'.$data->sys0205) ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0121')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= lang('Auth.v_sys0121_'.$data->sys0121) ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0120')?></label>
                <div class="col-sm-8">
                    <img src="<?= ($data->sys0120) ? base_url("data/sign/" . $data->sys0101 . '/' . $data->sys0120) : base_url("assets/img/no_sign.png") ?>" class="form-control" alt="<?=lang('Auth.f_sys0120')?>" style="width:100%;height:auto">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0112')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0112 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=lang('Auth.f_sys0113')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0113 ?></div>
                </div>
            </div>
        </div>
    </div>
</div>