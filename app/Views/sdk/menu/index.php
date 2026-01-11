<div class="row">
    <div class="col-xs-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#pane_list" data-toggle="tab">功能表項目</a></li>
                <li class="pull-right">
                    <button class="btn btn-default" onclick="add2()"><i class="fa fa-fw fa-plus-square-o"></i>添加項目</button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="pane_list">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="tree_div"></div>
                        </div>
                        <div class="col-md-8">
                            <div id="pane_detail" style="min-height:400px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo assets_css('jsTree/style.min') ?>
<?php echo assets_js('jstree.min') ?>

<script type='text/javascript'>
    $(function () {
        $('.tree_div').jstree({
            "core": {
                "check_callback": true,
                'animation': false,
                'data': {
                    'url': function (node) {
                        return base_url + folder + controller + '/tree';
                    },
                    'data': function (node) {
                        return {
                            'id': node.id
                        };
                    }
                }
            }
        });
        $(".tree_div").bind("loaded.jstree", function (e, data) {
            $('.tree_div').jstree('select_node', 'ul > li:first');
            $('.tree_div').jstree('open_node', 'ul > li:first');
        });
        $(".tree_div").bind("select_node.jstree", function (event, data) {
            detail2();
        });
    });
</script>