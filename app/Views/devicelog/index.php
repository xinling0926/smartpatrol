<div class="box box-primary">
    <div class="box-header with-border">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" id="bt1">
            <i class="fa fa-minus"></i><h3 class="box-title"><?= lang('box_query') ?></h3>
        </button>
    </div>
    <?php echo form_open('', array('id' => 'query_form', "class" => "form-horizontal")); ?>
    <div class="box-body">
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_dev0203')?></label>
            <div class="col-lg-2 col-sm-4"><?= form_date_input('dev0203s', $dev0203s) ?></div>
            <div class="control-label" style="float: left"><?=lang('to')?></div>
            <div class="col-lg-2 col-sm-4"><?= form_date_input('dev0203e', $dev0203e) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_dev0202')?></label>
            <div class="col-lg-4 col-sm-8"><?= form_dropdown_input('dev0202', $dev01s) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_dev0206')?></label>
            <div class="col-lg-4 col-sm-8"><?= form_text_input('dev0206k', $dev0206k) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_dev0204')?></label>
            <div class="col-lg-2 col-sm-4">
				<select name="dev0204" id="dev0204" class="form-control">
					<option value=""><?=lang('v_dev0204_')?></option>
					<option value="1"><?=lang('v_dev0204_1')?></option>
					<option value="2"><?=lang('v_dev0204_2')?></option>
					<option value="3"><?=lang('v_dev0204_3')?></option>
				</select>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href='javascript:query_form.reset();' class="btn btn-default"><i class="fa fa-eraser"></i><?= lang('toolbar_reset') ?></a>
        <a href="#" class="btn btn-primary pull-right" onclick="query();bt1.click();"><i class="fa fa-search"></i><?= lang('toolbar_search') ?></a>
    </div>
    </form>
</div>
<div id="pane_list"></div>

<?php assets_css('bootstrap-datepicker3', 'datepicker') ?>
<?php assets_js('bootstrap-datepicker.min', 'datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-CN.min', 'datepicker') ?>

<script type='text/javascript'>
    $('.date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "zh-CN",
        autoclose: true,
        todayHighlight: true
    });
</script>