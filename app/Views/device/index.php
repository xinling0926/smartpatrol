<div class="row">
	<div class="col-xs-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('index_heading')?></a></li>
				<li class="pull-right"></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pane1">
					<div class="dataTables_filter">
						<?= form_open('', ['id' => 'query_form']); ?>
						<?= form_dropdown_input('dev0103', $ent10s) ?>
						<?= form_text_input('search', '', '', ['placeholder' => lang('search_input_placeholder')]) ?>
						<?= form_dropdown_input('dev0106', [1 => lang('v_start'), 2 => lang('v_stop'), '' => lang('all')], $dev0106) ?>
						<button class="btn btn-primary" type="submit">
							<i class="fa fa-search"></i><?= lang('toolbar_search') ?></button>
						<?= form_close()?>
					</div>
					<div id="pane_list">
						<?= view('device/query', get_defined_vars()) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>