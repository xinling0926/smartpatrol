<div class="row">
	<div class="col-xs-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('Auth.account_index_heading')?></a></li>
				<li class="pull-right">
					<button class="btn btn-default" onclick="edit(this)"><i class="fa fa-fw fa-plus-square-o"></i> <?=lang('Auth.account_index_add_btn')?></button>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pane1">
					<div class="dataTables_filter">
						<?= form_open('', ['id' => 'query_form']); ?>
						<?= form_text_input('search', '', '', ['placeholder' => lang('Globe.search_hint')]) ?>
						<?= form_dropdown_input('sys0110', $dept) ?>
						<?= form_dropdown_input('sys0205', [0 => lang('Auth.v_sys0108_0'), 1 => lang('Auth.v_sys0108_1'), '' => lang('Globe.all')], $sys0205 ?? '') ?>
						<button class="btn btn-primary" type="submit">
							<i class="fa fa-search"></i><?= lang('Globe.toolbar_search') ?></button>
						<?=form_close()?>
					</div>
					<div id="pane_list">
						<?= view('account/query', get_defined_vars()) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
