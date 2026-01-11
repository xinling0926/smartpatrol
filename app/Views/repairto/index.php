<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('index_heading')?></a></li>
		<li class="pull-right">
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div id="pane_list">
				<?= view('repairto/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<?= assets_js('patrol'); ?>