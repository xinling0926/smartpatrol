<div class="box box-primary<?php if (isset($id)){ echo ' collapsed-box';}?>" id="report_condition">
	<div class="box-header with-border">
		<button type="button" class="btn btn-box-tool" data-widget="collapse">
			<i class="fa fa-minus"></i>
			<h3 class="box-title"><?= lang('box_query') ?></h3>
		</button>
	</div>
	<?php echo form_open('', array('id' => 'query_form', "class" => "form-horizontal")); ?>
	<div class="box-body">
		<div class="callout callout-danger" style="display: none;" id="message"></div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('label_report_date')?></label>
			<div class="col-lg-2 col-sm-4"><?= form_date_input('start_date', date('Y-m-d', strtotime(date('Y-m-d').' -5 day'))) ?></div>
			<div class="control-label" style="float: left"><?=lang('to')?></div>
			<div class="col-lg-2 col-sm-4"><?= form_date_input('end_date', today()) ?></div>
		</div>
	</div>
	<div class="box-footer">
		<button type="button" class="btn btn-primary pull-right" onclick="report()"><i class="fa fa-search"></i><?= lang('toolbar_search') ?></button>
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
