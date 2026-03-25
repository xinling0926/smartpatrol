/**
 *
 * patrol.js
 *
 * Copyright (c) 2016 数联信息技术(上海)有限公司 (http://www.duit.com.cn)
 *
 */

function check_out(obj) {
    var url = base_url + folder + controller + '/check_out/';
    var id = $(obj).data('id');
    ajax_post_view(url,
        "id=" + id,
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                detail(dataObj.id);
                setpage();
            } else {
                show_error(dataObj.message);
            }
        }
    );
}

function revert(obj) {
    if (langType == 'zh-CN') {
        message = '确定要放棄這個版本的修改?';
        title = '放棄修改询问';
        btnOk = '确定';
        btnCancel = '取消';
    } else {
        message = '確定要放棄這個版本的修改?';
        title = '放棄修改詢問';
        btnOk = '確定';
        btnCancel = '取消';
    }
    layer.confirm(message, {
        btn: [btnOk, btnCancel], //按钮
        title: title,
        icon: 3
    }, function (index) {
        id = $(obj).data('id');
        do_revert(id);
        layer.close(index);
    }, function (index) {
        layer.close(index);
    });
}

function do_revert(id) {
    var url = base_url + folder + controller + '/revert/';
    ajax_post_view(url,
        "id=" + id,
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_tab('detail');
                setpage();
            } else {
                show_error(dataObj.message);
            }
        }
    );
}

function commit(obj) {
    if (langType == 'zh-CN') {
        message = '确定要提交这个版本的修改?';
        title = '提交版本询问';
        btnOk = '确定';
        btnCancel = '取消';
    } else {
        message = '確定要提交這個版本的修改?';
        title = '提交版本詢問';
        btnOk = '確定';
        btnCancel = '取消';
    }
    layer.confirm(message, {
        btn: [btnOk, btnCancel], //按钮
        title: title,
        icon: 3
    }, function (index) {
        id = $(obj).data('id');
        do_commit(id);
        layer.close(index);
    }, function (index) {
        layer.close(index);
    });
}

function do_commit(id) {
    var url = base_url + folder + controller + '/commit/';
    ajax_post_view(url,
        "id=" + id,
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_tab('detail');
                setpage();
            } else {
                show_error(dataObj.message);
            }
        }
    );
}

var selected_item = null;

function contextMenuVisible(key, opt) {
    switch (key) {
        case 'add_sub':
            return (!$(selected_item).data('has-child') || $(selected_item).data('depth') > 1);
    }
    return true;
}

function contextMenuCallback(key, options) {
    var m = "clicked: " + key;
    window.console && console.log(m, options);
    switch (key) {
        case 'edit':
            fmd04_edit();
            break;
        case 'add':
            fmd04_add();
            break;
        case 'delete':
            fmd04_delete();
            break;
        case 'add_sub':
            fmd04_add_sub();
            break;
        case 'copy_fmd04':
            fmd04_copy();
            break;
        case 'up':
            fmd04_up();
            break;
        case 'down':
            fmd04_down();
            break;
        case 'edit_fmd06':
            fmd06_edit();
            break;
        case 'edit_fmd08':
            fmd08_edit();
            break;
        case 'delete_route':
            fmd08_delete();
            break;
        case 'edit_tag':
            fmd09_add();
            break;
        case 'delete_tag':
            fmd09_delete();
            break;
        case 'edit_fmd09':
            fmd09_edit();
            break;
        case 'edit_form':
            fmd09_add_form();
            break;
        case 'delete_form':
            fmd09_delete_form();
            break;
        case 'copy_fmd06':
            fmd06_copy();
            break;
    }
}

function fmd04_edit() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0401');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd04');
    if (langType == 'zh-CN') {
        var title = '新增项目';
    } else {
        var title = '新增項目';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd04_copy() {
    var source_id = $(selected_item).data('fmd0401');
    var url = base_url + folder + controller + '/copy_fmd04';
    var data = 'source=' + source_id;
    ajax_post_view(url, data,
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_dialog();
                detail();
            }
        }
    );
}

function fmd04_add() {
    id = $(selected_item).data('fmd0401');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'add_fmd04');
    if (langType == 'zh-CN') {
        var title = '新增项目';
    } else {
        var title = '新增項目';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd04_add_sub() {
    id = $(selected_item).data('fmd0401');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'add_fmd04_sub');
    if (langType == 'zh-CN') {
        var title = '新增项目';
    } else {
        var title = '新增項目';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd04_delete() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0401');

    if (langType == 'zh-CN') {
        message = '确定要删除这笔资料?';
        title = '删除询问';
        btnOk = '确定';
        btnCancel = '取消';
    } else {
        message = '確定要刪除這筆資料?';
        title = '刪除詢問';
        btnOk = '確定';
        btnCancel = '取消';
    }
    layer.confirm(message, {
        btn: [btnOk, btnCancel], //按钮
        title: title,
        icon: 3
    }, function (index) {
        do_delete(id, 'fmd04');
        layer.close(index);
    }, function (index) {
        layer.close(index);
    });
}

function fmd04_up() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0401');
    var url = base_url + folder + controller + '/order_fmd04/';

    ajax_post_view(url,
        "id=" + id + "&direction=1",
        function (data) {
            detail();
        }
    );
}

