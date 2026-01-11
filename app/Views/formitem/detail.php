<style type="text/css">
	#pane2, #pane3, #pane4 {
		overflow: auto;
	}
	#pane2 td, #pane3 td, #pane4 td, #pane2 th, #pane3 th, #pane4 th {
		white-space: nowrap;
	}
</style>
<div class="row toolbar">
	<div class="col-sm-8">
		<?php if ($data->fmd0108 == 2) : ?>
			<a href="#" class="btn btn-primary" onclick="check_out(this)" data-id="<?= $data->fmd0101 ?>"> <i class="fa fa-edit"></i> <?=lang('Globe.toolbar_edit')?></a>
			<a href="#" class="btn btn-info" onclick="fmd31_edit(<?= $data->fmd0101 ?>)" data-id="<?= $data->fmd0101 ?>"> <i class="fa fa-edit"></i> <?= lang('dialog_edit_fmd31_btn') ?></a>
		<?php else : ?>
			<?php if ($data->fmd0107 > 1) : ?>
				<a href="#" class="btn btn-danger" onclick="revert(this)" data-id="<?= $data->fmd0101 ?>"> <i class="fa fa-edit"></i> <?=lang('quit_edit_btn')?></a>
			<?php endif ?>
			<a href="#" class="btn btn-primary" onclick="commit(this)" data-id="<?= $data->fmd0101 ?>"> <i class="fa fa-edit"></i> <?=lang('finish_edit_btn')?></a>
			<a href="#" class="btn btn-warning" onclick="fmd01_edit(<?= $data->fmd0101 ?>)" data-id="<?= $data->fmd0101 ?>"> <i class="fa fa-edit"></i> <?= lang('dialog_edit_fmd01_btn') ?></a>
		<?php endif ?>
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?= lang('Globe.toolbar_close')?></a>
	</div>
</div>
<div class="row form-horizontal">
	<div class="form-group col-sm-3">
		<label class="col-sm-4 control-label"><?=lang('main_info_ent1004')?></label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->ent1004 ?></div>
		</div>
	</div>
	<div class="form-group col-sm-3">
		<label class="col-sm-4 control-label"><?=lang('main_info_fmd0103')?></label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->fmd0103 ?></div>
		</div>
	</div>
	<div class="form-group col-sm-6">
		<label class="col-sm-2 control-label"><?=lang('main_info_fmd0104')?></label>
		<div class="col-sm-10">
			<div class="form-control"><?= $data->fmd0104 ?></div>
		</div>
	</div>
	<div class="form-group col-sm-3">
		<label class="col-sm-4 control-label"><?=lang('main_info_fmd0105')?></label>
		<div class="col-sm-8">
			<div class="form-control"><?= $fmd0105_opt[$data->fmd0105] ?></div>
		</div>
	</div>
	<div class="form-group col-sm-3">
		<label class="col-sm-4 control-label"><?=lang('main_info_fmd0107')?></label>
		<div class="col-sm-8">
			<div class="form-control"><?= $data->fmd0107 ?></div>
		</div>
	</div>
	<div class="form-group col-sm-6">
		<label class="col-sm-2 control-label"><?=lang('main_info_fmd0108')?></label>
		<div class="col-sm-4">
			<div class="form-control"><?= $fmd0108_opt[$data->fmd0108] ?></div>
		</div>
	</div>
</div>

