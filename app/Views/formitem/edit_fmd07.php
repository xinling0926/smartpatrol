<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6">

		</div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></button>
			<button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>

	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
	<?php echo form_hidden('fmd0101', $fmd0101) ?>

	<div class="form-group"><label class="col-sm-6 control-label"><?=lang('FormItem.edit_fmd07_fields_label')?></label>
		<div class="col-sm-5">
			<?= form_checkbox_array_input('fields', $fmd03s, !empty($fmd03s) ? (string)array_keys($fmd03s)[0] : '') ?>
		</div>
	</div>
	<div class="form-group"><label class="col-sm-6 control-label"><?=lang('FormItem.edit_fmd07_delete_old_label')?></label>
		<div class="col-sm-5">
			<?= form_radio_input('delete_old', [1 => lang('FormItem.edit_fmd07_v_delete_old_1'), 0 => lang('FormItem.edit_fmd07_v_delete_old_0')]) ?>
		</div>
	</div>
	<div class="form-group"><label class="col-sm-6 control-label"><?=lang('FormItem.edit_fmd07_prefix_label')?></label>
		<div class="col-sm-5">
			<?= form_text_input('prefix', '', '') ?>
		</div>
	</div>
	<div class="form-group"><label class="col-sm-6 control-label"><?=lang('FormItem.edit_fmd07_suffix_label')?></label>
		<div class="col-sm-5">
			<?= form_text_input('suffix', '', '') ?>
		</div>
	</div>
	<?= form_close() ?>
</div>