function fmd04_down() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0401');
    var url = base_url + folder + controller + '/order_fmd04/';

    ajax_post_view(url,
        "id=" + id + "&direction=2",
        function (data) {
            detail();
        }
    );
}

function fmd06_edit() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0601');

    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd06');
    if (langType == 'zh-CN') {
        var title = '编辑栏位属性';
    } else {
        var title = '編輯欄位屬性';
    }
    $(obj).data('title', title);
    edit_dialog(obj, '60%', '490px');
}

function fmd02_edit(obj) {
    id = $(obj).data('id');

    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd02');
    if (langType == 'zh-CN') {
        var title = '编辑班别属性';
    } else {
        var title = '編輯班別屬性';
    }
    $(obj).data('title', title);
    edit_dialog(obj, '450px', '400px');
}

function fmd08_edit() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0801');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd08');
    if (langType == 'zh-CN') {
        var title = '编辑路线';
    } else {
        var title = '編輯路線';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd08_delete() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0801');

    if (langType == 'zh-CN') {
        message = '确定要删除这条路线?';
        title = '删除询问';
        btnOk = '确定';
        btnCancel = '取消';
    } else {
        message = '確定要刪除這條路線?';
        title = '刪除詢問';
        btnOk = '確定';
        btnCancel = '取消';
    }
    layer.confirm(message, {
        btn: [btnOk, btnCancel], //按钮
        title: title,
        icon: 3
    }, function (index) {
        do_delete(id, 'fmd08');
        layer.close(index);
    }, function (index) {
        layer.close(index);
    });
}

function fmd09_add() {

    id = $(selected_item).data('fmd0801');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'add_fmd09');
    if (langType == 'zh-CN') {
        var title = '新增巡检点';
    } else {
        var title = '新增巡檢點';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd09_edit() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0901');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_fmd09');
    if (langType == 'zh-CN') {
        var title = '修改巡检点';
    } else {
        var title = '修改巡檢點';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd09_add_form() {

    id = $(selected_item).data('fmd0901');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'add_fmd0906');
    if (langType == 'zh-CN') {
        var title = '新增电子表单';
    } else {
        var title = '新增電子表單';
    }
    $(obj).data('title', title);
    edit_dialog(obj);
}

function fmd09_delete() {
    if (selected_item == null) return;
    id = $(selected_item).data('fmd0901');
    if (langType == 'zh-CN') {
        message = '确定要删除这个巡检点?';
        title = '删除询问';
        btnOk = '确定';
        btnCancel = '取消';
    } else {
        message = '確定要刪除這個巡檢點?';
        title = '刪除詢問';
        btnOk = '確定';
        btnCancel = '取消';
    }
    layer.confirm(message, {
        btn: [btnOk, btnCancel], //按钮
        title: title,
        icon: 3
    }, function (index) {
        do_delete(id, 'fmd09');
        layer.close(index);
    }, function (index) {
        layer.close(index);
    });
}

function fmd09_delete_form() {
    if (selected_item == null) return;
    id = $(selected_item).data('id');
    if (langType == 'zh-CN') {
        message = '确定要删除这个电子表单?';
        title = '删除询问';
        btnOk = '确定';
        btnCancel = '取消';
    } else {
        message = '確定要刪除這個電子表單?';
        title = '刪除詢問';
        btnOk = '確定';
        btnCancel = '取消';
    }
    layer.confirm(message, {
        btn: [btnOk, btnCancel], //按钮
        title: title,
        icon: 3
    }, function (index) {
        do_delete(id, 'fmd0906');
        layer.close(index);
    }, function (index) {
        layer.close(index);
    });
}

