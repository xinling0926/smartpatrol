<div class="row toolbar">
	<div class="col-sm-8">
		<?php if ($data->fmd3006 == 2) : ?>
			<a href="#" class="btn btn-primary" onclick="check_out(this)" data-id="<?= $data->fmd3001 ?>"> <i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
		<?php else : ?>
			<?php if ($data->fmd3006 == 3) : ?>
				<a href="#" class="btn btn-danger" onclick="revert(this)" data-id="<?= $data->fmd3001 ?>"> <i class="fa fa-edit"></i> <?=lang('quit_edit_btn')?></a>
				<a href="#" class="btn btn-primary" onclick="commit(this)" data-id="<?= $data->fmd3001 ?>"> <i class="fa fa-edit"></i> <?=lang('finish_edit_btn')?></a>
			<?php endif ?>
			<?php if ($data->fmd3006 == 1) : ?>
				<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->fmd3001 ?>"> <i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
			<?php endif ?>
			<?php if (($data->fmd3006 == 0) || ($data->fmd3006 == 4)) : ?>
				<span class="btn" data-id="<?= $data->fmd3001 ?>"> <i class="fa fa-close"></i> <?=lang('v_toolbar_close')?></span>
			<?php endif ?>
		<?php endif ?>
	</div>
	<div class="col-sm-4 text-right">
		<?php if ($data->fmd3006 == 3) : ?>
		<a href="#" class="btn btn-success" onclick="save_and_close_dialog2()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<?php endif ?>
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
	</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
    </div>
</div>
<?php echo form_open('annual_checkup/edit_fmd30/'.$data->fmd3001, array('id' => 'edit_fmd30', "class" => "form-horizontal")); ?>
<?php if ($data) echo form_hidden('fmd3001', $data->fmd3001) ?>
<?php if (isset($data->fmd3003_lock) && $data->fmd3003_lock) echo form_hidden('fmd3003_lock', 'TRUE') ?>
<div class="row form-horizontal">
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('main_info_ent1004')?></label>
		<div class="col-sm-4">
			<div class="form-control"><?= $data->ent1004 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('main_info_fmd0104')?></label>
		<div class="col-sm-4">
			<div class="form-control"><?= $data->fmd0104 ?></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('v_beginDate')?></label>
		<div class="col-sm-4">
		<?php if ($data->fmd3006 == 3) : ?>
            <?= form_date_input('fmd3003', $data->fmd3003) ?>
		<?php else : ?>
			<div class="form-control patrol-form-fmd30" data-id="<?= $data->fmd3003 ?>" data-fmd3003="<?= $data->fmd3003 ?>"><?= $data->fmd3003 ?></div>
		<?php endif ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('v_endDate')?></label>
		<div class="col-sm-4">
		<?php if ($data->fmd3006 == 3) : ?>
			<?= form_date_input('fmd3004', $data->fmd3004) ?>
		<?php else : ?>
			<div class="form-control patrol-form-fmd30" data-id="<?= $data->fmd3004 ?>" data-fmd3004="<?= $data->fmd3004 ?>"><?= $data->fmd3004 ?></div>
		<?php endif ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label"><?=lang('v_type')?></label>
		<div class="col-sm-4">
		<?php if ($data->fmd3006 == 3) : ?>
			<div class="col-sm-10"><?= form_radio_field('fmd3005', $fmd3005_opt, $data, $data->fmd3005) ?></div>
		<?php else : ?>
			<div class="form-control patrol-form-fmd30" data-id="<?= $data->fmd3005 ?>" data-fmd3005="<?= $data->fmd3005 ?>"><?= $fmd3005_opt[$data->fmd3005] ?></div>
		<?php endif ?>
		</div>
	</div>
</div>
<?= form_close() ?>
<?php if ($data->fmd3006 == 3) : ?>
<?php echo assets_js('jquery.contextMenu.min', 'jQuery-contextMenu') ?>
<?php echo assets_css('jquery.contextMenu.min', 'jQuery-contextMenu') ?>
<script type='text/javascript'>
$(function () {
	$.contextMenu({
		selector: '.patrol-form-fmd30',
		trigger: 'left',
		callback: contextMenuCallback,
		items: {
			"edit_form_fmd30": {name: "<?=lang('dialog_edit_btn')?>", icon: "edit"}
		},
		events: {
			show: function (options) {
				selected_item = this;
			},
			hide: function (options) {
				selected_item = null;
			}
		}
	});
});

function contextMenuCallback(key, options) {
    var m = "clicked: " + key;
    window.console && console.log(m, options);
    switch (key) {
        case 'edit_form_fmd30':
            fmd30_edit();
			break;
    }
}
function fmd30_edit() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd3001');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd30');
    if (langType == 'zh-CN') {
        var title = '编辑';
    } else {
        var title = '編輯';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}
function save_and_close_dialog2() {
    $("#message").html('');
	$("#message").hide();
    ajax_post_view($("#edit_fmd30").attr('action'),
        $("#edit_fmd30").serialize(),
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
				setpage();
            } else {
                $("#message").html(dataObj.message);
                $("#message").show();
            }
        }
    );
}
</script>
<?php assets_css('bootstrap-datepicker3', 'datepicker') ?>
<?php assets_js('bootstrap-datepicker.min', 'datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-TW.min', 'datepicker') ?>
<script type='text/javascript'>
	$('.date').datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		language: "zh-TW",
		autoclose: true,
		zIndexOffset: 1200,
		todayHighlight: true
    });
</script>
<?php endif ?>