<div class="box box-primary approve_form">
	<div class="box-header with-border">
		<h3 class="box-title"><?=lang('approve_form_heading')?></h3>
	</div>
	<div class="box-body">
		<?php echo form_open('', array('id' => 'send_form', "class" => "form-horizontal")); ?>
		<?php echo form_hidden('fmd0101', $fmd0101) ?>
		<?php echo form_hidden('report_id', $report_id) ?>
		<?php echo form_hidden('fmd2101', $fmd2101) ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('label_memo')?></label>
			<div class="col-sm-8"><?= form_textarea_input('memo','','',['onkeyup'=>'this.value=this.value.substring(0, 100)']) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('label_current_user')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= $current_user->name ?></div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=lang('label_current_time')?></label>
			<div class="col-sm-8">
				<div class="form-control"><?= now() ?></div>
			</div>
		</div>
		</form>
	</div>
	<div class="box-footer">
		<a href='javascript:send_form.reset();' class="btn btn-default"><i class="fa fa-eraser"></i> <?= lang('toolbar_reset') ?></a>
		<button class="btn btn-primary pull-right" onclick="send_report()"><i class="fa fa-play-circle"></i> <?=lang('generate_send_report_btn')?></button>
		<span id="message2" class="text-red pull-right" style="display: none;"></span>
	</div>
</div>