<div class="row">
	<div class="col-sm-12">
				<div>
					<form id="query_form" accept-charset="utf-8" method="post" target="_blank">
						<div class="pull-right" style="margin-bottom:10px;">
							<select class="form-control" id="dev0101" name="dev0101" style="float:left;width:150px;margin-right:5px;">
								<?php foreach($dev01s as $k1 => $v1): ?>
								<option value="<?php echo $k1; ?>" <?php if(isset($option) && isset($option['dev0101']) && $option['dev0101'] == $k1){echo 'selected';} ?>><?php echo $v1; ?></option>
								<?php endforeach; ?>
							</select>&nbsp;
							<select class="form-control" id="sys0101" name="sys0101" style="float:left;width:150px;margin-right:5px;">
								<?php foreach($sys01s as $k1 => $v1): ?>
								<option value="<?php echo $k1; ?>" <?php if(isset($option) && isset($option['sys0101']) && $option['sys0101'] == $k1){echo 'selected';} ?>><?php echo $v1; ?></option>
								<?php endforeach; ?>
							</select>
							<select class="form-control" id="pad0306" name="pad0306" style="float:left;width:150px;margin-right:5px;">
								<option value=""><?=lang('select_state')?></option>
								<option value="0"<?php if(isset($option['pad0306']) && $option['pad0306'] == 0){echo 'selected';} ?>><?=lang('v_pad0306_0')?></option>
								<option value="1"<?php if(isset($option['pad0306']) && $option['pad0306'] == 1){echo 'selected';} ?>><?=lang('v_pad0306_1')?></option>
								<option value="2"<?php if(isset($option['pad0306']) && $option['pad0306'] == 2){echo 'selected';} ?>><?=lang('v_pad0306_2')?></option>
								<option value="3"<?php if(isset($option['pad0306']) && $option['pad0306'] == 3){echo 'selected';} ?>><?=lang('v_pad0306_3')?></option>
								<option value="4"<?php if(isset($option['pad0306']) && $option['pad0306'] == 4){echo 'selected';} ?>><?=lang('v_pad0306_4')?></option>
								<option value="5"<?php if(isset($option['pad0306']) && $option['pad0306'] == 5){echo 'selected';} ?>><?=lang('v_pad0306_5')?></option>
							</select>&nbsp;
							<input type="text" placeholder="<?=lang('input_placeholder_start_date')?>" class="form-control date" value="<?php if(isset($option['pad03z2s'])){echo $option["pad03z2s"];} ?>" name="pad03z2s" style="width:150px;float:left;margin-left:5px;">&nbsp;
							<input type="text" placeholder="<?=lang('input_placeholder_end_date')?>" class="form-control date" value="<?php if(isset($option['pad03z2e'])){echo $option["pad03z2e"];} ?>" name="pad03z2e" style="width:150px;float:left;margin-left:10px;">
                            <a href="#" class="btn btn-primary" style="float:left;margin-left:5px;" onclick="query();"><i class="fa fa-search"></i>&nbsp;<?=lang('toolbar_search')?></a>
							<a href="#" class="btn btn-primary" onclick="$('#query_form').attr('action', base_url + 'repair/export');$('#query_form').submit();" style="float:left;margin-left:5px;"><i class="fa fa-cloud-download"></i> <?=lang('export_excel_btn')?></a>
                        </div>
					<?=form_close() ?>
				</div>
		<table class="table table-striped table-hover dataTable">
			<tbody>
			<tr>
				<th><?=lang('f_dev0104')?></th>
				<th><?=lang('f_pad0303')?></th>
				<th><?=lang('f_pad0306')?></th>
				<th><?=lang('f_sys0104')?></th>
				<th><?=lang('f_pad03z2')?></th>
			</tr>
			<?php if(isset($data))
			foreach ($data as $d) : ?>
				<tr>
					<td><?= $d->dev0104 ?></td>
					<td><a href="#" onclick="detail('<?= $d->pad0301 ?>','<?= $d->pad0303 ?>');"><?= $d->pad0303 ?></a></td>
					<td><?php if($d->pad0306==0){echo '<font color="red">'.lang('v_pad0306_0').'</font>';}elseif($d->pad0306==1){echo lang('v_pad0306_1');}elseif($d->pad0306==2){echo lang('v_pad0306_2');}elseif($d->pad0306==3){echo lang('v_pad0306_3');}elseif($d->pad0306==4){echo lang('v_pad0306_4');}elseif($d->pad0306==5){echo '<font color="#2a7026">'.lang('v_pad0306_5').'</font>';} ?></td>
					<td><?= $d->sys0103 ?><?= $d->sys0104 ?></td>
					<td><?= $d->pad03z2 ?></td>
				</tr>
			<?php endforeach ?>
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

