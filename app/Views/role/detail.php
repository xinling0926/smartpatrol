<div class="row toolbar">
	<div class="col-sm-8">
		<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->rol0101 ?>"> <i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
		<a href="#" class="btn btn-danger" onclick="del(this)" data-id="<?= $data->rol0101 ?>" data-cuid="<?= csrf_hash() ?>">
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
				<label class="col-sm-4 control-label"><?=lang('f_rol0103')?></label>
				<div class="col-sm-8">
					<div class="form-control"><?= $data->rol0103 ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('f_rol0104')?></label>
				<div class="col-sm-8">
					<div class="form-control" style="height: 114px"><?= str_replace("\n", "<br>", $data->rol0104) ?></div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('f_rol0106')?></label>
				<div class="col-sm-8">
					<div class="form-control"><?= $rol0106_option[$data->rol0106] ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('f_rol0105')?></h3>
			</div>
			<div class="box-body">
				<?= form_checkbox_array_field('rol0105', $sys04s, $data, '', '', '', 'disabled') ?>
			</div>
		</div>

	</div>
</div>