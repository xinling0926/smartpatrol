<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title ?></h3>
    </div>
    <div class="box-body">
        <div class="row toolbar">
            <div class="col-sm-6 col-sm-offset-6 text-right">
                <a href="#" class="btn btn-success" onclick="save2()"><i class="fa fa-save"></i> <?= lang('toolbar_save') ?></a>
                <a href="#" class="btn btn-default" onclick="detail2()"><i class="fa fa-undo"></i> <?= lang('toolbar_cancel') ?></a>
            </div>
        </div>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
        </div>
		<?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
		<?php if ($data) echo form_hidden('ent1001', $data->ent1001) ?>
		<?php if (!$data) echo form_hidden('ent1005', $ent1005) ?>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent1004') ?></label>
            <div class="col-sm-8"><?= form_text_field('ent1004', $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent1003') ?></label>
            <div class="col-sm-8"><?= form_text_field('ent1003', $data) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label"><?= lang('f_ent1007') ?></label>
            <div class="col-sm-8">
                <div class="radio">
                    <label>
                        <input type="radio" name="ent1007" id="ent10070" value="1"<?php if ($data && $data->ent1007 == 1) echo " checked" ?>>
						<?= lang('v_ent1007_1') ?>
                    </label>
                    <label>
                        <input type="radio" name="ent1007" id="ent10071" value="2"<?php if ($data == NULL || $data->ent1007 == 2) echo " checked" ?>>
						<?= lang('v_ent1007_2') ?>
                    </label>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>