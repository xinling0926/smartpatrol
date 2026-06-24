<div class="row">
	<div class="col-xs-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pane_list" data-toggle="tab">EUI列表</a></li>
				<li class="pull-right"></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pane_list">
					<div class="dataTables_filter">
						<?= form_open('', ['id' => 'query_form']); ?>
						<?= form_dropdown_input('fmd4102', $ent10s) ?>
						<?= form_text_input('fmd4103', '', '', ['placeholder' => '統計年月[YYYY-MM]']) ?>
						<button class="btn btn-primary" type="submit">
							<i class="fa fa-search"></i><?= lang('toolbar_search') ?></button>
						<?= form_close() ?>
					</div>
					<?= view('euisettings/query', get_defined_vars()) ?>
				</div>
			</div>
		</div>
	</div>
</div>
