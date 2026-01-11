<div class="row">
	<div class="col-sm-12">
				<div>
					<form id="query_form" accept-charset="utf-8" method="post" target="_blank">
						<div class="pull-right" style="margin-bottom:10px;">
							<select class="form-control" id="pad0513" name="pad0513" style="float:left;width:150px;margin-right:5px;">
								<option value=""><?=lang('select_state')?></option>
								<option value="1"<?php if(isset($option['pad0513']) && $option['pad0513'] == 1){echo 'selected';} ?>><?=lang('v_pad0513_1')?></option>
								<option value="2"<?php if(isset($option['pad0513']) && $option['pad0513'] == 2){echo 'selected';} ?>><?=lang('v_pad0513_2')?></option>
								<option value="3"<?php if(isset($option['pad0513']) && $option['pad0513'] == 3){echo 'selected';} ?>><?=lang('v_pad0513_3')?></option>
								<option value="4"<?php if(isset($option['pad0513']) && $option['pad0513'] == 4){echo 'selected';} ?>><?=lang('v_pad0513_4')?></option>
								<option value="5"<?php if(isset($option['pad0513']) && $option['pad0513'] == 5){echo 'selected';} ?>><?=lang('v_pad0513_5')?></option>
							</select>&nbsp;
							<input type="text" placeholder="<?=lang('input_placeholder_start_date')?>" class="form-control date" value="<?php if(isset($option['pad0509s'])){echo $option["pad0509s"];} ?>" name="pad0509s" style="width:150px;float:left;margin-left:5px;">&nbsp;
							<input type="text" placeholder="<?=lang('input_placeholder_end_date')?>" class="form-control date" value="<?php if(isset($option['pad0509e'])){echo $option["pad0509e"];} ?>" name="pad0509e" style="width:150px;float:left;margin-left:10px;">
                            <a href="#" class="btn btn-primary" style="float:left;margin-left:5px;" onclick="query();"><i class="fa fa-search"></i>&nbsp;<?=lang('toolbar_search')?></a>
							<a href="#" class="btn btn-primary" onclick="$('#query_form').attr('action', base_url + 'repair-to/export');$('#query_form').submit();" style="float:left;margin-left:5px;"><i class="fa fa-cloud-download"></i> <?=lang('export_excel_btn')?></a>
                        </div>
					<?=form_close() ?>
				</div>
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_pad0504')?></th>
				<th><?=lang('f_pad0503')?></th>
				<th><?=lang('f_ent1004')?></th>
				<th><?=lang('f_sys0104')?></th>
				<th><?=lang('f_pad0509')?></th>
				<th><?=lang('f_cod0204')?></th>
				<th><?=lang('f_pad0511')?></th>
				<th><?=lang('f_pad0518')?></th>
				<th><?=lang('f_pad0512')?></th>
				<th><?=lang('f_pad0513')?></th>
			</tr>
			<?php  foreach ($data as $d){ ?>
				<tr>
					<td><?= $d->pad0504 ?></td>
					<td><a href="#" onclick="detail('<?= $d->pad0501 ?>','<?= $d->pad0504 ?>');"><?= $d->pad0503 ?></a></td>
					<td><?= $d->ent1004 ?></td>
					<td><?= $d->sys0103 ?><?= $d->sys0104 ?></td>
					<td><?= $d->pad0509 ?></td>
					<td><?= $d->cod0204 ?></td>
					<td><?= $pad0511_opt[$d->pad0511] ?></td>
					<td><?= $d->pad0518 ?></td>
					<td><?= isset($pad0512_opt[$d->pad0512]) ? $pad0512_opt[$d->pad0512] : '' ?></td>
					<td><?php if($d->pad0513==1){echo lang('v_pad0513_1');}elseif($d->pad0513==2){echo lang('v_pad0513_2');}elseif($d->pad0513==3){echo lang('v_pad0513_3');}elseif($d->pad0513==4){echo lang('v_pad0513_4');}elseif($d->pad0513==5){echo lang('v_pad0513_5');} ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<?php assets_css('css/bootstrap-datepicker3', 'bootstrap-datepicker') ?>
<?php assets_js('js/bootstrap-datepicker.min', 'bootstrap-datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-CN.min', 'bootstrap-datepicker') ?>

<script type='text/javascript'>
    $('#query_form .date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "zh-CN",
        autoclose: true,
        todayHighlight: true
    });
</script>

<?= view('layout/query_footer', get_defined_vars()) ?>
