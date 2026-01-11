<div class="row">
	<div class="col-sm-12">
				<div>
					<form id="query_form" accept-charset="utf-8" method="post">
						<div class="pull-right" style="margin-bottom:10px;">
							<input type="text" placeholder="<?=lang('search_input_placeholder')?>" class="form-control date" value="<?php if(isset($option['fmd1004s'])){echo $option["fmd1004s"];} ?>" name="fmd1004s" style="width:150px;float:left;margin-left:1px;">
                            <a href="#" class="btn btn-primary" style="float:left;margin-left:5px;" onclick="query();"><i class="fa fa-search"></i>&nbsp;<?=lang('toolbar_search')?></a>
                        </div>
					<?=form_close() ?>
				</div>
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_common_no')?></th>
				<th><?=lang('f_ent0103')?></th>
				<th><?=lang('f_fmd1004')?></th>
				<th><?=lang('f_fmd1005')?></th>
				<th><?=lang('f_fmd1006')?></th>
				<th><?=lang('f_fmd1007')?></th>
				<th><?=lang('f_fmd1008')?></th>
				<th><?=lang('f_fmd1009')?></th>
				<th><?=lang('f_fmd1010')?></th>
			</tr>
			<?php if(isset($data))
			foreach ($data as $d){
				$fmd1008	= explode(",", $d->fmd1008);
			?>
				<tr>
					<td><?php echo ++$offset; ?></td>
					<td><?= $d->ent0103 ?></td>
					<td><a href="#" onclick="detail('<?= $d->fmd1001 ?>','<?= $d->fmd1004 ?>');"><?= $d->fmd1004 ?></a></td>
					<td><?= $d->fmd1005 ?></td>
					<td><?php if($d->fmd1006==1){echo lang('v_fmd1006_1');}elseif($d->fmd1006==2){echo lang('v_fmd1006_2');}elseif($d->fmd1006==3){echo lang('v_fmd1006_3');} ?></td>
					<td><?= $d->fmd1007 ?></td>
					<td>
					<?php
					if(in_array(1, $fmd1008))
						echo lang('v_fmd1008_1');
					if(in_array(2, $fmd1008))
						echo lang('v_fmd1008_2');
					if(in_array(3, $fmd1008))
						echo lang('v_fmd1008_3');
					if(in_array(4, $fmd1008))
						echo lang('v_fmd1008_4');
					?>
					</td>
					<td><?php if($d->fmd1009){echo lang('v_fmd1009_1') . $d->fmd1009 . lang('v_fmd1009_2');}else{echo lang('v_fmd1009_3');} ?></td>
					<td><?php if($d->fmd1010){echo lang('v_fmd1010_1');}else{echo '<font color="red">'.lang('v_fmd1010_2').'</font>';} ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<?= view('layout/query_footer', get_defined_vars()) ?>

