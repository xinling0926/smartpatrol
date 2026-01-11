<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('fmd0101', $data->fmd0101) ?>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_ent1004')?></label>
	<div class="col-sm-4"><?= form_dropdown_field('fmd0102', $dept, $data, '', '', TRUE) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_fmd0103')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0103', $data) ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_fmd0104')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0104', $data) ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_fmd0105')?></label>
	<div class="col-sm-10"><?= form_radio_field('fmd0105', $fmd0105_opt, $data, 1) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_fmd0110')?></label>
	<div class="col-sm-10"><?= form_radio_field('fmd0110', ['0'=>lang('v_fmd0110_0'),1=>lang('v_fmd0110_1')], $data, 1) ?></div>
</div>
<?= form_close() ?>