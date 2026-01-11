<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('Photograph.v_panelTitle')?></a></li>
		<li class="pull-right"></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div class="dataTables_filter">
				<?= form_open('', ['id' => 'query_form']); ?>
				<?= form_dropdown_input('ent1001', $ent10s, null, 'width:90px;padding-left:0px;padding-right:0px;', array('onchange' => "js_linkage(this.value);")) ?>
				<?= form_dropdown_input('pad0703', $dev01s, null, 'width:100px;padding-left:0px;padding-right:0px;') ?>
				<?= form_dropdown_input('pad0704', $sys01s, null, 'width:100px;padding-left:0px;padding-right:0px;') ?>
				<?= form_text_input('search', '', '', ['placeholder' => lang('Photograph.v_searchCond')]) ?>
				<?= form_text_input('pad0707s', isset($options['pad0707s']) ? $options['pad0707s'] : '', 'width:120px', ['placeholder' => lang('Photograph.search_input_pad0707s_placeholder'), 'class' => 'date']) ?>
				<?= form_text_input('pad0707e', isset($options['pad0707e']) ? $options['pad0707e'] : '', 'width:120px', ['placeholder' => lang('Photograph.search_input_pad0707e_placeholder'), 'class' => 'date']) ?>
				<button class="btn btn-primary" type="submit">
                    <i class="fa fa-search"></i> <?=lang('Photograph.v_query')?>
                </button>
				<button class="btn btn-default" type="reset">
					<i class="fa fa-eraser"></i>
				</button>
				<?=form_close()?>
			</div>
			<div id="pane_list">
				<?= view('photograph/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<?php assets_css('patrol'); ?>
<?php assets_css('defaultTheme', 'fixed_header') ?>
<?php assets_js('jquery.fixedheadertable', 'fixed_header') ?>
<?php assets_js('patrol'); ?>
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

function js_linkage(val) {
	ajax_load_view('photograph/linkagebyent1001/' + val, function(data){
		$("#pad0704").empty();
		var json = json_decode(data);
		$("#pad0704").append('<option value=""><?=lang('Photograph.search_select_sys0101_default')?></option>');
		$.each(json.sys01s, function(index, item){
			$("#pad0704").append('<option value="' + index + '">' + item + '</option>');
		});
	});
}
</script>