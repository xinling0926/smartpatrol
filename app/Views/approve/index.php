<div class="box box-primary">
	<div class="box-header with-border">
		<h3 class="box-title"><?=lang('index_heading')?></h3>
	</div>
	<div class="box-body">
		<?php if ($data): ?>
			<table class="table table-striped table-hover">
				<tbody>
				<tr>
					<th><?=lang('f_ent1004')?></th>
					<th><?=lang('f_fmd0103')?></th>
					<th><?=lang('f_fmd0104')?></th>
					<th><?=lang('f_date')?></th>
					<th><?=lang('f_c07')?></th>
					<th><?=lang('f_c06')?></th>
				</tr>
				<?php foreach ($data as $fmd01) : ?>
					<?php foreach ($fmd01->subdata as $item) : ?>
						<tr>
							<td><?= $fmd01->ent1004 ?></td>
							<td><?= $fmd01->fmd0103 ?></td>
							<td><?= $fmd01->fmd0104 ?></td>
							<td><a href="#" id='a<?= $fmd01->fmd0101 ?>_<?= $item->c01 ?>' onclick='approve("<?= $fmd01->fmd0101
								?>","<?= $item->c01 ?>")'><?= $item->date ?><?php if ($item->c04>0) { echo '&nbsp;('.$fmd21s[$item->c04].')'; }
							?></a></td>
							<td><?= $item->c07 ?></td>
							<td><?= $user->name($item->c06) ?></td>
						</tr>
					<?php endforeach ?>
				<?php endforeach ?>
				</tbody>
			</table>
		<?php else : ?>
			<div class="alert alert-success alert-dismissible">
				<h4><i class="icon fa fa-check"></i> <?=lang('message_heading')?></h4><?=lang('message')?>
			</div>
		<?php endif ?>
	</div>
</div>
<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
<div id="report" class=""></div>

<script type='text/javascript'>

	var aid;
	function approve(fmd0101, id) {
		$("#report").html('');
		var url = base_url + folder + controller + '/approve_form/' + fmd0101 + '/' + id;
		aid = '#a' + fmd0101 + '_' + id;
		ajax_load_view(url, function (data) {
			var obj = json_decode(data);
			if (obj.message == 'ERR') {
				$("#message").html(obj.description);
				$("#message").show();
			} else {
				$('#report').html(obj.report);
                if ($('#report .scroll').width()<$('#patrol_table').width()
                    && $('#report .scroll').height()>($(window).height() - 100)) {
                    $('#report .scroll').height($(window).height() - 100);
                }
				var t = $('#report').offset().top;
				$(window).scrollTop(t - 20);
			}
		});
	}

	function send() {
		var url = base_url + folder + controller + '/do_approve';
		ajax_post_view(url,
			$('#approve_form').serialize(),
			function (data) {
				var obj = json_decode(data);
				if (obj.message == 'ERR') {
					$("#message2").html(obj.description);
					$("#message2").show();
				} else {
					$('#approval_zone').parent().remove();
					$('#sign_info').parent().addClass('col-lg-12');
					$('#sign_info').parent().removeClass('col-lg-6');
					$('#sign_info').parent().html(obj.sign_info);
					if (aid != null) {
						$(aid).parent().html($(aid).html() + ' <small class="label bg-green">(<?=lang('approved')?>)<small>');
					}
				}
			}
		);
	}

</script>
<?php assets_js('patrol') ?>
<?php assets_css('patrol') ?>