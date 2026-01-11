<div class="row">
	<div class="col-xs-6">
		<div class="box box-primary" id="report_condition">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('index_heading')?></h3>
			</div>
			<div class="box-body">
				<table class="table table-striped table-hover dataTable">
					<tbody>
					<tr>
						<th><?=lang('f_ent1004')?></th>
						<th><?=lang('f_fmd0103')?></th>
						<th><?=lang('f_fmd0104')?></th>
						<th><?=lang('f_fmd0105')?></th>
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
	<div class="col-xs-6" id="option"><?php if (isset($def_select_report)) echo $def_select_report; //20190131?></div>
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

	function generate_report() {
		$("#report").html('');
		$('#message').html('');
		$('#message').hide();
		var form = 'report_option_form';
		var url = base_url + folder + controller + '/generate_report';
		ajax_post_view(url,
			$('#' + form).serialize(),
			function (data) {
				var obj = json_decode(data);
				if (obj.message == 'err') {
					$("#message").html(obj.description);
					$("#message").show();
				} else {
					$('.box-body').eq(0).css('overflow', 'auto');
					$('.box-body:first').height($('.box').eq(1).height() - 61);
					$('#report').html(obj.report);
					if ($('#report .scroll').width()<$('#patrol_table').width()
                        && $('#report .scroll').height()>($(window).height() - 100)) {
						$('#report .scroll').height($(window).height() - 100);
					}
					var t = $('#report').offset().top;
					$(window).scrollTop(t - 20);
				}
			}
		);
	}

	function send_report() {
		var form = 'send_form';
		var url = base_url + folder + controller + '/send_report';
		ajax_post_view(url,
			$('#' + form).serialize(),
			function (data) {
				var obj = json_decode(data);
				if (obj.message == 'ERR') {
					$("#message2").html(obj.description);
					$("#message2").show();
				} else {
					$("#message2").parent().html('<?=lang('send_report_suc_hint')?>');
				}
			}
		);
	}

</script>
<?php assets_js('patrol') ?>
<?php assets_css('patrol') ?>