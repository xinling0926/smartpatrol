<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('index_heading')?></a></li>
		<li class="pull-right"> </li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div class="dataTables_filter">
				<?= form_open('', ['id' => 'query_form']); ?>
				<?= form_text_input('search', '', '', ['placeholder' => lang('search_hint')]) ?>
				<?= form_dropdown_input('fmd0102', $ent10s) ?>
				<button class="btn btn-primary" type="submit">
					<i class="fa fa-search"></i><?= lang('toolbar_search') ?></button>
				<?=form_close()?>
			</div>
			<div id="pane_list">
				<?= view('approvesetting/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<?= assets_js('patrol'); ?>