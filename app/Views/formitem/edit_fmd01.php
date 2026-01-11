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
	<?php echo form_open('', array('id' => 'fmd01_form', "class" => "form-horizontal")); ?>
	<?php echo form_hidden('fmd0101', $data->fmd0101) ?>
	<div class="form-group">
        <label class="col-sm-4 control-label"><?=lang('main_info_ent1004')?></label>
	    <div class="col-sm-8"><?= form_dropdown_field('fmd0102', $dept, $data, '', '') ?></div>
	</div>
    <div class="form-group">
        <label class="col-sm-4 control-label"><?=lang('main_info_fmd0103')?></label>
	    <div class="col-sm-8"><?= form_text_field('fmd0103', $data) ?></div>
	</div>
    <div class="form-group">
        <label class="col-sm-4 control-label"><?=lang('main_info_fmd0104')?></label>
        <div class="col-sm-8"><?= form_text_field('fmd0104', $data) ?></div>
	</div>
	<?= form_close() ?>
</div>