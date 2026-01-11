<div class="box box-primary<?php if (isset($id)) {
	echo ' collapsed-box';
} ?>" id="report_condition">
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
			<label class="col-sm-2 control-label"><?=lang('search_label_report_name')?></label>
			<?php if (count($ent10s) > 2) : ?>
				<div class="col-lg-2 col-sm-2">
					<?= form_dropdown_input('ent1001', $ent10s, '', '', ['onchange' => "select_ent10();"]) ?></div>
			<?php endif ?>
			<div class="col-lg-4 col-sm-6">
				<?= form_dropdown_input('fmd0101', $fmd01s, $fmd0101, '', ['onchange' => 'select_report(this)']) ?></div>
            <?=form_checkbox_input('close_report',1, lang('search_checkbox_close_report'),FALSE,['class'=>'col-lg-2 col-sm-2'],['onClick'=>'select_ent10();'])?>
		</div>
		<div class="form-group" hidden>
			<label class="col-sm-2 control-label"><?=lang('search_label_report_month')?></label>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('start_year', date('Y')) ?></div>
			<div class="control-label" style="float: left"><?=lang('date_year')?></div>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('start_month', date('m')) ?></div>
			<div class="control-label" style="float: left"><?=lang('date_month')?> <?=lang('to')?></div>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('end_year', date('Y')) ?></div>
			<div class="control-label" style="float: left"><?=lang('date_year')?></div>
			<div class="col-lg-1 col-sm-2"><?= form_text_input('end_month', date('m')) ?></div>
			<div class="control-label" style="float: left"><?=lang('date_month')?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('search_label_report_date')?></label>
			<div class="col-lg-2 col-sm-4"><?= form_date_input('start_date', first_day_of_month()) ?></div>
			<div class="control-label" style="float: left"><?=lang('to')?></div>
			<div class="col-lg-2 col-sm-4"><?= form_date_input('end_date', today()) ?></div>
		</div>
	</div>
	<div class="box-footer">
		<button type="button" class="btn btn-primary pull-right" onclick="show_report()"><i class="fa fa-search"></i> <?= lang('toolbar_search') ?>
		</button>
	</div>
	<?= form_close() ?>
</div>

<div class="nav-tabs-custom" hidden>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('data_list_title')?></a></li>
		<li class="pull-right"></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div id="pane_list"></div>
		</div>
	</div>
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

	function select_ent10() {
	    var close_report = $('#close_report').is(':checked');
        if ($('#ent1001').length>0) {
            var ent1001 = $('#ent1001').val();
        } else {
            var ent1001 = 0;
        }
		ajax_post_view('query_report/get_fmd01/',
            'ent1001='+ent1001+'&close_report='+close_report,
			function (data) {
				var fmd01s = json_decode(data);
				$("#fmd0101").empty();
				$.each(fmd01s, function (index, item) {
					$("#fmd0101").append('<option value="' + index + '">' + item + '</option>');
				});
			}
		);
	}

	function select_report(selectObj) {
		var fmd0101 = selectObj.options[selectObj.options.selectedIndex].value;
		if (fmd0101 == '') return;
		var url = base_url + folder + controller + '/select_report/' + fmd0101;
		ajax_load_view(url, function (data) {
			var dataObj = json_decode(data);
			if (dataObj.message = 'OK') {
				switch (dataObj.data.fmd0105) {
					case "3":
					case "4":
						$("#report_condition .form-group").eq(1).show();
						$("#report_condition .form-group").eq(2).hide();
						break;
					default:
						$("#report_condition .form-group").eq(1).hide();
						$("#report_condition .form-group").eq(2).show();
				}
			}
		});
	}

	function show_report() {
		$("#message").hide();
		var form = 'query_form';
		var url = base_url + folder + controller + '/query';
		ajax_post_view(url,
			$('#' + form).serialize(),
			function (data) {
				$("#report_condition button").eq(0).click();
				$("#pane_list").html(data);
				close_detail();
				$('.nav-tabs-custom').show();
			},
			function (message) {
				$("#message").html(message);
				$("#message").show();
			}
		);
	}

	function d(id, title) {

		if (title != null) data_title = title;
		if (id != null) data_id = id;
		var url = base_url + folder + controller + '/detail/' + data_id;
		if (get_detail_option != '') url += '?' + get_detail_option;

		ajax_load_view(url, function (data) {
			add_tab(data_title, 'detail');
			$("#pane_detail").html(data);
			if ($('#pane_detail .scroll').width()<$('#patrol_table').width()
                && $('#pane_detail .scroll').height()>($(window).height() - 100)) {
				$('#pane_detail .scroll').height($(window).height() - 100);
			}
			var t = $('#report_condition').offset().top + $('#report_condition').height();
			$(window).scrollTop(t);
		});
	}

	<?php if (isset($id)): ?>
	$(function () {
		var url = base_url + folder + controller + '/query';
		ajax_post_view(url,
			"id=<?=$id?>",
			function (data) {
				$("#pane_list").html(data);
				detail('<?=$id?>', '<?=$master->date . ' ' . $master->fmd0104?>');
				$('.nav-tabs-custom').show();
				<?php if (isset($anchor)) echo $anchor ?>
			}
		);
	});
	<?php endif ?>

</script>
<?php assets_js('patrol') ?>
<?php assets_css('patrol') ?>