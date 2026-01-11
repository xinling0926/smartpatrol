<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-6"></div>
		<div class="col-sm-6 text-right">
			<button class="btn btn-success" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('Globe.toolbar_save')?></button>
			<button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('Globe.toolbar_cancel')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<p class="text-red"><?=lang('FormItem.add_fmd0906_hint')?></p>
		</div>
	</div>
	<?php echo form_open('', array('id' => 'fmd09_form', "class" => "form-horizontal")); ?>
	<?= form_hidden('fmd0901', $fmd0901) ?>
	<div class="row" style="height: 200px; overflow: scroll;">
		<div class="col-sm-12">
		<?php foreach ($fmd07s as $fmd07): ?>
			<?= form_checkbox_input('fmd0906[]', $fmd07->fmd0701, $fmd07->fmd0703,false,['class'=>'inline']) ?>
		<?php endforeach ?>
		</div>
	</div>
	<input type="submit" style="display: none">
	<?= form_close() ?>
</div>