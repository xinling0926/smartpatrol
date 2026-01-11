<div class="box box-primary" id="report_condition">
	<div class="box-header with-border">
		<button type="button" class="btn btn-box-tool" data-widget="collapse">
			<i class="fa fa-minus"></i>
			<h3 class="box-title"><?= lang('Globe.box_query') ?></h3>
		</button>
	</div>
	<?php echo form_open('', array('id' => 'query_form', "class" => "form-horizontal")); ?>
	<div class="box-body">
		<div class="callout callout-danger" style="display: none;" id="message"></div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('QueryReportItem.label_report_name')?></label>
			<div class="col-lg-4 col-sm-8"><?= form_dropdown_input('fmd0101', $fmd01s, '', '', ['onchange' => 'select_report(this)']) ?></div>
		</div>
		<div class="form-group" hidden>
			<label class="col-sm-2 control-label"><?=lang('QueryReportItem.label_report_month')?></label>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('start_year', date('Y')) ?></div>
			<div class="control-label" style="float: left"><?=lang('Globe.date_year')?></div>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('start_month', date('m')) ?></div>
			<div class="control-label" style="float: left"><?=lang('Globe.date_month')?> <?=lang('Globe.to')?></div>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('end_year', date('Y')) ?></div>
			<div class="control-label" style="float: left"><?=lang('Globe.date_year')?></div>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('end_month', date('m')) ?></div>
			<div class="control-label" style="float: left"><?=lang('Globe.date_month')?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('QueryReportItem.label_report_time')?></label>
			<div class="col-lg-2 col-sm-4"><?= form_date_input('start_date', first_day_of_month()) ?></div>
			<div class="control-label" style="float: left"><?=lang('Globe.to')?></div>
			<div class="col-lg-2 col-sm-4"><?= form_date_input('end_date', today()) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('QueryReportItem.label_state')?></label>
			<div class="col-lg-2 col-sm-4"><?= form_dropdown_input('state', ['' => lang('Globe.all'), 1 => lang('QueryReportItem.v_state_1'), 2 => lang('QueryReportItem.v_state_2'), 3 => lang('QueryReportItem.v_state_3')]) ?>
			</div>
		</div>
	</div>
	<div class="box-footer">
		<button type="button" class="btn btn-primary pull-right" onclick="show_report()"><i class="fa fa-search"></i> <?= lang('Globe.toolbar_search') ?>
		</button>
	</div>
	<?= form_close() ?>
</div>
<div id="pane_list"></div>

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

	function select_report(selectObj) {
		var fmd0101 = selectObj.options[selectObj.options.selectedIndex].value;
		if (fmd0101 == '') return;
		var url = base_url + folder + controller + '/select_report/' + fmd0101;
		ajax_load_view(url, function (data) {
			var dataObj = json_decode(data);
			if (dataObj.message = 'OK') {
				switch (dataObj.fmd01.fmd0105) {
					case "3":
					case "4":
						$("#report_condition .form-group").eq(1).show();
						$("#report_condition .form-group").eq(2).hide();
						break;
					default:
						$("#report_condition .form-group").eq(1).hide();
						$("#report_condition .form-group").eq(2).show();
				}
				for (i = $("#report_condition .form-group").length; i > 4; i--) {
					$("#report_condition .form-group").eq(4).remove();
				}
				$("#report_condition .box-body").append(dataObj.option);
			}
		});
	}

	function show_report() {
		$("#message").hide();
		ajax_post_view(base_url + folder + controller + '/query',
			$('#query_form').serialize(),
			function (data) {
				$("#pane_list").html(data);
				$('#report_condition button').eq(0).click();
			},
			function (message) {
				$("#message").html(message);
				$("#message").show();
			}
		);
	}

</script>