<div class="nav-tabs-custom">
	<ul class="nav nav-tabs" style="background-color: aliceblue;">
		<li<?= ($page == 1) ? " class=\"active\"" : "" ?>><a href="#pane2" data-toggle="tab" onclick="change_detail_page(1)"><?=lang('t_d_item')?></a></li>
		<li<?= ($page == 2) ? " class=\"active\"" : "" ?>><a href="#pane3" data-toggle="tab" onclick="change_detail_page(2)"><?=lang('t_d_sheet')?></a></li>
		<li<?= ($page == 3) ? " class=\"active\"" : "" ?>><a href="#pane4" data-toggle="tab" onclick="change_detail_page(3)"><?=lang('t_d_route')?></a></li>
        <?php if ($data->fmd0105==1) : ?>
			<li<?= ($page == 4) ? " class=\"active\"" : "" ?>>
				<a href="#pane5" data-toggle="tab" onclick="change_detail_page(4)"><?=lang('t_d_group')?></a></li>
		<?php endif ?>
	</ul>
	<div class="tab-content">
		<?php if ($page==1) :?>
		<div class="tab-pane scroll<?= ($page == 1) ? " active" : "" ?>" id="pane2">
			<?= $patrol_table ?>
		</div>
		<?php endif ?>
		<?php if ($page==2) :?>
		<div class="tab-pane<?= ($page == 2) ? " active" : "" ?>" id="pane3">
			<?php if ($data->fmd0108 == 1) : ?>
				<button class="btn btn-primary" onclick="edit_dialog(this)" data-action="edit_fmd07" data-id="<?= $data->fmd0101 ?>">
					<i class="fa fa-cogs"></i> <?=lang('auto_set_sheet_btn')?>
				</button>
			<?php endif ?>
			<div class="scroll"><?= $form_table ?></div>
		</div>
		<?php endif ?>
		<?php if ($page==3) :?>
		<div class="tab-pane<?= ($page == 3) ? " active" : "" ?>" id="pane4">
			<?php if ($data->fmd0108 == 1) : ?>
				<button class="btn btn-primary" onclick="edit_dialog(this)" data-action="edit_fmd08" data-parent="<?= $data->fmd0101 ?>">
					<i class="fa fa-plus-square-o"></i> <?=lang('add_route_btn')?>
				</button>
			<?php endif ?>
			<?= $route_table ?>
		</div>
		<?php endif ?>
        <?php if ($page==4) : ?>
		<div class="tab-pane<?= ($page == 4) ? " active" : "" ?>" id="pane5">
            <?php if ($data->fmd0108 == 1) : ?>
            <button class="btn btn-primary" onclick="edit_dialog(this)" data-action="edit_fmd02" data-parent="<?= $data->fmd0101 ?>">
                <i class="fa fa-plus-square-o"></i> <?=lang('add_fmd02_btn')?>
            </button>
            <?php endif ?>
			<table class="table table-striped table-hover dataTable">
				<tbody>
				<tr>
					<th><?=lang('f_common_no')?></th>
					<th><?=lang('f_d_fmd0204')?></th>
					<?php if ($data->fmd0105 == 1) : ?>
						<th><?=lang('f_d_fmd0205')?></th>
						<th><?=lang('f_d_fmd0206')?></th>
					<?php endif ?>
				</tr>
				<?php foreach ($fmd02s as $d) : ?>
					<tr>
						<td><?= $d->fmd0203 ?></td>
						<?php if ($data->fmd0108 == 1) : ?>
							<td class="a" onclick='fmd02_edit(this)' data-id=<?= $d->fmd0201 ?>><?= $d->fmd0204 ?></a></td>
						<?php else: ?>
							<td><?= $d->fmd0204 ?></td>
						<?php endif ?>
						<?php if ($data->fmd0105 == 1) : ?>
							<td><?= $d->fmd0205 ?></td>
							<td><?= $d->fmd0206 ?></td>
						<?php endif ?>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
		<?php endif ?>
	</div>
