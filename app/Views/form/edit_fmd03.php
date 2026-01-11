<div class="row toolbar">
	<div class="col-sm-6">
		<?php if (isset($data) && $data): ?>
			<a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->fmd0301 ?>" data-item="fmd03" data-cuid="<?=
			csrf_hash() ?>">
				<i class="fa fa-trash-o"></i> <?=lang('toolbar_del')?></a>
		<?php endif ?>
	</div>
	<div class="col-sm-6 text-right">
		<a href="#" class="btn btn-success" onclick="save_sub_item('fmd03_form','fmd03')"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="close_tab('fmd03')"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<?php echo form_open('', array('id' => 'fmd03_form', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('fmd0301', $data->fmd0301) ?>
<?php if (!$data) echo form_hidden('fmd0302', $fmd0302) ?>
<div class="form-group"><label class="col-sm-2 control-label"><?=lang('label_fmdn03')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0303', $data) ?></div>
</div>
<div class="form-group"><label class="col-sm-2 control-label"><?=lang('label_fmd0304')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0304', $data) ?> </div>
</div>
<div class="form-group"><label class="col-sm-2 control-label"><?=lang('label_fmd0305')?></label>
	<div class="col-sm-4"><?= form_text_field('fmd0305', $data) ?> </div>
</div>
<?= form_close() ?>
