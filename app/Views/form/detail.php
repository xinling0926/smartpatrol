<div class="row toolbar">
	<div class="col-sm-8">
		<?php if ($data->fmd0108<=1) :?>
			<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->fmd0101 ?>"> <i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
		<?php endif ?>
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
	</div>
</div>
<div class="row form-horizontal">
	<div class="col-sm-6">
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_ent1004')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $data->ent1004 ?></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_fmd0103')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $data->fmd0103 ?></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_fmd0104')?></label>
			<div class="col-sm-8">

				<div class="form-control"><?= $data->fmd0104 ?></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_fmd0105')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $fmd0105_opt[$data->fmd0105] ?></div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_fmd0110')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= ($data->fmd0110)?lang('v_fmd0110_1'):lang('v_fmd0110_0') ?></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_fmd0107')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $data->fmd0107 ?></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('main_info_fmd0108')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $fmd0108_opt[$data->fmd0108] ?></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('table_fmd03_title')?></h3>
				<?php if ($data->fmd0108==0) :?>
					<button class="btn btn-default pull-right" onclick="edit(this)" data-item='fmd03' data-parent='<?= $data->fmd0101 ?>'>
						<i class="fa fa-fw fa-plus-square-o"></i> <?=lang('table_fmd03_add_btn')?>
					</button>
				<?php endif ?>
			</div>
			<div class="box-body">
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th><?=lang('f_common_no')?></th>
						<th><?=lang('f_field_name')?></th>
						<th><?=lang('f_field_length')?></th>
					</tr>
					<?php foreach ($fmd03s as $d) : ?>
						<tr>
							<td><?= $d->fmd0303 ?></td>
							<?php if ($data->fmd0108==0) :?>
								<td class="a" onclick='edit(this)' data-id=<?= $d->fmd0301 ?> data-item='fmd03'><?= $d->fmd0304 ?></a></td>
							<?php else : ?>
								<td><?= $d->fmd0304 ?></td>
							<?php endif ?>
							<td><?= $d->fmd0305 ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('table_fmd05_title')?></h3>
				<?php if ($data->fmd0108==0) :?>
					<button class="btn btn-default pull-right" onclick="edit(this)" data-item='fmd05' data-parent='<?= $data->fmd0101 ?>'>
						<i class="fa fa-fw fa-plus-square-o"></i> <?=lang('table_fmd05_add_btn')?>
					</button>
				<?php endif ?>
			</div>
			<div class="box-body">
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th><?=lang('f_common_no')?></th>
						<th><?=lang('f_field_name')?></th>
						<th><?=lang('f_field_shape')?></th>
						<th><?=lang('f_field_length')?></th>
						<th><?=lang('f_field_remark')?></th>
					</tr>
					<?php foreach ($fmd05s as $d) : ?>
						<tr>
							<td><?= $d->fmd0503 ?></td>
							<?php if ($data->fmd0108==0) :?>
								<td class="a" onclick='edit(this)' data-id=<?= $d->fmd0501 ?> data-item='fmd05'><?= $d->fmd0504 ?></a></td>
							<?php else: ?>
								<td><?= $d->fmd0504 ?></td>
							<?php endif ?>
							<td><?= $data_type_opt[$d->fmd0505] ?></td>
							<td><?= $d->fmd0506 ?></td>
							<td><?= $d->fmd0507?lang('v_fmd0507_1'):lang('v_fmd0507_0'); ?></td>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-sm-12">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('table_fmd02_title')?></h3>
				<?php if ($data->fmd0108==0) :?>
					<button class="btn btn-default pull-right" onclick="edit(this)" data-item='fmd02' data-parent='<?= $data->fmd0101 ?>'>
						<i class="fa fa-fw fa-plus-square-o"></i> <?=lang('table_fmd02_add_btn')?>
					</button>
				<?php endif ?>
			</div>
			<div class="box-body">
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th><?=lang('f_common_no')?></th>
						<th><?=lang('f_group_name')?></th>
						<?php if ($data->fmd0105==1) : ?>
							<th><?=lang('f_start_time')?></th>
							<th><?=lang('f_end_time')?></th>
						<?php endif ?>
					</tr>
					<?php foreach ($fmd02s as $d) : ?>
						<tr>
							<td><?= $d->fmd0203 ?></td>
							<?php if ($data->fmd0108<1) :?>
								<td class="a" onclick='edit(this)' data-id=<?= $d->fmd0201 ?> data-item='fmd02'><?= $d->fmd0204 ?></a></td>
							<?php else: ?>
								<td><?= $d->fmd0204 ?></td>
							<?php endif ?>
							<?php if ($data->fmd0105==1) : ?>
								<td><?= $d->fmd0205 ?></td>
								<td><?= $d->fmd0206 ?></td>
							<?php endif ?>
						</tr>
					<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>