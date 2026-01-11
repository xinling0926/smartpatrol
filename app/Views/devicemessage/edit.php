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
<?php if ($data) echo form_hidden('dev0301', $data->dev0301) ?>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('label_dev0302')?></label>
	<div class="col-sm-4"><?= form_dropdown_field('dev0302', $dev01s, $data, '', '', TRUE) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('label_dev0303')?></label>
	<div class="col-sm-4"><?= form_dropdown_field('dev0303', $sys01s, $data, '', '', TRUE) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('label_dev0304')?></label>
	<div class="col-sm-4"><?= form_text_field('dev0304', $data, '', '', false, array('placeholder' => lang('input_placeholder_dev0304'))) ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('label_dev0305_i')?></label>
	<div class="col-sm-4"><?= form_textarea_input('dev0305', $data, '', array('placeholder' => lang('input_placeholder_dev0305'))) ?>
	</div>
</div>
<?= form_close() ?>