</div>
<?php if ($data->fmd0108 == 1) : ?>
	<?php echo assets_js('jquery.contextMenu.min', 'jQuery-contextMenu') ?>
	<?php echo assets_css('jquery.contextMenu.min', 'jQuery-contextMenu') ?>
	<script type='text/javascript'>
		<?php if ($page==1) :?>
		$(function () {
			$.contextMenu({
				selector: '.patrol-item',
				trigger: 'left',
				callback: contextMenuCallback,
				items: {
					"edit": {name: "<?=lang('dialog_edit_btn')?>", icon: "edit"},
					"add_sub": {name: "<?=lang('dialog_add_btn')?>", icon: "add"},
					"up": {
						name: "<?=lang('dialog_move_up_btn')?>",
						icon: "fa-arrow-up",
						visible: function(key, opt) {
							return $(this).data('fmd0303') == 1;
						}
					},
					"down": {
						name: "<?=lang('dialog_move_down_btn')?>",
						icon: "fa-arrow-down",
						visible: function(key, opt) {
							return $(this).data('fmd0303') == 1;
						}
					},
					"copy_fmd04": {name: "<?=lang('dialog_copy_btn')?>", icon: "copy"},
					"sep1": "---",
					"delete": {name: "<?=lang('dialog_delete_btn')?>", icon: "delete"}
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
		// Handle click on empty row to add first patrol item
		$(function () {
			$('.patrol-item-empty').on('click', function() {
				var fmd0101 = $(this).data('fmd0101');
				var obj = {};
				$(obj).data('id', fmd0101);
				$(obj).data('action', 'add_fmd04first');
				$(obj).data('title', '<?=lang('dialog_add_btn')?>');
				edit_dialog(obj, '500px', '400px');
			});
		});
		$(function () {
			$.contextMenu({
				selector: '.patrol-field',
				trigger: 'left',
				callback: contextMenuCallback,
				items: {
					"edit_fmd06": {name: "<?=lang('dialog_edit_btn')?>", icon: "edit"},
					"copy_fmd06": {name: "<?=lang('dialog_copy_fmd06_btn')?>", icon: "copy"},
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
		<?php endif ?>
		<?php if ($page==3) :?>
		$(function () {
			$.contextMenu({
				selector: '.patrol-tag',
				trigger: 'left',
				callback: contextMenuCallback,
				items: {
					"edit_form": {name: "<?=lang('dialog_add_form_btn')?>", icon: "add"},
					"edit_fmd09": {name: "<?=lang('dialog_edit_tag_btn')?>", icon: "edit"},
					"sep1": "---",
					"delete_tag": {name: "<?=lang('dialog_delete_tag_btn')?>", icon: "delete"}
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

		$(function () {
			$.contextMenu({
				selector: '.patrol-form',
				trigger: 'left',
				callback: contextMenuCallback,
				items: {
					"delete_form": {name: "<?=lang('dialog_delete_btn')?>", icon: "delete"}
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

		$(function () {
			$.contextMenu({
				selector: '.patrol-route',
				trigger: 'left',
				callback: contextMenuCallback,
				items: {
					"edit_tag": {name: "<?=lang('dialog_add_tag_btn')?>", icon: "add"},
					"edit_fmd08": {name: "<?=lang('dialog_edit_route_btn')?>", icon: "edit"},
					"sep1": "---",
					"delete_route": {name: "<?=lang('dialog_delete_route_btn')?>", icon: "delete"}
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
		<?php endif ?>
		<?php if ($page==2) :?>
		$(function () {
			// 選擇要加入電子表單的巡檢項目
			$('.patrol-form-select').bind('click', patrol_form_select);
			// 點選電子表單進入編輯
			$('.patrol-form-edit').bind('click', patrol_form_edit);
		});
		<?php endif ?>
	</script>
<?php endif ?>

<script type='text/javascript'>
	$(function () {
		get_detail_option = "page=<?=$page?>";
		<?php if($page==1) { ?>
		if ($('#patrol_table').width()>$('#pane2').width() && $('#pane2').height() > ($(window).height()-200)){
			$('#pane2').height($(window).height() - 200);
			$('#patrol_table').fixedHeaderTable();
		}
		<?php } elseif ($page==2) { ?>
		if ($('#form_table').width()>$('#pane3').width() &&  $('#pane3 .scroll').height() > ($(window).height()-200)){
			$('#pane3 .scroll').height($(window).height() - 200);
			$('#form_table').fixedHeaderTable();
		}
		<?php } ?>
	});
</script>
<script type='text/javascript'>
function fmd01_edit(id) {

    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd01');
	var title = '<?= lang('dialog_edit_fmd01_btn') ?>';
    $(obj).data('title', title);
    edit_dialog(obj, '400px', '300px');
}

function fmd31_edit(id) {

	var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd31');
	var title = '<?= lang('dialog_edit_fmd31_btn') ?>';
    $(obj).data('title', title);
    edit_dialog(obj, '720px', '540px');
}
</script>
