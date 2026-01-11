<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6">
            <?php if (isset($data) && $data): ?>
                <a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->fmd0201 ?>" data-item="fmd02" data-cuid="<?=
                csrf_hash() ?>">
                    <i class="fa fa-trash-o"></i> <?=lang('Globe.toolbar_del')?></a>
            <?php endif ?>
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
    <?php if ($data) echo form_hidden('fmd0201', $data->fmd0201) ?>
    <?php if (!$data) echo form_hidden('fmd0202', $fmd0202) ?>
    <div class="form-group">
        <label class="col-sm-4 control-label"><?=lang('f_d_fmd0203')?></label>
        <div class="col-sm-8"><?= form_text_field('fmd0203', $data) ?></div>
    </div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=lang('edit_fmd02_fmd0204_label')?></label>
		<div class="col-sm-8"><?= form_text_field('fmd0204', $data) ?> </div>
	</div>
	<?php if ($fmd01->fmd0105 == 1) : ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('edit_fmd02_fmd0205_label')?></label>
			<div class="col-sm-8"><?= form_time_field('fmd0205', $data) ?> </div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('edit_fmd02_fmd0206_label')?></label>
			<div class="col-sm-8"><?= form_time_field('fmd0206', $data) ?> </div>
		</div>
	<?php endif ?>
	<?= form_close() ?>
</div>
<?php assets_css('bootstrap-clockpicker.min', 'clockpicker') ?>
<?php assets_js('bootstrap-clockpicker.min', 'clockpicker') ?>
<script type='text/javascript'>
	$('.time').clockpicker({
		autoclose: true
	});
</script>