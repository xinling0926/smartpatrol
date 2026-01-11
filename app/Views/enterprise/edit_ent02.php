<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save_sub_item('edit_form','ent02')"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="close_tab('ent02')"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
	<?php if ($data) echo form_hidden('ent0201', $data->ent0201) ?>
	<?php if ($data) {
		echo form_hidden('ent0202', $data->ent0202);
	} else {
		echo form_hidden('ent0202', $ent0101);
	} ?>
	<div class="col-md-6">
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_ent0206')?></label>
			<div class="col-sm-8"><?= form_dropdown_field('ent0206', $ent0206_option, $data) ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_value')?></label>
			<div class="col-sm-8"><?= form_text_field('value', $data) ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_ent0205')?></label>
			<div class="col-sm-8"><?= form_date_field('ent0205', $data) ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('f_ent0204')?></label>
			<div class="col-sm-8"><?= form_text_field('ent0204', $data) ?>
			</div>
		</div>
	</div>
	<?= form_close() ?>
</div>

<?php assets_css('bootstrap-datepicker3', 'datepicker') ?>
<?php assets_js('bootstrap-datepicker.min', 'datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-CN.min', 'datepicker') ?>

<script type='text/javascript'>
	$('.date').datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		language: "zh-CN",
		autoclose: true,
		zIndexOffset: 1200,
		todayHighlight: true
	});
</script>