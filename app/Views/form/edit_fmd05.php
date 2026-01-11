<div class="row toolbar">
	<div class="col-sm-6">
		<?php if (isset($data) && $data): ?>
			<a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->fmd0501 ?>" data-item="fmd05" data-cuid="<?=
			csrf_hash() ?>">
				<i class="fa fa-trash-o"></i> <?=lang('toolbar_del')?></a>
		<?php endif ?>
	</div>
	<div class="col-sm-6 text-right">
		<a href="#" class="btn btn-success" onclick="save_sub_item('fmd05_form','fmd05')"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="close_tab('fmd05')"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<?php echo form_open('', array('id' => 'fmd05_form', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('fmd0501', $data->fmd0501) ?>
<?php if (!$data) echo form_hidden('fmd0502', $fmd0502) ?>
<div class="row">
	<div class="col-md-6">
		<div class="form-group"><label class="col-sm-3 control-label"><?=lang('label_fmdn03')?></label>
			<div class="col-sm-8"><?= form_text_field('fmd0503', $data) ?></div>
		</div>
		<div class="form-group"><label class="col-sm-3 control-label"><?=lang('label_fmd0304')?></label>
			<div class="col-sm-8"><?= form_text_field('fmd0504', $data) ?> </div>
		</div>
		<div class="form-group"><label class="col-sm-3 control-label"><?=lang('label_fmd0505')?></label>
			<div class="col-sm-8">
				<?= form_dropdown_field('fmd0505', $data_type_opt, $data, NULL, '', FALSE, array('onChange' => "fmd0505_onchange(this)")) ?>
			</div>
		</div>
		<div class="form-group"<?= (!isset($data) or array_search($data->fmd0505, [3, 6]) === FALSE) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0305')?></label>
			<div class="col-sm-8"><?= form_text_field('fmd0506', $data) ?> </div>
		</div>
		<div class="form-group"<?= (!isset($data) or array_search($data->fmd0505, [4, 5, 7]) === FALSE) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0508')?></label>
			<div class="col-sm-8"><?= form_textarea_field('fmd0508', $data) ?> </div>
		</div>
		<div class="form-group"<?= (!isset($data) or $data->fmd0505 != 8) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0508_1')?></label>
			<div class="col-sm-8"><?= form_text_field('fmd0508_1', $data, lang('v_fmd0508_1')) ?> </div>
		</div>
		<div class="form-group"<?= (!isset($data) or $data->fmd0505 != 8) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0508_2')?></label>
			<div class="col-sm-8"><?= form_text_field('fmd0508_2', $data, lang('v_fmd0508_2')) ?> </div>
		</div>
		<div class="form-group"<?= (!isset($data) or array_search($data->fmd0505, [1, 2, 3, 4, 7]) === FALSE) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0509')?></label>
			<div class="col-sm-8"><?= form_text_field('fmd0509', $data, '', '', FALSE, 'rows=3') ?> </div>
		</div>
		<div class="form-group"<?= (!isset($data) or array_search($data->fmd0505, [5, 6]) === FALSE) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0509')?></label>
			<div class="col-sm-8"><?= form_textarea_field('fmd0509_1', $data, '', '', FALSE, 'rows=3') ?> </div>
		</div>
		<div class="form-group"<?= (!isset($data) or $data->fmd0505 != 8) ? " style='display:none'" : '' ?>>
			<label class="col-sm-3 control-label"><?=lang('label_fmd0509')?></label>
			<div class="col-sm-8"><?= form_radio_field('fmd0509_2', ['0' => lang('v_fmd0508_1'), '1' => lang('v_fmd0508_2')], $data) ?>  </div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group"<?= (isset($data) and array_search($data->fmd0505, [4, 5, 7]) !== FALSE) ? '' : " style='display:none'" ?>>
			<label class="col-sm-4 control-label"><?=lang('label_fmd0512_1')?></label>
			<div class="col-sm-7"><?= form_textarea_field('fmd0512_1', $data) ?>
			</div>
		</div>
		<div class="form-group"<?= (isset($data) and $data->fmd0505 < 3) ? '' : " style='display:none'" ?>>
			<label class="col-sm-4 control-label"><?=lang('label_fmd0512_2')?></label>
			<div class="col-sm-3"><?= form_text_field('fmd0512_2', $data) ?></div>
			<div class="col-sm-1">~</div>
			<div class="col-sm-3"><?= form_text_field('fmd0512_3', $data) ?></div>
		</div>
		<div class="form-group"<?= (isset($data) and $data->fmd0505 == 9) ? " style='display:none'" : '' ?>>
            <label class="col-sm-4 control-label"><?=lang('label_fmd0514')?></label>
			<div class="col-sm-8"><?= form_radio_field('fmd0514', ['0' => lang('v_yn_0'), '1' => lang('v_yn_1')], $data) ?> </div>
		</div>
		<div class="form-group"><label class="col-sm-4 control-label"><?=lang('label_fmd0507')?></label>
			<div class="col-sm-8"><?= form_radio_field('fmd0507', ['0' => lang('v_yn_0'), '1' => lang('v_yn_1')], $data) ?> </div>
		</div>
		<div class="form-group"<?= (isset($data) and $data->fmd0505 == 9) ? " style='display:none'" : '' ?>>
            <label class="col-sm-4 control-label"><?=lang('label_fmd0510')?></label>
			<div class="col-sm-8"><?= form_radio_field('fmd0510', ['0' => lang('v_yn_0'), '1' => lang('v_yn_1')], $data) ?> </div>
		</div>
		<div class="form-group"<?= (isset($data) and $data->fmd0505 == 9) ? " style='display:none'" : '' ?>>
            <label class="col-sm-4 control-label"><?=lang('label_fmd0511')?></label>
			<div class="col-sm-8"><?= form_radio_field('fmd0511', ['0' => lang('v_yn_0'), '1' => lang('v_yn_1')], $data) ?> </div>
		</div>
	</div>
</div>
<?= form_close() ?>

