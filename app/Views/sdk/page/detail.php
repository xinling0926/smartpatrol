<div class="row toolbar">
	<div class="col-sm-8">
		<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->sys0401 ?>"> <i class="fa fa-edit"></i> 修改</a>
		<a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->sys0401 ?>"> <i class="fa fa-trash-o"></i> 删除</a>
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> 关闭</a>
	</div>
</div>
<div class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-4 control-label">Title</label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->sys0402 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Folder</label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->sys0409 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Controller</label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->sys0403 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Action</label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->sys0404 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">权限设定</label>
		<div class="col-sm-8">
			<div class="form-control"><?= $sys0406_opt[$data->sys0406] ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">系统管理员权限</label>
		<div class="col-sm-8">
			<div class="form-control"><?= ($data->sys0410) ? '是' : '否' ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">功能模组</label>
		<div class="col-sm-8">
			<div class="form-control"><?= (is_null($data->sys0411)) ? '标准版' : $of[$data->sys0411] ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">显示顺序</label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->sys0405 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">状态</label>
		<div class="col-sm-8">
			<div class="form-control"><?php if ($data->sys0408) {
					echo "启用";
				} else {
					echo "停用";
				} ?></div>
		</div>
	</div>
</div>