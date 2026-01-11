<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#pane1" data-toggle="tab"><?=lang('index_heading')?></a></li>
		<li class="pull-right">
			<button class="btn btn-default" onclick="edit(this)"><i class="fa fa-fw fa-plus-square-o"></i> <?=lang('index_add_report_btn')?></button>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="pane1">
			<div class="dataTables_filter">
				<?= form_open('', ['id' => 'query_form']); ?>
				<?= form_text_input('search', '', '', ['placeholder' => lang('index_search_placeholder')]) ?>
				<?= form_dropdown_input('fmd0102', $dep_opt) ?>
				<?= form_dropdown_input('fmd0108', $fmd0108_opt, $fmd0108) ?>
				<button class="btn btn-primary" type="submit">
					<i class="fa fa-search"></i> <?= lang('toolbar_search') ?></button>
				<?=form_close()?>
			</div>
			<div id="pane_list">
				<?= view('form/query', get_defined_vars()) ?>
			</div>
		</div>
	</div>
</div>
<script type='text/javascript'>
	function d(id, title){
		close_tab('fmd02');
		close_tab('fmd03');
		close_tab('fmd05');
		detail(id,title);
	}

	function a(id,s){
		ajax_post_view(base_url + folder + controller + '/state',
			'id='+id+'&s='+s,
			function (data) {
				setpage();
			}
		);
	}

	function q(id) {
		layer.confirm('<?=lang('index_dialog_hint')?>', {
			btn: ['<?=lang('index_dialog_confirm_btn')?>', '<?=lang('index_dialog_cancel_btn')?>'], //按钮
			title: "<?=lang('index_dialog_title')?>",
			icon: 3
		}, function (index) {
			a(id,1);
			layer.close(index);
		}, function (index) {
			layer.close(index);
		});
	}

	function fmd0505_onchange(selectObj){
		var fmd0505 = selectObj.options[selectObj.options.selectedIndex].value;

		if (['3', '6'].indexOf(fmd0505) >= 0) {
			$('#fmd05_form .form-group').eq(3).show();
		} else {
			$('#fmd05_form .form-group').eq(3).hide();
		}

		if (['4', '5', '7'].indexOf(fmd0505) >= 0) {
			$('#fmd05_form .form-group').eq(4).show();
		} else {
			$('#fmd05_form .form-group').eq(4).hide();
		}

		if (fmd0505 == 8) {
			$('#fmd05_form .form-group').eq(5).show();
			$('#fmd05_form .form-group').eq(6).show();
			$('#fmd05_form .form-group').eq(9).show();
		} else {
			$('#fmd05_form .form-group').eq(5).hide();
			$('#fmd05_form .form-group').eq(6).hide();
			$('#fmd05_form .form-group').eq(9).hide();
		}

		if (['1', '2', '3', '4', '7'].indexOf(fmd0505) >= 0) {
			$('#fmd05_form .form-group').eq(7).show();
		} else {
			$('#fmd05_form .form-group').eq(7).hide();
		}

		if (['5', '6'].indexOf(fmd0505) >= 0) {
			$('#fmd05_form .form-group').eq(8).show();
		} else {
			$('#fmd05_form .form-group').eq(8).hide();
		}

		if (fmd0505 == 4 || fmd0505 == 5 || fmd0505 == 7) {
			$('#fmd05_form .form-group').eq(10).show();
			$('#fmd05_form .form-group').eq(11).hide();
		} else if (fmd0505 > 0 && fmd0505 < 3) {
			$('#fmd05_form .form-group').eq(10).hide();
			$('#fmd05_form .form-group').eq(11).show();
		} else {
			$('#fmd05_form .form-group').eq(10).hide();
			$('#fmd05_form .form-group').eq(11).hide();
		}
		
		if (fmd0505==9){
            $('#fmd05_form .form-group').eq(12).hide();
            $('#fmd05_form .form-group').eq(14).hide();
            $('#fmd05_form .form-group').eq(15).hide();
        } else {
            $('#fmd05_form .form-group').eq(12).show();
            $('#fmd05_form .form-group').eq(14).show();
            $('#fmd05_form .form-group').eq(15).show();
        }

	}

</script>
