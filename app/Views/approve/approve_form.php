<div class="box box-primary" id="approval_zone">
	<div class="box-header with-border">
		<h3 class="box-title"><?=lang('approve_form_heading')?></h3>
	</div>
	<div class="box-body">
		<?php echo form_open('', array('id' => 'approve_form', "class" => "form-horizontal")); ?>
		<?php echo form_hidden('fmd0101', $fmd0101) ?>
		<?php echo form_hidden('id', $id) ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('f_d06')?></label>
			<div class="col-sm-10"><?= form_radio_input('result',['1'=>lang('v_d06_1'),2=>lang('v_d06_2')],1) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('f_d07')?></label>
			<div class="col-sm-10"><?= form_textarea_input('memo','','',['onkeyup'=>'this.value=this.value.substring(0, 100)']) ?></div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=lang('f_d05')?></label>
			<div class="col-sm-4">
				<img src="<?= ($current_user->sys0120) ? base_url("data/sign/" . $current_user->sys0101 . '/' . $current_user->sys0120) :
                    base_url("assets/img/no_sign.png") ?>" class="form-control" alt="<?=lang('no_sign')?>" style="width:100%;height:auto">
			</div>
			<label class="col-sm-2 control-label"><?=lang('f_d04')?></label>
			<div class="col-sm-4">
				<div class="form-control"><?= now() ?></div>
			</div>
		</div>
		</form>
	</div>
	<div class="box-footer">
		<a href='javascript:approval_form.reset();' class="btn btn-default"><i class="fa fa-eraser"></i> <?= lang('toolbar_reset') ?></a>
		<button class="btn btn-primary pull-right" onclick="send()"><i class="fa fa-play-circle"></i> <?=lang('toolbar_ok')?></button> &emsp;
		<span id="message2"></span>
	</div>
</div>