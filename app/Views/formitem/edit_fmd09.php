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
	<?php echo form_open('', array('id' => 'fmd09_form', "class" => "form-horizontal")); ?>
	<?php if ($data) echo form_hidden('fmd0901', $data->fmd0901) ?>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.add_fmd09_fmd0904_label')?></label>
		<div class="col-sm-3"><?= form_text_field('fmd0904', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.add_fmd09_fmd0905_label')?></label>
		<div class="col-sm-4"><?= form_text_field('fmd0905', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.add_fmd09_fmd0908_label')?></label>
		<div class="col-sm-7"><?= form_text_field('fmd0908', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.add_fmd09_fmd0909_label')?></label>
		<div class="col-sm-7"><?= form_text_field('fmd0909', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.add_fmd09_fmd0910_label')?></label>
		<div class="col-sm-4"><?= form_text_field('fmd0910', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('FormItem.add_fmd09_fmd0911_label')?></label>
		<div class="col-sm-4"><?= form_text_field('fmd0911', $data) ?> </div>
	</div>
	<?= form_close() ?>
</div>