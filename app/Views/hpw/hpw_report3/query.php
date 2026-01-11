<div class="row">
    <div class="col-sm-12">
        <table class="table table-striped table-hover dataTable">
            <tbody>
            <tr>
                <th><?=lang('f_common_no')?></th>
                <th><?=lang('f_ent1004')?></th>
                <th><?=lang('f_fmd0104')?></th>
                <th><?=lang('f_report_time')?></th>
                <th><?=lang('f_fmd0204')?></th>
                <th><?=lang('f_patrol_item')?></th>
                <th><?=lang('f_err')?></th>
                <th><?=lang('f_memo')?></th>
            </tr>
            <?php
            $offset = 1;
            foreach ($detail as $item) {
                echo "<tr><td>{$offset}</td>";
                echo "<td>{$item->ent1004}</td>";
                echo "<td>{$item->fmd0104}</td>";
                echo "<td>{$item->report_time}</td>";
                echo "<td>{$item->fmd0204}</td>";
                echo "<td>{$item->patrol_item}</td>";
                if($item->err == 1){
                    echo "<td>".lang('v_state_1')."</td>";
                    echo "<td>{$item->memo}</td>";
                } elseif($item->err == 2) {
                    echo "<td>".lang('v_state_2')."</td>";
                    echo "<td onclick='editMemo({$item->id},{$item->isona_id},\"{$item->fmd0203}\",\"{$item->fmd0503}\",\"{$item->memo}\",\"{$item->table_name}\")'>{$item->memo}</td>";
                }
                echo "</tr>";
                $offset++;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-sm-5">
        <div class="dataTables_info">
            <?php echo $this->pagination->create_info(); ?>
            <a class="btn btn-primary" onclick="export_excel()" href="#">汇出Excel</a>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="dataTables_paginate">
            <?php echo $this->pagination->create_links(); ?>
        </div>
    </div>
</div>
<!--编辑漏检说明弹窗 start-->
<div class="window_wrapper" id="hpw_report3_wrap" style="display:none;">
    <div class="row toolbar">
        <div class="col-sm-6"></div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-success" onclick="my_save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('toolbar_save')?></button>
            <button class="btn btn-default" onclick="close_dialog()"><i class="fa fa-undo"></i> <?=lang('toolbar_cancel')?></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
        </div>
    </div>
    <?php echo form_open('hpw/hpw_report3/save_memo', array('id' => 'hpw_report3_memo', "class" => "form-horizontal")); ?>
    <div class="form-group">
        <label class="col-sm-3 control-label"><?=lang('label_memo')?></label>
        <div class="col-sm-8">
            <input name="memo" id="memo" value=""/>
        </div>
    </div>
    <input type="submit" style="display: none">
    <input type="hidden" name="id" id="id" value=""/>
    <input type="hidden" name="iso_id" id="iso_id" value=""/>
    <input type="hidden" name="fmd0203" id="fmd0203" value=""/>
    <input type="hidden" name="fmd0503" id="fmd0503" value=""/>
    <input type="hidden" name="table_name" id="table_name" value=""/>
    <?= form_close() ?>
</div>
<!--编辑漏检说明弹窗 end---->
<script type='text/javascript'>
    var page = <?php echo $now_page?>;
    function export_excel() {
        window.location.href = base_url + 'hpw/hpw_report3/export_excel';
    }
    function editMemo(id,iso_id,fmd0203,fmd0503,memo,table_name)
    {
        $("#id").val(id);
        $("#iso_id").val(iso_id);
        $("#fmd0203").val(fmd0203);
        $("#fmd0503").val(fmd0503);
        $("#memo").val(memo);
        $("#table_name").val(table_name);
        layer.open({
            type: 1,
            title: '<?=lang('dialog_title')?>',
            zIndex:1000,
            skin: 'layui-layer-rim', //加上边框
            area: ["30%", "auto"], //宽高
            content: $("#hpw_report3_wrap")
        });
    }
    function my_save_and_close_dialog() {
        $("#message").html('');
        $("#message").hide();
        ajax_post_view($(".layui-layer form").attr('action'),
            $(".layui-layer form").serialize(),
            function (data) {
                var dataObj = json_decode(data);
                if (dataObj.message == 'OK') {
                    close_dialog();
                    show_new_data();
                } else {
                    $("#message").html(dataObj.message);
                    $("#message").show();
                }
            }
        );
    }
    function show_new_data()
    {
        var page_url = base_url + folder + controller + '/query/' + page;
        ajax_load_view(page_url, function (data) {
            $("#pane_list").html(data);
        });
    }
</script>