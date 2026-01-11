<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6">

		</div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-success" onclick="notice_save()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></button>
			<button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'fmd31_form', "class" => "form-horizontal")); ?>
	<?php echo form_hidden('fmd0101', $data->fmd0101) ?>
	<?php echo form_hidden('sys0101v', '') ?>
	<div class="form-group">
		<label class="col-sm-2 control-label">部門篩選</label>
		<div class="col-sm-10">
			<select class="form-control" id="ent1001" onchange="js_change_ent10(this.value);">
				<option value="0">選擇部門</option>
				<?php
				foreach($ent10s as $v) :
					$is_select = ($v->ent1001 == $ent1001)? ' selected' : '';
				?>
				<option value="<?php echo $v->ent1001; ?>"<?= $is_select ?>><?php echo $v->ent1004; ?></option>
				<?php
				endforeach
				?>
			</select>
		</div>
	</div>
	<div class="form-group">
        <label class="col-sm-2 control-label">發送 E-Mail 通知的條件</label>
	    <div class="col-sm-10">
		<?php foreach($errTypes as $errType): ?>
			<?= form_checkbox_input('fmd3103[]', $errType->id, $errType->name, $data->fmd3103[$errType->id], ['class'=>'inline', 'style'=>'display: flex; align-items: center;']) ?>
		<?php endforeach ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">人員指定</label>
		<div class="col-sm-4">
			<select style="height:250px;" multiple="" class="form-control" id="sys0101_a" name="sys0101_a[]">
			<?php foreach($sys01s as $v): ?>
				<option value="<?= $v->sys0101 ?>"><?= $v->sys0103 . $v->sys0104 ?></option>
			<?php endforeach ?>
			</select>
		</div>
		<label class="col-sm-2" style="text-align:center;">
			<br/>
			<br/>
			<a href="javascript:;" onclick="js_addtoright('#sys0101_a', '#sys0101', sys0101);">加入 >></a>
			<br/>
			<a href="javascript:;" onclick="js_addtoright_all('#sys0101_a', '#sys0101', sys0101);">全加 >></a>
			<br/>
			<a href="javascript:;" onclick="js_removeformright('#sys0101', sys0101);">移除 <<</a>
		</label>
		<div class="col-sm-4">
			<select style="height:250px;" multiple="" class="form-control" id="sys0101" name="sys0101[]">
			<?php if (count($data->fmd3104)) : ?>
			<?php foreach($data->fmd3104 as $v) : ?>
				<option value="<?= $v->sys0101 ?>"><?= $v->name ?></option>
			<?php endforeach; ?>
			<?php endif; ?>
			</select>
		</div>
	</div>
	<?= form_close() ?>
</div>
<script>
var sys0101 = [];
var dev0101 = [];
//根据部门筛选设备与用户
function js_change_ent10(val)
{

	$("#sys0101_a").empty();
	$.ajax({
		url: 'form_item/getsys012dev01/' + val,
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

function notice_save() {
	$("input[name=sys0101v]").val( sys0101.toString() );
	save_and_close_dialog();
}
</script>
<script>
<?php
if (count($data->fmd3104)) {
	foreach($data->fmd3104 as $val) {
		echo 'sys0101.push("'.$val->sys0101.'");' . PHP_EOL;
	}
}
?>
</script>