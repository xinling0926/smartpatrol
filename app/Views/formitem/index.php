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
				<?= form_text_input('search', '', '', ['placeholder' => lang('index_search_placeholder')]) ?>
				<?= form_dropdown_input('fmd0102', $dep_opt) ?>
				<button class="btn btn-primary" type="submit">
					<i class="fa fa-search"></i> <?= lang('Globe.toolbar_search') ?></button>
				<?=form_close()?>
			</div>
			<div id="pane_list">
				<?= view('formitem/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<?php assets_css('patrol'); ?>
<?php assets_css('defaultTheme', 'fixed_header') ?>
<?php assets_js('jquery.fixedheadertable', 'fixed_header') ?>
<?php assets_js('patrol'); ?>
