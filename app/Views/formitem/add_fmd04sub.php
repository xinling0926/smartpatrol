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
	<?php echo form_open('', array('id' => 'fmd04_form', "class" => "form-horizontal")); ?>
	<?php foreach($fmd03s as $fmd03):?>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?= $fmd03->fmd0304?></label>
		<div class="col-sm-8"><?php
			if (isset($fmd03->text)) { echo "<div class=\"form-control\" readonly>".htmlspecialchars($fmd03->text)."</div>"; }
			else { echo form_text_input('fmd0404'.$fmd03->fmd0301);} ?></div>
	</div>
	<?php endforeach ?>
	<input type="submit" style="display: none">
	<?= form_close() ?>
</div>