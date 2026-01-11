<div class="box box-success">
    <div class="box-body">
		<table id="table_list" class="table table-striped table-hover dataTable box">
		<thead>
		<tr>
            <th>Table Name</th>
            <th>Comment</th>
		</tr>
		</thead>
		</table>
	</div>
</div>

<script>
    var table_list;
    $(function () {
        table_list = $('#table_list').DataTable({
            "ajaxSource": "<?php echo base_url('sdk/table/get_table_list')?>",
            "columns": [
                { "data": "sys3002" },
                { "data": "sys3003" }],
            "scrollY": 450,
            "scrollCollapse": true,
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "oLanguage": {
                "sUrl": "<?php echo base_url('assets/datatables/zh_CN.json')?>"
            },
            initComplete:initComplete,
            createdRow: function ( row, data, index ) {
                $(row).data('id',data.sys3001); }
        });
    });

    function initComplete(data){
        var t = $(".dataTables_wrapper .row:first div:first");
        t.removeClass('col-sm-6');
        t.addClass('col-sm-4');
        t.html('<h4>Table List</h4>');
        t = $(".dataTables_filter").parent();
        t.removeClass('col-sm-6');
        t.addClass('col-sm-8');

        table_list.$('tbody tr').on('click', function () {
            if (!$(this).hasClass('active')) {
                table_list.$('tr.active').removeClass('active');
                $(this).addClass('active');
                var url = base_url + folder + controller + '/detail/' + $(this).data('id');
                ajax_load_view(url,function(data){
                    $("#pane_detail").html(data);
                });
            }
        } );

        table_list.$('tbody tr:first').click();
    }

</script>
