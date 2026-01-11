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
	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
	<?php if ($data) echo form_hidden('ent2001', $data->ent2001) ?>
	<div class="col-md-6">
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_ent2003')?></label>
			<div class="col-sm-8"><?= form_text_field('ent2003', $data) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_ent2004')?></label>
			<div class="col-sm-8"><?= form_text_field('ent2004', $data) ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_ent2005')?></label>
			<div class="col-sm-8"><?= form_radio_field('ent2005', array('0'=>lang('v_ent2005_0'),'1'=>lang('v_ent2005_1')), $data, 1) ?></div>
		</div>
	</div>
	<?= form_close() ?>
</div>
