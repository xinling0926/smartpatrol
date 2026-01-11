<div class="box box-primary" id="report_condition">
	<div class="box-header with-border"><h3 class="box-title"><?=lang('select_report_heading')?></h3></div>
	<div class="box-body">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		<?php echo form_open('', array('id' => 'report_option_form', "class" => "form-horizontal")); ?>
		<?php echo form_hidden('fmd0101', $fmd01->fmd0101) ?>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=lang('label_fmd0104')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $fmd01->fmd0104 ?></div>
			</div>
		</div>
		<?php switch ($fmd01->fmd0105) :
			case 3: ?>
				<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_month')?></label>
				<div class="col-sm-2"><?= form_text_input('year', (isset($year))?$year:date('Y')) ?></div>
				<div class="control-label" style="float: left"><?=lang('date_year')?></div>
				<div class="col-sm-2"><?= form_text_input('month', (isset($month))?$month:date('m')) ?></div>
				<div class="control-label" style="float: left"><?=lang('date_month')?></div>
				</div><?php break;
			case 4:
			case 5: ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=lang('label_month')?></label>
					<div class="col-sm-2"><?= form_text_input('year',(isset($year))?$year:date('Y')) ?></div>
					<div class="control-label" style="float: left"><?=lang('date_year')?></div>
					<div class="col-sm-2"><?= form_text_input('month', (isset($month))?$month:date('m')) ?></div>
					<div class="control-label" style="float: left"><?=lang('date_month')?></div>
				</div>
				<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-8"><?= form_radio_input('time', [1 => lang('v_times_1'), 2 => lang('v_times_2')], 1) ?></div>
				</div><?php break;
			default: ?>
				<div class="form-group">
				<label class="col-sm-3 control-label"><?=lang('label_date')?></label>
				<div class="col-sm-8"><?= form_date_input('date', ((isset($master) && isset($master->date))?$master->date:today())) ?></div>
				</div><?php break;
		endswitch; ?>
		<?php if (isset($fmd21s)) { ?>
			<?php if ($fmd21s) { ?>
				<?= form_hidden('fmd21_count', count($fmd21s)) ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=lang('label_fmd2101')?></label>
					<div class="col-sm-8">
						<?= form_radio_input('fmd2101', $fmd21s) ?>
					</div>
				</div>
			<?php } ?>
		<?php } else
			if (isset($fmd02s)) { ?>
				<?php if ($fmd02s) { ?>
					<?= form_hidden('fmd02_count', count($fmd02s)) ?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=lang('label_fmd2101')?></label>
						<div class="col-sm-8">
							<?= form_checkbox_array_input('fmd0203', $fmd02s, NULL, ['class' => 'inline'], ['class' => 'form-control-group']) ?>
							<?= form_checkbox_input('select_all', '', lang('all'), '', ['class' => 'pull-right'], ['onclick' => 's()']) ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		<?= form_close() ?>
	</div>
	<div class="box-footer">
		<a href='javascript:report_option_form.reset();' class="btn btn-default"><i class="fa fa-eraser"></i> <?= lang('toolbar_reset') ?></a>
		<button class="btn btn-primary pull-right" onclick="generate_report()"><i class="fa fa-play-circle"></i> <?=lang('generate_report_btn')?></button>
	</div>
</div>
<?php if ($use_datepicker) : ?>
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
		function s() {
			$("#checkbox_fmd0203 input").prop('checked', $('#select_all').prop('checked'));
		}
	</script>
<?php endif ?>