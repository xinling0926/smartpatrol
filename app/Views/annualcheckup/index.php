<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('v_panelTitle')?></a></li>
		<li class="pull-right">
		<button class="btn btn-default" onclick="edit(this)"><i class="fa fa-fw fa-plus-square-o"></i> <?=lang('v_addFmd30')?></button>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div class="dataTables_filter">
				<?= form_open('', ['id' => 'query_form']); ?>
				<?= form_text_input('search', '', '', ['placeholder' => lang('v_searchCond')]) ?>
				<?= form_dropdown_input('fmd3006', $fmd3006_opt, 'all') ?>
				<button class="btn btn-primary" type="submit">
					<i class="fa fa-search"></i> <?=lang('v_query')?></button>
				<?=form_close()?>
			</div>
			<div id="pane_list">
				<?= view('annualcheckup/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<?php assets_css('patrol'); ?>
<?php assets_css('defaultTheme', 'fixed_header') ?>
<?php assets_js('jquery.fixedheadertable', 'fixed_header') ?>
<?php assets_js('patrol'); ?>
<script type='text/javascript'>
	function a(id,s){
		ajax_post_view(base_url + folder + controller + '/state', 'id='+id+'&s='+s,
			function (data) {
				setpage();
			}
		);
	}
	function q(id) {
		layer.confirm('<?=lang('v_confirmTips')?>', {
			btn: ['<?=lang('v_ok')?>', '<?=lang('v_cancel')?>'], //按钮
			title: '<?=lang('v_confirmEnable')?>',
			icon: 3
		}, function (index) {
			a(id,2);
			layer.close(index);
		}, function (index) {
			layer.close(index);
		});
	}
</script>