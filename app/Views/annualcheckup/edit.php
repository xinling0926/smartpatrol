<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('fmd3001', $data->fmd3001) ?>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_ent1004')?></label>
	<?php if (count($ent10s) > 2) : ?>
	<div class="col-sm-4">
	<?php if ($data) : ?>
		<div class="form-control"><?= $data->ent1004 ?></div>
	<?php else : ?>
		<?= form_dropdown_input('ent1001', $ent10s, '', '', ['onchange' => "select_ent10();"]) ?>
	<?php endif ?>
	</div>
	<?php endif ?>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('main_info_fmd0104')?></label>
	<div class="col-sm-4">
	<?php if ($data) : ?>
		<div class="form-control"><?= $data->fmd0104 ?><?=form_hidden('fmd0101', $data->fmd0101)?></div>
	<?php else : ?>
		<?= form_dropdown_input('fmd0101', $fmd01s, '', '', ['onchange' => '']) ?>
	<?php endif ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('v_beginDate')?></label>
	<div class="col-sm-4">
	<?php if ($data) : ?>
		<?= form_date_input('fmd3003', $data->fmd3003) ?>
	<?php else : ?>
		<?= form_date_input('fmd3003', today()) ?>
	<?php endif ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('v_endDate')?></label>
	<div class="col-sm-4">
	<?php if ($data) : ?>
		<?= form_date_input('fmd3004', $data->fmd3004) ?>
	<?php else : ?>
		<?= form_date_input('fmd3004', today()) ?>
	<?php endif ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"><?=lang('v_type')?></label>
	<div class="col-sm-10"><?= form_radio_field('fmd3005', $fmd3005_list, $data, 1) ?></div>
	</div>
</div>
<?= form_close() ?>
<?php assets_css('bootstrap-datepicker3', 'datepicker') ?>
<?php assets_js('bootstrap-datepicker.min', 'datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-TW.min', 'datepicker') ?>
<script type='text/javascript'>
	$('.date').datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		language: "zh-TW",
		autoclose: true,
		zIndexOffset: 1200,
		todayHighlight: true
	});

	function select_ent10() {
	    var close_report = $('#close_report').is(':checked');
        var ent1001= $('#ent1001').val();
		ajax_post_view('query_report/get_fmd01/',
            'ent1001='+ent1001+'&close_report='+close_report,
			function (data) {
				var fmd01s = json_decode(data);
				$("#fmd0101").empty();
				$.each(fmd01s, function (index, item) {
					$("#fmd0101").append('<option value="' + item.fmd0101 + '">' + item.fmd0104 + '</option>');
				});
			}
		);
	}
</script>