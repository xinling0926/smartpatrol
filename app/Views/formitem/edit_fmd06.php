<div class="window_wrapper">
    <div class="row toolbar">
        <div class="col-sm-9">
            <h4><?= $prompt ?></h4>
            <h4><?= $fmd05->fmd0504 ?></h4>
        </div>
        <div class="col-sm-3 text-right">
            <button class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></button>
            <button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
        </div>
    </div>
	<?php echo form_open('', array('id' => 'fmd06_form', "class" => "form-horizontal")); ?>
	<?php echo form_hidden('fmd0601', $data->fmd0601) ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group"><label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0606_label')?></label>
                <div class="col-sm-8">
					<?= form_dropdown_field('fmd0606', $data_type_opt, $data, NULL, '', FALSE, array('onChange' => "fmd0606_onchange(this)")) ?>
                </div>
            </div>
            <div class="form-group"<?= (!isset($data) or array_search($data->fmd0606, [3, 6]) === FALSE) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0607_label')?></label>
                <div class="col-sm-8"><?= form_text_field('fmd0607', $data) ?> </div>
            </div>
            <div class="form-group"<?= (!isset($data) or array_search($data->fmd0606, [4, 5, 7]) === FALSE) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0608_label')?></label>
                <div class="col-sm-8"><?= form_textarea_field('fmd0608', $data) ?> </div>
            </div>
            <div class="form-group"<?= (!isset($data) or $data->fmd0606 != 8) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0608_1_label')?></label>
                <div class="col-sm-8"><?= form_text_field('fmd0608_1', $data) ?> </div>
            </div>
            <div class="form-group"<?= (!isset($data) or $data->fmd0606 != 8) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0608_2_label')?></label>
                <div class="col-sm-8"><?= form_text_field('fmd0608_2', $data) ?> </div>
            </div>
            <div class="form-group"<?= (!isset($data) or array_search($data->fmd0606, [1, 2, 3, 4, 7]) !== FALSE) ? '' : " style='display:none'" ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0609_label')?></label>
                <div class="col-sm-8"><?= form_text_field('fmd0609', $data, '', '', FALSE, 'rows=3') ?> </div>
            </div>
            <div class="form-group"<?= (!isset($data) or array_search($data->fmd0606, [5, 6]) !== FALSE) ? '' : " style='display:none'" ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0609_label')?></label>
                <div class="col-sm-8"><?= form_textarea_field('fmd0609_1', $data, '', '', FALSE, 'maxlength=20') ?> </div>
            </div>
            <div class="form-group"<?= (!isset($data) or $data->fmd0606 != 8) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0609_label')?></label>
                <div class="col-sm-8"><?= form_radio_field('fmd0609_2', ['0' => lang('FormItem.edit_fmd06_v_09_0'), '1' => lang('FormItem.edit_fmd06_v_09_1')], $data) ?></div>
            </div>
            <div class="form-group"<?= (!isset($data) or $data->fmd0606 != 8) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0618_label')?></label>
                <div class="col-sm-8"><?= form_text_field('fmd0618', $data, '', '', FALSE, 'maxlength=20') ?></div>
            </div>
            <div class="form-group"<?= (!isset($data) or $data->fmd0606 != 8) ? " style='display:none'" : '' ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0619_label')?></label>
                <div class="col-sm-8"><?= form_radio_field('fmd0619', ['0' => lang('FormItem.edit_fmd06_v_0'), '1' => lang('FormItem.edit_fmd06_v_1')], $data) ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group"<?= (isset($data) and array_search($data->fmd0606, [4, 5, 7]) !== FALSE) ? '' : " style='display:none'" ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0614_1_label')?></label>
                <div class="col-sm-7"><?= form_textarea_field('fmd0614_1', $data) ?>
                </div>
            </div>
            <div class="form-group"<?= (isset($data) and $data->fmd0606 < 3) ? '' : " style='display:none'" ?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0614_2_label')?></label>
                <div class="col-sm-3"><?= form_text_field('fmd0614_2', $data) ?></div>
                <div class="col-sm-1">~</div>
                <div class="col-sm-3"><?= form_text_field('fmd0614_3', $data) ?></div>
            </div>
            <div class="form-group"<?= (isset($data) and $data->fmd0606 =='9') ? " style='display:none'" : ''?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0616_label')?></label>
                <div class="col-sm-8"><?= form_radio_field('fmd0616', ['0' => lang('FormItem.edit_fmd06_v_0'), '1' => lang('FormItem.edit_fmd06_v_1')], $data) ?> </div>
            </div>
            <div class="form-group"><label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0611_label')?></label>
                <div class="col-sm-8"><?= form_radio_field('fmd0611', ['0' => lang('FormItem.edit_fmd06_v_0'), '1' => lang('FormItem.edit_fmd06_v_1')], $data) ?> </div>
            </div>
            <div class="form-group"<?= (isset($data) and $data->fmd0606 =='9') ? " style='display:none'" : ''?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0612_label')?></label>
                <div class="col-sm-8"><?= form_radio_field('fmd0612', ['0' => lang('FormItem.edit_fmd06_v_0'), '1' => lang('FormItem.edit_fmd06_v_1')], $data) ?> </div>
            </div>
            <div class="form-group"<?= (isset($data) and $data->fmd0606 =='9') ? " style='display:none'" : ''?>>
                <label class="col-sm-4 control-label"><?=lang('FormItem.edit_fmd06_fmd0613_label')?></label>
                <div class="col-sm-8"><?= form_radio_field('fmd0613', ['0' => lang('FormItem.edit_fmd06_v_0'), '1' => lang('FormItem.edit_fmd06_v_1')], $data) ?> </div>
            </div>
        </div>
    </div>
	<?= form_close() ?>
</div>
