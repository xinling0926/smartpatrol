<?php assets_css('dataTables.bootstrap','datatables');?>
<?php assets_js('jquery.dataTables','datatables');?>
<?php assets_js('dataTables.bootstrap','datatables');?>
<div class="row">
	<div class="col-md-4" id="pane_list">
		<?= view('sdk/table/query', get_defined_vars()) ?>
	</div>
    <div class="col-md-8" id="pane_detail" style="padding-left: 0">
    </div>
</div>


