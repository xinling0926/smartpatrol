<div class="row toolbar">
	<div class="col-sm-8">
		<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->fmd4101 ?>"> <i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-4 control-label">分店</label>
				<div class="col-sm-8"><div class="form-control"><?= $data->fmd4003 ?></div></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">統計年月</label>
				<div class="col-sm-8"><div class="form-control"><?= $data->fmd4103 ?></div></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">自動計算數值</label>
				<div class="col-sm-8"><div class="form-control"><?= $data->fmd4105 ?></div></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">統計數值</label>
				<div class="col-sm-8"><div class="form-control"><?= $data->fmd4104 ?></div></div>
			</div>
		</div>
	</div>
</div>
