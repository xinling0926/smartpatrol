<div class="row">
	<div class="col-xs-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pane_list" data-toggle="tab"><?=lang('index_android_man')?></a></li>
				<li class="pull-right">
					<button class="btn btn-default" onclick="edit_android()"><i class="fa fa-fw fa-plus-square-o"></i> <?=lang('upload_android_app_btn')?></button>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="pane_list">
					<?= view('systemsetting/android_man_detail', get_defined_vars()) ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
function edit_android() {
    var obj = {};
    $(obj).data('action', 'android_man_edit');
    if (langType == 'zh-CN') {
        var title = '<?=lang('upload_android_app_apk')?>';
    } else {
        var title = '<?=lang('upload_android_app_apk')?>';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}
</script>