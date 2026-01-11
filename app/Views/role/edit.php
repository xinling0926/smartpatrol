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
	<?php if ($data) echo form_hidden('rol0101', $data->rol0101) ?>
	<div class="col-md-6">
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_rol0103')?></label>
			<div class="col-sm-8"><?= form_text_field('rol0103', $data) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_rol0104')?></label>
			<div class="col-sm-8"><?= form_textarea_field('rol0104', $data) ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_rol0106')?></label>
			<div class="col-sm-8"><?= form_radio_field('rol0106', $rol0106_option, $data, 1) ?></div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('f_rol0105')?></h3>
			</div>
			<div class="box-body">
				<div><?= form_checkbox_array_field('rol0105', $sys05s, $data) ?></div>
			</div>
		</div>
	</div>
	<?= form_close() ?>
</div>
