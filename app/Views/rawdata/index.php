<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('index_heading')?></a></li>
		<li class="pull-right">
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div class="dataTables_filter">
				<?= form_open('', ['id' => 'query_form']); ?>
				<?= form_dropdown_input('ent1001', $ent10s, null, 'width:90px;padding-left:0px;padding-right:0px;', array('onchange' => "js_linkage(this.value);")) ?>
				<?= form_dropdown_input('pad0104', $sys01s, null, 'width:100px;padding-left:0px;padding-right:0px;') ?>
				<?= form_dropdown_input('fmd0804', $fmd08s, null, 'width:90px;padding-left:0px;padding-right:0px;', array('onchange' => "js_linkage_pad0104(this.value);")) ?>
				<?= form_dropdown_input('pad0102s', $fmd07s, null, 'width:90px;padding-left:0px;padding-right:0px;') ?>
				<?= form_dropdown_input('pad0105s', $fmd02s, null, 'width:90px;padding-left:0px;padding-right:0px;') ?>
				<?= form_dropdown_input('pad0103', $dev01s, null, 'width:90px;padding-left:0px;padding-right:0px;') ?>
				<?= form_text_input('pad0109s', isset($options['pad0109s']) ? $options['pad0109s'] : '', 'width:100px', ['placeholder' => lang('search_input_pad0109s_placeholder'), 'class' => 'date']) ?>
				<?= form_text_input('pad0109e', '', 'width:100px', ['placeholder' => lang('search_input_pad0109e_placeholder'), 'class' => 'date']) ?>
				<?= form_dropdown_input('orders', $orders, null, 'width:60px;padding-left:0px;padding-right:0px;') ?>
				<button class="btn btn-primary" type="submit">
					<i class="fa fa-search"></i><?= lang('toolbar_search') ?></button>
				<button class="btn btn-default" type="reset"><i class="fa fa-eraser"></i></button>
				<?= form_close() ?>
			</div>
			<div id="pane_list">
				<?= view('rawdata/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<?= assets_js('patrol'); ?>
<?= assets_css('patrol'); ?>

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
function js_linkage(val)
{
	ajax_load_view('rawdata/linkagebyent1001/' + val, function(data){
		$("#pad0104").empty();
		var json = json_decode(data);
		$("#pad0104").append('<option value=""><?=lang('search_select_sys0101_default')?></option>');
		$.each(json.sys01s, function(index, item){
			$("#pad0104").append('<option value="' + index + '">' + item + '</option>');
		});

		$("#fmd0804").empty();
		$.each(json.fmd08s, function(index, item){
			$("#fmd0804").append('<option value="' + index + '">' + item + '</option>');
		});

		$("#pad0103").empty();
		$("#pad0103").append('<option value=""><?=lang('search_select_dev0101_default')?></option>');
		$.each(json.dev01s, function(index, item){
			$("#pad0103").append('<option value="' + index + '">' + item + '</option>');
		});
	});
}
function js_linkage_pad0104(val)
{
	ajax_post_view('rawdata/linkage_pad0104', {'fmd0804':val} , function(data){
		var json = json_decode(data);

		$("#pad0105s").empty();
		$("#pad0105s").append('<option value=""><?=lang('search_select_fmd02s_default')?></option>');
		$.each(json.fmd02s, function(index, item){
			$("#pad0105s").append('<option value="' + index + '">' + item + '</option>');
		});

		$("#pad0102s").empty();
		$("#pad0102s").append('<option value=""><?=lang('search_select_fmd07s_default')?></option>');
		$.each(json.fmd07s, function(index, item){
			$("#pad0102s").append('<option value="' + index + '">' + item + '</option>');
		});
	});
}
</script>
<!-- time0 : <?php echo $time0; ?> , time1 : <?php echo $time1; ?> , time2 : <?php echo $time2; ?> , -->