function fmd06_copy() {
    $('.fmd06_copy_source').addClass('patrol-field');
    $('.fmd06_copy_source').removeClass('fmd06_copy_source');

    $(selected_item).removeClass('patrol-field');
    $(selected_item).addClass('fmd06_copy_source');

    $('.patrol-field').addClass('patrol-field-select');
    $('.patrol-field').removeClass('patrol-field');

    if (langType == 'zh-CN') {
        var title = '请勾选要贴上的巡检项目。';
        var btnOk = '确定';
        var btnCancel = '取消';
    } else {
        var title = '請勾選要貼上的巡檢項目。';
        var btnOk = '確定';
        var btnCancel = '取消';
    }

    $('.content').append('<div class="fmd06_copy_message callout callout-warning"></div>');
    $('.fmd06_copy_message').append('<b>'+title+' </b>');
    $('.fmd06_copy_message').append('<button class="btn btn-success" onclick="save_fmd06_copy()"><i class="fa fa-save"></i> '+btnOk+'</button> ');
    $('.fmd06_copy_message').append('<button class="btn btn-default" onclick="cancel_fmd06_copy()"><i class="fa fa-undo"></i> '+btnCancel+'</button>');
    $('.patrol-field-select').bind('click', fmd06_copy_select);
}

function fmd06_copy_select(e) {
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    } else {
        $(this).addClass('selected');
    }
}

function cancel_fmd06_copy() {
    $('.patrol-field-select').unbind();
    $('.fmd06_copy_message').remove();
    $('.fmd06_copy_source').addClass('patrol-field');
    $('.fmd06_copy_source').removeClass('fmd06_copy_source');
    $('.patrol-field-select').addClass('patrol-field');
    $('.patrol-field-select').removeClass('selected');
    $('.patrol-field-select').removeClass('patrol-field-select');
}

function save_fmd06_copy() {
    $('.patrol-field-select').unbind();
    $('.fmd06_copy_message').remove();

    var id = [];
    $('.patrol-field-select.selected').each(function () {
        id.push($(this).data('fmd0601'));
    });

    var source_id = $('.fmd06_copy_source').data('fmd0601');
    var url = base_url + folder + controller + '/copy_fmd06';
    var data = 'source=' + source_id + '&to=' + id.join('_');
    ajax_post_view(url, data,
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_dialog();
                detail();
            } else {
                cancel_fmd06_copy();
            }
        }
    );
}

function fmd0606_onchange(selectObj) {
    var fmd0606 = selectObj.options[selectObj.options.selectedIndex].value;

    if (['3', '6'].indexOf(fmd0606) >= 0) {
        $('#fmd06_form .form-group').eq(1).show();
    } else {
        $('#fmd06_form .form-group').eq(1).hide();
    }

    if (['4', '5', '7'].indexOf(fmd0606) >= 0) {
        $('#fmd06_form .form-group').eq(2).show();
    } else {
        $('#fmd06_form .form-group').eq(2).hide();
    }

    if (fmd0606 == 8) {
        $('#fmd06_form .form-group').eq(3).show();
        $('#fmd06_form .form-group').eq(4).show();
        $('#fmd06_form .form-group').eq(7).show();
        $('#fmd06_form .form-group').eq(8).show();
        $('#fmd06_form .form-group').eq(9).show();
    } else {
        $('#fmd06_form .form-group').eq(3).hide();
        $('#fmd06_form .form-group').eq(4).hide();
        $('#fmd06_form .form-group').eq(7).hide();
        $('#fmd06_form .form-group').eq(8).hide();
        $('#fmd06_form .form-group').eq(9).hide();
    }

    if (['1', '2', '3', '4', '7'].indexOf(fmd0606) >= 0) {
        $('#fmd06_form .form-group').eq(5).show();
    } else {
        $('#fmd06_form .form-group').eq(5).hide();
    }

    if (['5', '6'].indexOf(fmd0606) >= 0) {
        $('#fmd06_form .form-group').eq(6).show();
    } else {
        $('#fmd06_form .form-group').eq(6).hide();
    }

    if (fmd0606 == 4 || fmd0606 == 5 || fmd0606 == 7) {
        $('#fmd06_form .form-group').eq(10).show();
        $('#fmd06_form .form-group').eq(11).hide();
    } else if (fmd0606 > 0 && fmd0606 < 3) {
        $('#fmd06_form .form-group').eq(10).hide();
        $('#fmd06_form .form-group').eq(11).show();
    } else {
        $('#fmd06_form .form-group').eq(10).hide();
        $('#fmd06_form .form-group').eq(11).hide();
    }
}

function change_detail_page(idx) {
    get_detail_option = "page=" + idx;
    detail();
}

