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
	<?php echo form_open('', array('id' => 'fmd08_form', "class" => "form-horizontal")); ?>
	<?php if ($data) echo form_hidden('fmd0801', $data->fmd0801) ?>
	<?php if (!$data) echo form_hidden('fmd0802', $fmd0802) ?>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.edit_fmd08_fmd0803_label')?></label>
		<div class="col-sm-4"><?= form_text_field('fmd0803', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.edit_fmd08_fmd0804_label')?></label>
		<div class="col-sm-8"><?= form_text_field('fmd0804', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.edit_fmd08_fmd0805_label')?></label>
		<div class="col-sm-8"><?= form_radio_field('fmd0805', ['0' => lang('Globe.v_yn_0'), '1' => lang('Globe.v_yn_1')], $data) ?> </div>
	</div>
	<?= form_close() ?>
</div>