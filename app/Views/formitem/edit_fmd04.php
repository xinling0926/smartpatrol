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
	<?php echo form_open('', array('id' => 'fmd04_form', "class" => "form-horizontal")); ?>
	<?php echo form_hidden('fmd0401', $data->fmd0401) ?>
	<?php if ($data->fmd0407==0) echo form_hidden('fmd0407', 2) ?>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=lang('FormItem.edit_fmd04_fmd0404_label')?></label>
		<div class="col-sm-8"><?= form_text_field('fmd0404', $data) ?></div>
	</div>
	<?= form_close() ?>
</div>