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
	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
	<?php if ($data) echo form_hidden('fmd4101', $data->fmd4101) ?>
	<div class="col-md-6">
		<div class="form-group">
			<label class="col-sm-4 control-label">分店</label>
			<div class="col-sm-8"><?= $data->fmd4003 ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">統計年月</label>
			<div class="col-sm-8"><?= $data->fmd4103 ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">自動計算數值</label>
			<div class="col-sm-8"><?= $data->fmd4105 ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">統計數值</label>
			<div class="col-sm-8"><?= form_text_field('fmd4104', $data) ?></div>
		</div>
	</div>
	<?= form_close() ?>
</div>
