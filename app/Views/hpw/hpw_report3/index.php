<div class="box box-primary<?php if (isset($id)) {
    echo ' collapsed-box';
} ?>" id="report_condition">
    <div class="box-header with-border">
        <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
            <h3 class="box-title"><?= lang('box_query') ?></h3>
        </button>
    </div>
    <?php echo form_open('', array('id' => 'query_form', "class" => "form-horizontal")); ?>
    <div class="box-body">
        <div class="callout callout-danger" style="display: none;" id="message"></div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_report_name')?></label>
            <?php if (count($ent10s) > 2) : ?>
                <div class="col-lg-2 col-sm-2">
                    <?= form_dropdown_input('ent1001', $ent10s, '', '', ['onchange' => "select_ent10();"]) ?></div>
            <?php endif ?>
            <div class="col-lg-4 col-sm-6">
                <?= form_dropdown_input('fmd0101', $fmd01s) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_report_date')?></label>
            <div class="col-lg-2 col-sm-4"><?= form_date_input('start_date', first_day_of_month()) ?></div>
            <div class="control-label" style="float: left"><?=lang('to')?></div>
            <div class="col-lg-2 col-sm-4"><?= form_date_input('end_date', today()) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?=lang('label_report_state')?></label>
            <div class="col-lg-2 col-sm-4"><?= form_dropdown_input('state', ['' => lang('all'), 1 => lang('v_state_1'), 2 => lang('v_state_2')]) ?>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <button type="button" class="btn btn-primary pull-right" onclick="show_report()"><i class="fa fa-search"></i> <?= lang('toolbar_search') ?>
        </button>
    </div>
    <?= form_close() ?>
</div>
<div class="nav-tabs-custom" hidden>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#pane1" data-toggle="tab"><?=lang('box_result')?></a></li>
        <li class="pull-right"></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="pane1">
            <div id="pane_list"></div>
        </div>
    </div>
</div>
<?php assets_css('bootstrap-datepicker3', 'datepicker') ?>
<?php assets_js('bootstrap-datepicker.min', 'datepicker') ?>
<?php assets_js('locales/bootstrap-datepicker.zh-CN.min', 'datepicker') ?>
<script type='text/javascript'>

    $('.date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "zh-CN",
        autoclose: true,
        zIndexOffset: 1200,
        todayHighlight: true
    });

    function select_ent10() {
        var ent1001= $('#ent1001').val();
        ajax_post_view('hpw_report3/get_fmd01/',
            'ent1001='+ent1001,
            function (data) {
                var fmd01s = json_decode(data);
                $("#fmd0101").empty();
                $.each(fmd01s, function (index, item) {
                    $("#fmd0101").append('<option value="' + item.fmd0101 + '">' + item.fmd0104 + '</option>');
                });
            }
        );
    }

    function show_report() {
        $("#message").hide();
        var form = 'query_form';
        var url = base_url + folder + controller + '/query';
        ajax_post_view(url,
            $('#' + form).serialize(),
            function (data) {
                $("#report_condition button").eq(0).click();
                $("#pane_list").html(data);
                $('.nav-tabs-custom').show();
            },
            function (message) {
                $("#message").html(message);
                $("#message").show();
                $('.nav-tabs-custom').hide();
            }
        );
    }
</script>
<?php assets_js('patrol') ?>
<?php assets_css('patrol') ?>