<?php
	$_permissions = session('permissions') ?? '';
	if (is_array($_permissions)) {
		$_permArray = $_permissions;
	} else {
		$_permArray = $_permissions ? explode(",", $_permissions) : [];
	}
?>
<div class="box box-warning">
	<div class="box-header">
		<h3 class="box-title"><?=lang('Home.repair_list_title')?></h3>
	</div>
	<div class="box-body">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th>&nbsp;</th>
				<th style="text-align:center;"><?=lang('Home.f_pad030_rows')?></th>
				<th style="text-align:center;width:150px;"><?=lang('Home.f_pad030_time')?></th>
			</tr>
			<?php if(in_array(23, $_permArray)) : ?>
			<tr>
				<td><a href="<?= base_url('repair') ?>" class="small-box-footer"><?=lang('Home.repair_state_1')?></a></td>
				<td style="text-align:center;"><?= $repair['pad030']['rows']; ?></td>
				<td style="text-align:center;"><?= $repair['pad030']['time']; ?></td>
			</tr>
			<?php endif; ?>

			<?php if(in_array(31, $_permArray)) : ?>
			<tr>
				<td><a href="<?= base_url('repair_to') ?>" class="small-box-footer"><?=lang('Home.repair_state_2')?></a></td>
				<td style="text-align:center;"><?= $repair['pad051']['rows']; ?></td>
				<td style="text-align:center;"><?= $repair['pad051']['time']; ?></td>
			</tr>
			<?php endif; ?>

			<?php if(in_array(30, $_permArray)) : ?>
			<tr>
				<td><a href="<?= base_url('Repair_from') ?>?pad0513=3" class="small-box-footer"><?=lang('Home.repair_state_3')?></a></td>
				<td style="text-align:center;"><?= $repair['pad053']['rows']; ?></td>
				<td style="text-align:center;"><?= $repair['pad053']['time']; ?></td>
			</tr>
			<?php endif; ?>

			<?php if(in_array(31, $_permArray)) : ?>
			<tr>
				<td><a href="<?= base_url('repair_to') ?>?pad0513=4" class="small-box-footer"><?=lang('Home.repair_state_4')?></a></td>
				<td style="text-align:center;"><?= $repair['pad054']['rows']; ?></td>
				<td style="text-align:center;"><?= $repair['pad054']['time']; ?></td>
			</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>