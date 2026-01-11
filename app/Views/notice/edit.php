<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="notice_save()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12"><div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div></div>
	<?php echo form_open('',array('id'=>'edit_form',"class"=>"form-horizontal")); ?>
		<?php if ($data) echo form_hidden('fmd1001',$data->fmd1001)?>
		<div class="col-md-8">
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_fmd1004')?></label>
				<div class="col-sm-4"><?= form_text_field('fmd1004', $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_fmd1005')?></label>
				<div class="col-sm-4">
					<textarea class="form-control" value="" id="fmd1005" rows="3" cols="40" name="fmd1005"><?php if(isset($data)){echo $data->fmd1005;} ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_ent1001')?></label>
				<div class="col-sm-4">
					<select class="form-control" id="ent1001" onchange="js_change_ent10(this.value);">
						<option value="0"><?=lang('select_ent10_default')?></option>
						<?php foreach($ent10s as $v){ ?>
						<option value="<?php echo $v->ent1001; ?>"><?php echo $v->ent1004; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_dev0104')?></label>
				<div class="col-sm-4">
					<select style="height:150px;" multiple="" class="form-control" id="dev0101_a">
						<?php foreach($dev01s as $v){ ?>
						<option value="<?php echo $v->dev0101; ?>" <?php if(isset($data) && !empty($data->fmd1002) && in_array($v->dev0101, explode(",", $data->fmd1002))){echo 'selected';} ?>><?php echo $v->dev0104; ?></option>
						<?php } ?>
					</select>
				</div>
				<label class="col-sm-2" style="text-align:center;">
					<br/>
					<br/>
					<a href="javascript:;" onclick="js_addtoright('#dev0101_a', '#dev0101', dev0101);"><?=lang('dev0101')?> >></a>
					<br/>
					<a href="javascript:;" onclick="js_addtoright_all('#dev0101_a', '#dev0101', dev0101);"><?=lang('add_dev0101_all_btn')?> >></a>
					<br/>
					<a href="javascript:;" onclick="js_removeformright('#dev0101', dev0101);"><?=lang('remove_dev0101_btn')?> <<</a>
				</label>
				<div class="col-sm-4"><select style="height:150px;" multiple="" class="form-control" id="dev0101" name="dev0101[]"></select></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_sys0104')?></label>
				<div class="col-sm-4">
					<select style="height:150px;" multiple="" class="form-control" id="sys0101_a">
						<?php foreach($sys01s as $v){ ?>
						<option value="<?php echo $v->sys0101; ?>" <?php if(isset($data) && !empty($data->fmd1003) && in_array($v->sys0101, explode(",", $data->fmd1003))){echo 'selected';} ?>><?php echo $v->sys0103; ?><?php echo $v->sys0104; ?></option>
						<?php } ?>
					</select>
				</div>
				<label class="col-sm-2" style="text-align:center;">
					<br/>
					<br/>
					<a href="javascript:;" onclick="js_addtoright('#sys0101_a', '#sys0101', sys0101);"><?=lang('add_dev0101_btn')?> >></a>
					<br/>
					<a href="javascript:;" onclick="js_addtoright_all('#sys0101_a', '#sys0101', sys0101);"><?=lang('add_dev0101_all_btn')?> >></a>
					<br/>
					<a href="javascript:;" onclick="js_removeformright('#sys0101', sys0101);"><?=lang('remove_dev0101_btn')?> <<</a>
				</label>
				<div class="col-sm-4"><select style="height:150px;" multiple="" class="form-control" id="sys0101" name="sys0101[]"></select></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_fmd1006')?></label>
				<div class="col-sm-4">
					<input type="radio" name="fmd1006" onclick="js_show_time(1);" value="1" <?php if(!isset($data) || (isset($data) && $data->fmd1006==1)){echo 'checked';} ?> />&nbsp;<?=lang('v_fmd1006_1')?>&nbsp;&nbsp;
					<input type="radio" name="fmd1006" onclick="js_show_time(2);" value="2" <?php if(isset($data) && $data->fmd1006==2){echo 'checked';} ?> />&nbsp;<?=lang('v_fmd1006_2')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_where_date')?></label>
				<div class="col-sm-8" id="div_fmd1007">
					<?php
						if(!isset($data) || (isset($data) && $data->fmd1006 == 1)) {
							$fmd1007	= isset($data) ? explode(",", $data->fmd1007) : array();
					?>
					<input type="checkbox" name="fmd1007[]" value ="1" <?php if(in_array(1, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('MON')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1007[]" value ="2" <?php if(in_array(2, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('TUE')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1007[]" value ="3" <?php if(in_array(3, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('WED')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1007[]" value ="4" <?php if(in_array(4, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('THU')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1007[]" value ="5" <?php if(in_array(5, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('FRI')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1007[]" value ="6" <?php if(in_array(6, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('SAT')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1007[]" value ="7" <?php if(in_array(7, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('SUN')?>
					<?php }elseif(isset($data) && $data->fmd1006 == 2){ ?>
					<?php
					$fmd1007	= explode(",", $data->fmd1007);	
					for($i=1; $i<32; $i++){
							$checked	= in_array($i, $fmd1007) ? 'checked' : '';
							echo '<input type="checkbox" name="fmd1007[]" value ="' . $i . '" ' . $checked . ' />&nbsp;' . $i . '&nbsp;&nbsp;';
							if($i == 15)
								echo '<br/>';
						}
					?>
					<?php } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_fmd1008')?></label>
				<div class="col-sm-8">
					<?php $fmd1008	= isset($data) ? explode(",", $data->fmd1008) : array(); ?>
					<input type="checkbox" name="fmd1008[]" value ="1" <?php if(in_array(1, $fmd1008)){echo 'checked'; } ?> />&nbsp;<?=lang('v_fmd1008_1_1')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1008[]" value ="2" <?php if(in_array(2, $fmd1008)){echo 'checked'; } ?> />&nbsp;<?=lang('v_fmd1008_2_1')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1008[]" value ="3" <?php if(in_array(3, $fmd1008)){echo 'checked'; } ?> />&nbsp;<?=lang('v_fmd1008_3_1')?>&nbsp;&nbsp;
					<input type="checkbox" name="fmd1008[]" value ="4" <?php if(in_array(4, $fmd1008)){echo 'checked'; } ?> />&nbsp;<?=lang('v_fmd1008_4_1')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_fmd1009')?></label>
				<div class="col-sm-8"><?= form_text_field('fmd1009', $data) ?><?=lang('str_description_1')?><br/><?=lang('str_description_2')?><br/><?=lang('str_description_3')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?=lang('label_fmd1010')?></label>
				<div class="col-sm-8">
					<input type="radio" name="fmd1010" value="1" <?php if(isset($data) && $data->fmd1010==1){echo 'checked';} ?> />&nbsp;<?=lang('v_fmd1010_1')?>&nbsp;&nbsp;
					<input type="radio" name="fmd1010" value="0" <?php if(isset($data) && $data->fmd1010==0){echo 'checked';} ?> />&nbsp;<?=lang('v_fmd1010_2')?>
				</div>
			</div>
		</div>
	<?= form_close() ?>
</div>
<script type="text/javascript">
var sys0101 = [];
var dev0101 = [];
//根据部门筛选设备与用户
function js_change_ent10(val)
{
	if(val == 0)
		return;

	$("#sys0101_a").empty();
	$("#dev0101_a").empty();
	$.ajax({
		url: 'notice/getsys012dev01/' + val,
		beforeSend: function () {
			show_loading();
		},
		error: function (request) {
			close_loading();
			show_error(request.responseText);
		},
		success: function (data) {
			close_loading();
			var dataObj = json_decode(data);
			$.each(dataObj.sys01, function(index,item){
				$("#sys0101_a").append('<option value="' + item.sys0101 + '">' + item.sys0103 + item.sys0104 + '</option>');
			});
			$.each(dataObj.dev01, function(index,item){
				$("#dev0101_a").append('<option value="' + item.dev0101 + '">' + item.dev0104 + '</option>');
			});
		}
	});
}
function js_addtoright(a, b, obj)
{
	$(a + ' :selected').each(function(i, item){
		var v = $(item).val();
		var t = $(item).text();
		
		if($.inArray(v, obj) == -1)
		{
			$(b).append('<option value="' + v + '">' + t + '</option>');
			obj.push(v);
		}
			
	});
}
function js_addtoright_all(a, b, obj)
{
	$(a + " option").each(function(i, item){
		var v = $(item).val();
		var t = $(item).text();

		if($.inArray(v, obj) == -1)
		{
			$(b).append('<option value="' + v + '">' + t + '</option>');
			obj.push(v);
		}
	});
}
function js_removeformright(a, obj)
{
	$(a + ' :selected').each(function(i, item){
		var index = $.inArray($(item).val(), obj);
		if(index != -1)
		{
			obj.splice(index,1);
			$(item).remove();
		}
	});
}
function js_show_time(id)
{
	var div = "";
	if(id == 1)
	{
		<?php
			$fmd1007	= isset($data) && $data->fmd1006 == 1 ? explode(",", $data->fmd1007) : array();
		?>
		//每周
		div += '' +
		'<input type="checkbox" name="fmd1007[]" value ="1" <?php if(in_array(1, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('MON')?>&nbsp;&nbsp;'+
		'<input type="checkbox" name="fmd1007[]" value ="2" <?php if(in_array(2, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('TUE')?>&nbsp;&nbsp;'+
		'<input type="checkbox" name="fmd1007[]" value ="3" <?php if(in_array(3, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('WED')?>&nbsp;&nbsp;'+
		'<input type="checkbox" name="fmd1007[]" value ="4" <?php if(in_array(4, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('THU')?>&nbsp;&nbsp;'+
		'<input type="checkbox" name="fmd1007[]" value ="5" <?php if(in_array(5, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('FRI')?>&nbsp;&nbsp;'+
		'<input type="checkbox" name="fmd1007[]" value ="6" <?php if(in_array(6, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('SAT')?>&nbsp;&nbsp;'+
		'<input type="checkbox" name="fmd1007[]" value ="7" <?php if(in_array(7, $fmd1007)){echo 'checked'; } ?> />&nbsp;<?=lang('SUN')?>'+
		'';
	}
	else if(id == 2)
	{
		//每月
		<?php
		$fmd1007	= isset($data) && $data->fmd1006 == 2 ? explode(",", $data->fmd1007) : array();
		for($i=1; $i<32; $i++)
		{
			$checked	= in_array($i, $fmd1007) ? 'checked' : '';
			echo "div +='<input type=\"checkbox\" name=\"fmd1007[]\" value =\"{$i}\" {$checked} />&nbsp;{$i}&nbsp;&nbsp';";
			if($i == 15)
				echo 'div += "<br/>";';
		}
		?>
	}
	$("#div_fmd1007").html(div);
}
function notice_save()
{
	$("#dev0101 option").each(function(index,item){$(item).attr("selected", "true");});
	$("#sys0101 option").each(function(index,item){$(item).attr("selected", "true");});
	save();
}
<?php
if(isset($data))
{
	if($data->fmd1002)
	{
		echo "js_addtoright('#dev0101_a', '#dev0101', dev0101);";
		echo '$("#dev0101 option").each(function(index,item){$(item).attr("selected","true");});';
	}
	if($data->fmd1003)
	{
		echo "js_addtoright('#sys0101_a', '#sys0101', sys0101);";
		echo '$("#sys0101 option").each(function(index,item){$(item).attr("selected","true");});';
	}
}
?>
</script>