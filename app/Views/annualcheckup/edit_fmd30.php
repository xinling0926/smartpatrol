<div class="window_wrapper">
    <div class="row toolbar">
        <div class="col-sm-6 col-sm-offset-6 text-right">
            <a href="#" class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
            <a href="#" class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
        </div>
    </div>
    <?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
    <?php if ($data) echo form_hidden('fmd3001', $data->fmd3001) ?>
    <?php if (isset($data->fmd3003_lock) && $data->fmd3003_lock) echo form_hidden('fmd3003_lock', 'TRUE') ?>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?=lang('main_info_ent1004')?></label>
        <?php if (count($ent10s) > 2) : ?>
        <div class="col-sm-4">
        <?php if ($data) : ?>
            <div class="form-control"><?= $data->ent1004 ?></div>
        <?php else : ?>
            <?= form_dropdown_input('ent1001', $ent10s, '', '', ['onchange' => "select_ent10();"]) ?>
        <?php endif ?>
        </div>
        <?php endif ?>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?=lang('main_info_fmd0104')?></label>
        <div class="col-sm-4">
        <?php if ($data) : ?>
            <div class="form-control"><?= $data->fmd0104 ?></div>
        <?php else : ?>
            <?= form_dropdown_input('fmd0101', $fmd01s, '', '', ['onchange' => '']) ?>
        <?php endif ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?=lang('v_beginDate')?></label>
        <div class="col-sm-4">
        <?php if ($data) : ?>
            <?= form_date_input('fmd3003', $data->fmd3003) ?>
        <?php else : ?>
            <?= form_date_input('fmd3003', today()) ?>
        <?php endif ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?=lang('v_endDate')?></label>
        <div class="col-sm-4">
        <?php if ($data) : ?>
            <?= form_date_input('fmd3004', $data->fmd3004) ?>
        <?php else : ?>
            <?= form_date_input('fmd3004', today()) ?>
        <?php endif ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?=lang('v_type')?></label>
        <div class="col-sm-10"><?= form_radio_field('fmd3005', $fmd3005_opt, $data, 1) ?></div>
        </div>
    </div>
    <?= form_close() ?>
</div>
<?php assets_css('bootstrap-datepicker3', 'datepicker') ?>
<?php assets_js('bootstrap-datepicker.min', 'datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-TW.min', 'datepicker') ?>
<script type='text/javascript'>
	$('.date').datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		language: "zh-TW",
		autoclose: true,
		zIndexOffset: 1200,
		todayHighlight: true
    });
</script>