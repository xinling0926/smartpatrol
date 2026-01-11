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
	<?php if ($data) echo form_hidden('sys1001', $data->sys1001) ?>
	<div class="col-md-6">
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_sys1002')?></label>
			<div class="col-sm-8"><?= form_text_field('sys1002', $data) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_sys1003')?></label>
			<div class="col-sm-8"><?= form_text_field('sys1003', $data) ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_sys1004')?></label>
			<div class="col-sm-8"><?= form_text_field('sys1004', $data) ?></div>
		</div>
	</div>
	<?= form_close() ?>
</div>
