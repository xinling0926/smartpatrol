<style type="text/css">
	.inline div{
		display: inline;
	}
	div#checkbox_fmd0706_w div {
		margin-right: 5px;
	}
	.checkbox_group div{
		display: inline;
	}
	#checkbox_fmd0706_d label {
		width: 30px;
	}
	.checkbox label{
		position: relative;
	}
</style>
<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6"></div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></button>
			<button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></button>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=lang('FormItem.edit_form_fmd0703_label')?></label>
		<div class="col-sm-7"><?= form_text_field('fmd0703', $data) ?></div>
	</div>
	<?php if ($fmd02s) : ?>
		<div class="form-group checkbox_group">
			<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0704_label')?></label>
			<div class="col-sm-8"><?= form_checkbox_array_field('fmd0704', $fmd02s, $data,'', ['class'=>'inline']) ?></div>
		</div>
	<?php endif ?><?php if ($fmd01->fmd0105>1): ?>
	<?php if ($fmd01->fmd0105>2): ?>
	<div class="form-group checkbox_group">
		<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0706_i_label')?></label>
		<div class="col-sm-8">
			<?=lang('FormItem.add_fmd07_string_1')?>
			<?= form_checkbox_array_input('fmd0706_i', [1=>1,2=>2,3=>3,4=>4], $fmd0706_i,['class'=>'inline']) ?>
			<?= form_dropdown(['name' => 'fmd0706_t', 'id' => 'fmd0706_t'], ['i' => lang('FormItem.add_fmd07_string_2'),'m' => lang('FormItem.add_fmd07_string_3')], $fmd0706_t) ?>
		</div>
	</div><?php endif ?>
	<div class="form-group checkbox_group">
		<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0706_w_label')?></label>
		<div class="col-sm-8">
			<?=lang('FormItem.add_fmd07_string_4')?>	<?= form_checkbox_array_input('fmd0706_w', $week, $fmd0706_w,['class'=>'inline']) ?>
		</div>
	</div>
	<?php endif ?>
	<?php if ($fmd01->fmd0105==3): ?>
	<div class="form-group checkbox_group">
		<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0706_d_label')?></label>
		<div class="col-sm-8">
			<?= form_checkbox_array_input('fmd0706_d', $day, $fmd0706_d,['class'=>'inline']) ?>
		</div>
	</div><?php endif ?>
	<div class="box box-warning box-solid">
		<div class="box-header"><?=lang('FormItem.edit_form_warning_box_title')?></div>
		<div class="box-body">
			<?php $fmd0401 = 0 ?>
			<?php foreach ($fmd06s as $fmd06) : ?>
				<?php if ($fmd0401 <> $fmd06->fmd0603) {
					$fmd0401 = $fmd06->fmd0603;
					echo "<div class=\"row patrol_item_group_header\">{$fmd04s[$fmd0401]}</div>";
				} ?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?= form_checkbox('delete[]', $fmd06->fmd0601) . $fmd06->fmd0504 ?></label>
					<div class="col-sm-7"><?php
						switch ($fmd06->fmd0606) {
							case 4:
								$opt = explode("\n", $fmd06->fmd0608);
								$idx = array_search($fmd06->fmd0609, $opt);
								echo form_dropdown_input('edit_' . $fmd06->fmd0601, $opt, $idx);
								break;
							case 5:
								echo form_checkbox_array_input('edit_' . $fmd06->fmd0601, explode("\n", $fmd06->fmd0608));
								break;
							default:
								echo form_text_input('edit_' . $fmd06->fmd0601, $fmd06->fmd0609);

						}
						?></div>
				</div>
			<?php endforeach ?>
		</div>
	</div>
	<?= form_close() ?>
</div>