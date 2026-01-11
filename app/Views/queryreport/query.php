<?php
	$showapprove = config('App')->showapprove ?? false;
?>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_common_no')?></th>
				<th><?=lang('f_fmd0102')?></th>
				<th><?=lang('f_fmd0104')?></th>
				<th><?=lang('f_date')?></th>
				<th><?=lang('f_sys0103')?></th>
				<th><?=lang('f_datetime')?></th>
				<?php if ($showapprove):?>
				<th><?=lang('f_sign_result');?></th>
				<?php endif;?>
				<th><?=lang('f_error_count')?></th>
				<th><?=lang('f_miss_count')?></th>
			</tr>
			<?php foreach ($data as $d) : ?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><?= $fmd01->ent1004 ?></td>
					<td><?= $d->fmd0104 ?></td>
					<td><a href="javascript:d('<?=$d->fmd0101.'-'.$d->id ?>','<?= $d->date.' '.$d->fmd0104 ?>')"><?= $d->date ?></a></td>
					<td><?= $d->sys0103.$d->sys0104 ?></td>
					<td><?= $d->datetime ?></td>
					<?php if ($showapprove):?>
					<td><?php
						if ((intval($d->c08)>=1) && (intval($d->c08)<=99)){
							echo '送簽中';
						}elseif (intval($d->c08)==100){
							echo '簽核完成';
						}elseif (intval($d->c08==101)){
							echo '簽核退回';
						}else{
							echo '未送簽';
						}
						?></td>
					<?php endif;?>
					<td><?= $d->error_count ?></td>
					<td><?= $d->miss_count ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>
<?= view('layout/query_footer', get_defined_vars()) ?>


