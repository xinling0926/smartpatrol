<div class="row toolbar">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<a href="#" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></a>
		<a href="#" class="btn btn-default" onclick="cancel_edit()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></a>
	</div>
</div>
<div class="row">
	<div class="col-md-12"><div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div></div>
	<?php echo form_open('',array('id'=>'edit_form',"class"=>"form-horizontal")); ?>
		<?php if ($data) echo form_hidden('dev0101',$data->dev0101)?>
		<div class="col-md-6">
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('label_dev0104')?></label>
				<div class="col-sm-8"><?= form_text_field('dev0104', $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('label_dev0105')?></label>
				<div class="col-sm-8"><?= form_text_field('dev0105', $data,'','',TRUE) ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('label_ent1004')?></label>
				<div class="col-sm-8"><?= form_dropdown_field('dev0103', $dept, $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('label_dev0110')?></label>
				<div class="col-sm-8"><?= form_text_field('dev0110', $data) ?></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=lang('label_dev0106')?></label>
				<div class="col-sm-8">
					<?= form_radio_field('dev0106',$dev0106_opt,$data,1) ?>
				</div>
			</div>
		</div>
	</form>
</div>