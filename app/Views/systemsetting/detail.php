<div class="row toolbar">
	<div class="col-sm-8">
		<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->sys1001 ?>"> <i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
		<a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->sys1001 ?>" data-cuid="<?= csrf_hash() ?>">
			<i class="fa fa-trash-o"></i> <?=lang('toolbar_del')?></a>
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('f_sys1002')?></label>
				<div class="col-sm-8">
					<div class="form-control"><?= $data->sys1002 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('f_sys1003')?></label>
				<div class="col-sm-8">
					<div class="form-control"><?= $data->sys1003 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('f_sys1004')?></label>
				<div class="col-sm-8">
					<div class="form-control"><?= $data->sys1004 ?></div>
				</div>
			</div>
		</div>
	</div>
</div>