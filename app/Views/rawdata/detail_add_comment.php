<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-12 text-right">
			<button class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'isoN_form', "class" => "form-horizontal")); ?>
	<?php echo form_hidden('fmd0106', $data->fmd0106) ?>
    <?php echo form_hidden('fmd0201', $data->fmd0201) ?>
	<?php echo form_hidden('fmd0203', $data->fmd0203) ?>
    <?php echo form_hidden('fmd0701', $data->fmd0701) ?>
    <?php echo form_hidden('master_id', $data->master_id) ?>
	<div class="form-group">
		<label class="col-md-3 control-label"><?= lang('f_content') ?></label>
		<div class="col-md-9"><?= form_textarea_field('comments', null, '', '', FALSE, 'maxlength=60') ?> </div>
		<label class="col-md-3 control-label"><?= lang('f_add_auto_comments') ?></label>
		<div class="col-md-9"><?= form_radio_input('auto_comment', [0 => lang('f_no'), 1 => lang('f_yes')], 0, '', '') ?> </div>
	</div>
	<?php echo form_close(); ?>
</div>
<?php assets_css('bootstrap-clockpicker.min', 'clockpicker') ?>
<?php assets_js('bootstrap-clockpicker.min', 'clockpicker') ?>
<script type='text/javascript'>
    $('.time').clockpicker({
        autoclose: true
    });
</script>