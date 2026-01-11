/**
 *
 * spacepro.js
 *
 * Copyright (c) 2015 数联信息技术(上海)有限公司 (http://www.duit.com.cn)
 *
 */

function ajax_load_view(url, success_callback) {
    if (url.indexOf('?') > -1) {
        url += '&' + jQuery.now();
    } else {
        url += '?' + jQuery.now();
    }
    $.ajax({
        url: url,
        beforeSend: function () {
            show_loading();
        },
        error: function (request) {
            close_loading();
            show_error(request.responseText);
        },
        success: function (data) {
            close_loading();
            if (typeof success_callback === "function") {
                success_callback(data);
            }
        }
    });
}

function ajax_post_view(url, data, success_callback, error_callback) {
    if (typeof(data) == 'object') {
        if (data.cuid == null) {
            data.cuid = cuid;
        }
    } else {
        if (data.indexOf('cuid=') < 0) data = data + "&cuid=" + cuid;
    }
    $.ajax({
        url: url,
        data: data,
        type: "POST",
        beforeSend: function () {
            show_loading();
        },
        error: function (request) {
            close_loading();
            if (typeof error_callback === "function") {
                error_callback(request.responseText);
            } else {
                show_error(request.responseText);
            }
        },
        success: function (data) {
            close_loading();
            if (typeof success_callback === "function") {
                success_callback(data);
            }
        }
    });
}

function json_decode(data) {
    try {
        if (typeof data === 'object') {
            return data;
        }
        return JSON.parse(data);
    } catch (e) {
        console.error('JSON parse error:', e, data);
        alert('資料解析錯誤: ' + (data.substring ? data.substring(0, 200) : data));
        return {message: '資料解析錯誤'};
    }
}

var popup_window;
function show_popup_window(url, width, height) {
    ajax_load_view(url, function (data) {
        popup_window = layer.open({
            type: 1,
            title: false,
            shadeClose: false,
            shade: 0.2,
            skin: 'layui-layer-dialog',
            area: [width, height],
            content: data
        });
    });
}

function close_popup_window() {
    layer.close(popup_window);
}

function show_error(msg) {

    height = Math.ceil(msg.length / 50) * 30 + 140;
    if (height < 170) height = '170';
    if (height > 600) height = '600';

    layer.open({
        type: 0,
        title: "錯誤",
        area: ['480px', height + 'px'],
        icon: 5,
        content: msg
    });
}

function get_tab_index(id) {
    var tabs = $(".tab-content").children(".tab-pane");
    var idx = 0;
    var tab_id = 'pane_' + id;
    for (var i = 1; i < tabs.length; i++) {
        if (tabs.eq(i).attr('id') == tab_id) {
            idx = i;
        }
    }
    return idx;
}

function add_tab(title, id) {
    var tabs = $(".tab-content .tab-pane");
    var idx = get_tab_index(id);
    if (idx > 0) {
        set_tab(idx);
        $(".nav-tabs .active a").html(title);
    } else {
        $(".tab").removeClass("on");
        var html = '<li class="active"><a href="#' + 'pane_' + id + '" data-toggle="tab">' + title + "</a></li>";
        $(".nav-tabs li").removeClass("active");
        $(".nav-tabs .pull-right").before(html);
        $(".tab-pane").removeClass("active");
        $(".tab-content").append('<div class="tab-pane active" id="pane_' + id + '"></div>');
    }
}

// 切換上頭Tab頁面
function set_tab(i) {
    $('.nav-tabs li').removeClass("active");
    $(".nav-tabs li").eq(i).addClass("active");
    $(".tab-pane").removeClass("active");
    $(".tab-pane").eq(i).addClass("active");
}

function close_tab(id) {
    idx = get_tab_index(id);
    if (idx > 0) {
        $(".nav-tabs li").eq(idx).remove();
        $('#pane_' + id).remove();
        set_tab(idx - 1);
    }
}

// 資料列表換頁
var page_url = "";
function setpage(obj) {
    var div, page;
    if (obj) {
        page = $(obj).data('ci-pagination-page');
        div = $(obj).data('div');
        page_url = base_url + folder + controller + '/query/' + page;
    }

    if (page_url == "") {
        page_url = base_url + folder + controller + '/query/1';
    }

    if (!div) {
        div = 'pane_list';
    }

    ajax_load_view(page_url, function (data) {
        $("#" + div).html(data);
    });
}

//顯示明細資料
var data_id = 0;
var data_title = '';
var get_detail_option = '';
function detail(id, title) {

    if (title != null) data_title = title;
    if (id != null) data_id = id;
    var url = base_url + folder + controller + '/detail/' + data_id;
    if (get_detail_option != '') url += '?' + get_detail_option;

    ajax_load_view(url, function (data) {
        add_tab(data_title, 'detail');
        $("#pane_detail").html(data);
    });
}

