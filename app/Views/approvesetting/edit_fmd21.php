<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6">
			<?php if ($data) :?><button class="btn btn-danger" onclick="del(this)" data-id="<?= $data->fmd2101 ?>" data-item="fmd21" data-cuid="<?=
			csrf_hash() ?>"><i class="fa fa-trash-o"></i> <?=lang('toolbar_del')?></button><?php endif ?>
		</div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></button>
			<button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'fmd21_form', "class" => "form-horizontal")); ?>
	<?php if ($data) echo form_hidden('fmd2101', $data->fmd2101) ?>
	<?php if (!$data) echo form_hidden('fmd0101', $fmd0101) ?>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('f_fmd2103')?></label>
		<div class="col-sm-4"><?= form_text_field('fmd2103', $data) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('f_fmd2104')?></label>
		<div class="col-sm-8"><?= form_text_field('fmd2104', $data, '', '', FALSE, ['maxlength' => 20]) ?> </div>
	</div>
	<div class="form-group"><label class="col-sm-3 control-label"><?=lang('f_fmd2105')?></label>
		<div class="col-sm-8"><?= form_checkbox_array_field('fmd2105', $fmd02s, $data, '', ['class' => 'inline']) ?> </div>
	</div>
	<?= form_close() ?>
</div>