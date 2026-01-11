<div class="box box-primary">
    <div class="box-header with-border">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" id="bt1">
            <i class="fa fa-minus"></i><h3 class="box-title">查詢條件</h3>
        </button>
    </div>
    <?php echo form_open('', array('id' => 'query_form', "class" => "form-horizontal")); ?>
    <div class="box-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">操作日期</label>
            <div class="col-lg-2 col-sm-4"><?= form_date_input('log0302s', $log0302s) ?></div>
            <div class="control-label" style="float: left">至</div>
            <div class="col-lg-2 col-sm-4"><?= form_date_input('log0302e', $log0302e) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">使用者</label>
            <div class="col-lg-2 col-sm-4"><?= form_dropdown_input('log0304', $sys01s) ?>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href='javascript:query_form.reset();' class="btn btn-default"><i class="fa fa-eraser"></i>清除</a>
        <a href="#" class="btn btn-primary pull-right" onclick="query();bt1.click();"><i class="fa fa-search"></i>查詢</a>
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