function sub_detail(obj) {

    var id = $(obj).data('id');
    var title = $(obj).data('title');
    if (title == null) title = $(obj).html();
    var data_item = $(obj).data("item");
    if (data_item == null) {
        data_item = "detail";
        url = base_url + folder + controller + '/detail/' + id;
    } else {
        url = base_url + folder + controller + '/detail_' + data_item + '/' + id;
    }

    ajax_load_view(url, function (data) {
        add_tab(title, data_item);
        $("#pane_" + data_item).html(data);
    });
}

function close_detail() {
    close_tab('detail');
    data_id = 0;
    data_title = "";
}

function edit(obj) {
    var title, id, ext_data = '';

    var data = $(obj).data();
    for (var property in data) {
        if (property == 'title') {
            title = data[property];
        } else if (property == 'id') {
            id = data[property];
        } else {
            if (ext_data) ext_data += '&';
            ext_data += property + '=' + data[property];
        }
    }

    if (title == null) title = $(obj).html();
    if (id == null) id = 0;
    var data_item = $(obj).data("item");
    if (data_item == null) {
        data_item = "detail";
        url = base_url + folder + controller + '/edit/' + id;
    } else {
        url = base_url + folder + controller + '/edit_' + data_item + '/' + id;
    }
    if (ext_data != '') url += '?' + ext_data;

    ajax_load_view(url, function (data) {
        add_tab(title, data_item);
        $("#pane_" + data_item).html(data);
        $("#pane_" + data_item + " .form-control").eq(0).focus();
        $("#pane_" + data_item + " form").submit(function () {
                save();
                return false;
            }
        );
    });
}

function edit_dialog(obj, width, height) {
    var action, id, ext_data = '', title;

    if (width == undefined) {
        if ($(document.body)[0].clientWidth < 769) {
            width = '90%';
        } else {
            width = '40%';
        }
    }
    if (height == undefined) height = 'auto';
    var data = $(obj).data();
    for (var property in data) {
        if (property == 'action') {
            action = data[property];
        } else if (property == 'id') {
            id = data[property];
        } else if (property == 'title') {
            title = data[property];
        } else {
            if (ext_data) ext_data += '&';
            ext_data += property + '=' + data[property];
        }
    }

    if (id == null) id = 0;
    if (title == null) title = $(obj).html();
    var url = base_url + folder + controller + '/' + action + '/' + id;
    if (ext_data != '') url += '?' + ext_data;

    ajax_load_view(url, function (data) {
        layer.open({
            type: 1,
            title: title,
            zIndex: 1000,
            skin: 'layui-layer-rim', //加上边框
            area: [width, height], //宽高
            content: data
        });
        $(".layui-layer form .form-control").eq(0).focus();
        $(".layui-layer form").submit(function () {
                save_and_close_dialog();
                return false;
            }
        );
    });
}

function save_and_close_dialog() {
    $("#message").html('');
    $("#message").hide();
    ajax_post_view($(".layui-layer form").attr('action'),
        $(".layui-layer form").serialize(),
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_dialog();
                detail();
            } else {
                $("#message").html(dataObj.message);
                $("#message").show();
            }
        }
    );
}

function close_dialog() {
    layer.closeAll();
}

function cancel_edit() {
    if (data_id == 0) {
        close_tab('detail');
    } else {
        detail();
    }
}

function save(form, data_item) {
    $("#message").html('');
    $("#message").hide();
    if (form == null) form = 'edit_form';
    if (data_item == null) data_item = 'detail';
    ajax_post_view($('#' + form).attr('action'),
        $('#' + form).serialize(),
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
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

function save_sub_item(form, data_item) {
    $("#message").html('');
    $("#message").hide();
    if (form == null) form = 'edit_form';
    if (data_item == null) data_item = 'detail';
    ajax_post_view($('#' + form).attr('action'),
        $('#' + form).serialize(),
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                close_tab(data_item);
                detail();
                setpage();
            } else {
                $("#message").html(dataObj.message);
                $("#message").show();
            }
        }
    );
}

function del(obj) {
    // Capture id and data_item immediately before showing dialog
    var id = $(obj).data('id');
    var data_item = $(obj).data('item');

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
        do_delete(id, data_item);
        close_dialog();
    }, function (index) {
        layer.close(index);
    });
}

function do_delete(id, data_item) {
    if (data_item == null) {
        var url = base_url + folder + controller + '/delete/';
    } else {
        var url = base_url + folder + controller + '/delete_' + data_item + '/';
    }

    ajax_post_view(url,
        "id=" + id,
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                if (data_item == null) {
                    close_tab('detail');
                    setpage();
                } else {
                    close_tab(data_item);
                    detail();
                }
            } else {
                show_error(dataObj.message);
            }
        }
    );
}

