<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#pane_list" data-toggle="tab"><?= lang('index_heading') ?></a></li>
                <li class="pull-right">
                    <button class="btn btn-default" onclick="edit(this)"><i
                                class="fa fa-fw fa-plus-square-o"></i> <?= lang('add_ent01_btn') ?></button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pane_list">
                    <?= view('enterprise/query', get_defined_vars()) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function save_enterprise(form, data_item) {
        $("#message").html('');
        $("#message").hide();
        if (form == null) form = 'edit_form';
        if (data_item == null) data_item = 'detail';
        ajax_post_view($('#' + form).attr('action'),
            $('#' + form).serialize(),
            function (data) {
                var dataObj = json_decode(data);
                if (dataObj.message == 'OK') {
                    if (dataObj.update_ent0103 == true) {
                        $('.enterprise-panel .name').html(dataObj.title);
                    }
                    if (data_item != 'detail') close_tab(data_item);
                    detail(dataObj.id, dataObj.title);
                    setpage();
                } else {
                    $("#message").html(dataObj.message);
                    $("#message").show();
                }
            }
        );
    }
</script>