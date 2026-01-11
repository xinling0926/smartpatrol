<div class="row">
    <div class="col-xs-6">
        <div class="box box-primary" id="report_condition">
            <div class="box-header with-border">
                <h3 class="box-title">選擇報表</h3>
            </div>
            <div class="box-body">
                <table class="table table-striped table-hover dataTable">
                    <tbody>
                    <tr>
                        <th>所屬部門</th>
                        <th>報表編號</th>
                        <th>報表名稱</th>
                        <th>報表週期</th>
                    </tr>
                    <?php foreach ($fmd01s as $d) : ?>
                        <tr>
                            <td><?= $d->ent1004 ?></td>
                            <td><a href="javascript:void(0);" onclick='select_report(<?= $d->fmd0101 ?>)'><?= $d->fmd0103 ?></a></td>
                            <td><a href="javascript:void(0);" onclick='select_report(<?= $d->fmd0101 ?>)'><?= $d->fmd0104 ?></a></td>
                            <td><?= $fmd0105_opt[$d->fmd0105] ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-6" id="option"></div>
</div>
<div id="report"></div>
<script type='text/javascript'>
    function select_report(id) {
        $("#report").html('');
        var url = base_url + folder + controller + '/select_report/' + id;
        ajax_load_view(url, function (data) {
            $("#option").html(data);
            var t = $('#option').offset().top;
            $(window).scrollTop(t - 20);
        });
    }

    function show_data() {
        $("#report").html('');
        $('#message').html('');
        $('#message').hide();
        var form = 'excel_form';
        var url = base_url + folder + controller + '/show_data';
        ajax_post_view(url,
            $('#' + form).serialize(),
            function (data) {
                var obj = json_decode(data);
                if (obj.message == 'err') {
                    $("#message").html(obj.description);
                    $("#message").show();
                } else {
                    $('#report').html(obj.report);
                }
            }
        );
    }

    function do_import() {

        var form = 'commit_form';
        var url = base_url + folder + controller + '/do_import';
        ajax_post_view(url,
            $('#' + form).serialize(),
            function (data) {
                var obj = json_decode(data);
                if (obj.message == 'err') {
                    $("#message2").html(obj.description);
                    $("#message2").show();
                } else {
                    $("#message2").parent().html('完成');
                }
            }
        );

        return false;
    }

</script>
