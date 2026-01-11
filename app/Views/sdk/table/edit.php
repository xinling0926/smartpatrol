<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> 存档</a>
		<a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> 取消</a>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
	</div>
</div>
<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('sys0101', $data->sys3001) ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Table Name</label>
	<div class="col-sm-8"><?= form_text_field('sys3002', $data) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Comment</label>
	<div class="col-sm-8"><?= form_textarea_field('sys3003', $data) ?></div>
</div>
<?= form_close() ?>