function patrol_form_select(e) {
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
        $(this).html('');
    } else {
        $(this).addClass('selected');
        $(this).html('<i class="fa fa-lg fa-check-square-o" aria-hidden="true"></i>');
    }

    if (langType == 'zh-CN') {
        var title = '请勾选要加入电子表单的巡检项目。';
        var btnOk = '确定';
        var btnCancel = '取消';
    } else {
        var title = '請勾選要加入電子表單的巡檢項目。';
        var btnOk = '確定';
        var btnCancel = '取消';
    }

    if ($('.patrol-form-select.selected').length > 0) {
        if ($('.patrol_form_message').length == 0) {
            $('.content').append('<div class="patrol_form_message callout callout-warning"></div>');
            $('.patrol_form_message').append('<b>'+title+'</b>');
            $('.patrol_form_message').append('<button class="btn btn-success" onclick="add_patrol_field_to_from()"><i class="fa fa-save"></i> '+btnOk+'</button> ');
            $('.patrol_form_message').append('<button class="btn btn-default" onclick="cancel_patrol_field_select()"><i class="fa fa-undo"></i> '+btnCancel+'</button>');
        }
    } else {
        $('.patrol_form_message').remove();
    }
}

function cancel_patrol_field_select() {
    $('.patrol-form-select.selected').html('');
    $('.patrol-form-select.selected').removeClass('selected');
    $('.patrol_form_message').remove();
}

function add_patrol_field_to_from() {
    var id = [];
    $('.patrol-form-select.selected').each(function () {
        id.push($(this).data('id'));
    });

    if (langType == 'zh-CN') {
        var title = '新增电子表单';
    } else {
        var title = '新增電子表單';
    }

    var url = base_url + folder + controller + '/add_fmd07/' + id.join('_');
    ajax_load_view(url,
        function (data) {
            layer.open({
                type: 1,
                title: title,
                skin: 'layui-layer-rim', //加上边框
                area: ['40%', 'auto'], //宽高
                content: data
            });
            $(".layui-layer form .form-control").eq(0).focus();
            $(".layui-layer form").submit(function () {
                    $('.patrol_form_message').remove();
                    save_and_close_dialog();
                    return false;
                }
            );
        }
    );
}

function patrol_form_edit(e) {
    var id = $(this).data('id');
    var obj = {};
    $(obj).data('id', id);
    $(obj).data('action', 'edit_form');

    if (langType == 'zh-CN') {
        var title = '编辑电子表单项目';
    } else {
        var title = '編輯電子表單項目';
    }
    $(obj).data('title', title);
    edit_dialog(obj, '40%', '80%');
}

function show_rawdata(id, master_id) {
    if (master_id === undefined) {
        master_id = '';
    }
    if ($(document.body)[0].clientWidth < 769) {
        var width = '90%';
    } else {
        var width = '70%';
    }
    var height = '90%';
    var url = base_url + 'rawdata/detail_dialog/' + id + '/' + master_id;

    if (langType == 'zh-CN') {
        var title = '巡检纪录';
    } else {
        var title = '巡檢紀錄';
    }
    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            skin: 'layui-layer-rim', //加上边框
            area: [width, height], //宽高
            content: data
        });
    });
}

function show_rawdata_miss(tmpJson) {

    if ($(document.body)[0].clientWidth < 769) {
        var width = '90%';
    } else {
        var width = '70%';
    }
    var height = '90%';
    var url = base_url + 'rawdata/detail_dialog_miss?data=' + encodeURIComponent(tmpJson);
    if (langType == 'zh-CN') {
        var title = '巡检纪录';
    } else {
        var title = '巡檢紀錄';
    }

    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            skin: 'layui-layer-rim', //加上边框
            area: [width, height], //宽高
            content: data
        });
    });
}

function show_rawdata_add_comment(param) {

    var url = base_url + 'rawdata/detail_add_comment/' + param;
    if (langType == 'zh-CN') {
        var title = '新增注记';
    } else {
        var title = '新增註記';
    }

    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            skin: 'layui-layer-rim', //加上边框
            area: ['auto', 'auto'], //宽高
            content: data
        });
    });
}

function show_rawdata_auto_comment_edit(param) {

    if ($(document.body)[0].clientWidth < 769) {
        var width = '50%';
    } else {
        var width = '40%';
    }
    var height = '50%';

    var url = base_url + 'rawdata/detail_auto_comment_edit/' + param;
    if (langType == 'zh-CN') {
        var title = '自动连续注记';
    } else {
        var title = '自動連續註記';
    }

    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            skin: 'layui-layer-rim', //加上边框
            area: [width, height], //宽高
            content: data
        });
    });
}

function showFromHistory(fmd0101) {
    if ($(document.body)[0].clientWidth < 769) {
        var width = '90%';
    } else {
        var width = '600px';
    }
    var height = '50%';
    var url = base_url + 'form_item/form_history/' + fmd0101;

    if (langType == 'zh-CN') {
        var title = '报表版本资讯';
    } else {
        var title = '報表版本資訊';
    }
    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            skin: 'layui-layer-rim', //加上边框
            area: [width, height], //宽高
            content: data
        });
    });
}