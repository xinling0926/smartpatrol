<div class="row">
	<div class="col-xs-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pane_list" data-toggle="tab">功能列表</a></li>
				<li class="pull-right">
					<button class="btn btn-default" onclick="edit(this)"><i class="fa fa-fw fa-plus-square-o"></i>添加功能</button>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pane_list">
					<?= view('sdk/page/query', get_defined_vars()) ?>
				</div>
			</div>
		</div>
	</div>
</div>