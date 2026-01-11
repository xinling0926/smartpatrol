<div class="row toolbar">
	<div class="col-sm-6">
		<?php if (isset($data) && $data): ?>
			<a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->fmd0201 ?>" data-item="fmd02" data-cuid="<?=
			csrf_hash() ?>">
				<i class="fa fa-trash-o"></i> <?=lang('toolbar_del')?></a>
		<?php endif ?>
	</div>
	<div class="col-sm-6 text-right">
		<a href="#" class="btn btn-success" onclick="save_sub_item('fmd02_form','fmd02')"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="close_tab('fmd02')"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<?php echo form_open('', array('id' => 'fmd02_form', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('fmd0201', $data->fmd0201) ?>
<?php if (!$data) echo form_hidden('fmd0202', $fmd0202) ?>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('label_fmdn03')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0203', $data) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('label_fmd0204')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0204', $data) ?> </div>
</div>
<?php if ($fmd01->fmd0105 == 1) : ?>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('label_fmd0205')?></label>
		<div class="col-sm-4"><?= form_time_field('fmd0205', $data) ?> </div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('label_fmd0206')?></label>
		<div class="col-sm-4"><?= form_time_field('fmd0206', $data) ?> </div>
	</div>
<?php endif ?>
<?= form_close() ?>
<?php assets_css('bootstrap-clockpicker.min', 'clockpicker') ?>
<?php assets_js('bootstrap-clockpicker.min', 'clockpicker') ?>
<script type='text/javascript'>
	$('.time').clockpicker({
		autoclose: true
	});
</script>
