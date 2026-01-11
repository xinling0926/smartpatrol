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
<?php if ($data) echo form_hidden('sys0401', $data->sys0401) ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Title</label>
	<div class="col-sm-8"><?= form_text_field('sys0402', $data) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Folder</label>
	<div class="col-sm-8"><?= form_text_field('sys0409', $data) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Controller</label>
	<div class="col-sm-8"><?= form_text_field('sys0403', $data) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">Action</label>
	<div class="col-sm-8"><?= form_text_field('sys0404', $data) ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">权限设定</label>
	<div class="col-sm-8"><?= form_radio_field('sys0406', $sys0406_opt, $data, '1') ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">系统管理员权限</label>
	<div class="col-sm-8"><?= form_radio_field('sys0410', array('0' => '否', '1' => '是'), $data, '0') ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">功能模组</label>
	<div class="col-sm-8"><?= form_radio_field('sys0411', $of, $data, '') ?></div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">显示顺序</label>
	<div class="col-sm-8"><?= form_text_field('sys0405', $data, '0') ?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">状态</label>
	<div class="col-sm-8">
		<div class="radio">
			<label>
				<input type="radio" name="sys0408" id="sys04080" value="0"<?php if ($data && $data->sys0408 == 0) echo " checked" ?>>
				停用
			</label>
			<label>
				<input type="radio" name="sys0408" id="sys04081" value="1"<?php if ($data == NULL || $data->sys0408 == 1) echo " checked" ?>>
				启用
			</label>
		</div>
	</div>
</div>
</form>