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
</style>
<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6"></div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-success" onclick="$('#fmd07_form').submit()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></button>
			<button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'fmd07_form', "class" => "form-horizontal")); ?>
	<?= form_hidden('id',$id); ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">
			<input type="radio" name="method" value="1" checked>
			<?=lang('FormItem.add_fmd07_fmd0701_label')?></label>
		<div class="col-sm-7"><?= form_dropdown_input('fmd0701', $fmd07s) ?></div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">
			<input type="radio" name="method" value="2">
			<?=lang('FormItem.add_fmd07_fmd0703_label')?></label>
		<div class="col-sm-7"><?= form_text_input('fmd0703', '', '', 'readonly') ?></div>
	</div>
	<?php if ($fmd02s) : ?>
		<div class="form-group checkbox_group">
			<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0704_label')?></label>
			<div class="col-sm-8"><?= form_checkbox_array_input('fmd0704', $fmd02s,'', ['class'=>'inline']) ?></div>
		</div>
	<?php endif ?><?php if ($fmd01->fmd0105>1): ?>
		<?php if ($fmd01->fmd0105>2): ?>
			<div class="form-group checkbox_group">
			<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0706_i_label')?></label>
			<div class="col-sm-8">
				<?=lang('FormItem.add_fmd07_string_1')?>
				<?= form_checkbox_array_input('fmd0706_i', [1=>1,2=>2,3=>3,4=>4], '',['class'=>'inline']) ?>
				<?= form_dropdown(['name' => 'fmd0706_t', 'id' => 'fmd0706_t'], ['i' => lang('FormItem.add_fmd07_string_2'),'m' => lang('FormItem.add_fmd07_string_3')]) ?>
			</div>
			</div><?php endif ?>
		<div class="form-group checkbox_group">
			<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0706_w_label')?></label>
			<div class="col-sm-8">
				<?=lang('FormItem.add_fmd07_string_4')?>	<?= form_checkbox_array_input('fmd0706_w', $week, [],['class'=>'inline']) ?>
			</div>
		</div>
	<?php endif ?>
	<?php if ($fmd01->fmd0105==3): ?>
		<div class="form-group checkbox_group">
		<label class="col-sm-4 control-label"><?=lang('FormItem.add_fmd07_fmd0706_d_label')?></label>
		<div class="col-sm-8">
			<?= form_checkbox_array_input('fmd0706_d', $day, '',['class'=>'inline']) ?>
		</div>
		</div><?php endif ?>
	<input type="submit" style="display: none">
	<?= form_close() ?>
</div>
<script type='text/javascript'>
	$(function () {
		$(".checkbox input").attr("disabled", "true");
		$('input[name="method"]').change(function () {
			if ($("input[name='method']:checked").val() == 1) {
				$('#fmd0701').removeAttr("readonly");
				$('#fmd0703').attr("readonly", "readonly");
				$('.checkbox input').attr("disabled", "true");
			} else {
				$('#fmd0701').attr("readonly", "readonly");
				$('#fmd0703').removeAttr("readonly");
				$('.checkbox input').removeAttr("disabled");
			}
		});
	});
</script>