function query() {
    var form = 'query_form';
    var url = base_url + folder + controller + '/query';
    ajax_post_view(url,
        $('#' + form).serialize(),
        function (data) {
            $("#pane_list").html(data);
        }
    );
}

function report() {
    var form = 'query_form';
    var url = base_url + folder + controller + '/report';
    ajax_post_view(url,
        $('#' + form).serialize(),
        function (data) {
            if ($('#report_result').length > 0) {
                $("#report_result").remove();
            }
            $("#report_condition").after(data);
            $('#report_condition button').eq(0).click();
        }
    );
}

function order(id, direction) {
    ajax_post_view(base_url + folder + controller + '/order/',
        "id=" + id + "&direction=" + direction,
        function (data) {
            setpage();
        }
    );
}

function order_sub_item(id, direction, table) {
    ajax_post_view(base_url + folder + controller + '/order/',
        "id=" + id + "&direction=" + direction + "&table=" + table,
        function (data) {
            detail();
        }
    );
}

function download_excel() {
    window.location.href = base_url + folder + controller + '/download_excel/';
}

function detail2() {
    id = $('.tree_div').jstree('get_selected')[0];
    if (id == '0') {
        $("#pane_detail").html('');
        return;
    }
    var url = base_url + folder + controller + '/detail/' + id;
    ajax_load_view(url, function (data) {
        $("#pane_detail").html(data);
    });
}

function add2() {

    parent_id = $('.tree_div').jstree('get_selected')[0];

    url = base_url + folder + controller + '/edit/0/' + id;
    ajax_load_view(url, function (data) {
        $("#pane_detail").html(data);
        idx = 0;
        while ($('#pane_detail input').eq(idx).attr('type') == 'hidden') {
            idx++;
        }
        $('#pane_detail input').eq(idx).focus();
        $("#pane_detail form").submit(function () {
                save();
                return false;
            }
        );
    });
}

function edit2(obj) {
    var id = $(obj).data('id');
    if (id == null) id = 0;
    url = base_url + folder + controller + '/edit/' + id;
    ajax_load_view(url, function (data) {
        $("#pane_detail").html(data);
        idx = 0;
        while ($('#pane_detail input').eq(idx).attr('type') == 'hidden') {
            idx++;
        }
        $('#pane_detail input').eq(idx).focus();
        $("#pane_detail form").submit(function () {
                save();
                return false;
            }
        );
    });
}

function save2(form) {
    if (form == null) form = 'edit_form';
    $("#message").html('');
    $("#message").hide();
    ajax_post_view($('#' + form).attr('action'),
        $('#' + form).serialize(),
        function (data) {
            var dataObj = json_decode(data);
            if (dataObj.message == 'OK') {
                var tree_div = $('.tree_div');
                if (dataObj.parent != null) {
                    tree_div.jstree().load_node(dataObj.parent, function () {
                        $('.tree_div').jstree().open_node(dataObj.parent, function () {
                            if (dataObj.id) {
                                if (tree_div.jstree().get_selected())
                                    $('.tree_div').jstree().deselect_node($('.tree_div').jstree().get_selected());
                                tree_div.jstree().select_node(dataObj.id);
                            }
                        });
                    });
                } else {
                    tree_div.jstree().rename_node(dataObj.id, dataObj.title);
                    tree_div.jstree().deselect_node($('.tree_div').jstree().get_selected());
                    tree_div.jstree().select_node(dataObj.id);
                }
            } else {
                $("#message").html(dataObj.message);
                $("#message").show();
            }
        }
    );
}

function node_query() {
    var id = $('.tree_div').jstree('get_selected')[0];
    var url = base_url + folder + controller + '/query/';
    ajax_post_view(url,
        "node_id=" + id + "&cuid=" + $.cookie('cuid'),
        function (data) {
            $("#pane_list").html(data);
        }
    );
}

var loading_count = 0;
function show_loading() {
    loading_count++;
    if (loading_count === 1) {
        layer.load(2, {shade: [0.2, 'white']});
    }
}

function close_loading() {
    loading_count--;
    if (loading_count <= 0) {
        loading_count = 0;
        layer.closeAll('loading');
    }
}

function scroll_to_anchor(aid) {
    var aTag = $("a[name='" + aid + "']");
    if (aTag.length == 0) {
        setTimeout("scroll_to_anchor('" + aid + "')", 1000);
    } else {
        $('html,body').animate({scrollTop: aTag.offset().top}, 'slow');
    }
}

$(function () {
    $("#query_form").submit(function () {
        query();
        return false;
    });
});

// 關閉滑鼠右鍵
//document.oncontextmenu = function(){
//    return false;
//}
