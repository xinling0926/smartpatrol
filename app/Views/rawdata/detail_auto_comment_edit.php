<div class="window_wrapper">
	<div class="row toolbar">
		<div class="col-sm-12 text-right">
			<button class="btn btn-danger" onclick="save_and_close_dialog()"><i class="fa fa-save"></i> <?=lang('toolbar_del')?></button>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
		</div>
	</div>
    <?php echo form_open('', array('id' => 'isoN_form', "class" => "form-horizontal")); ?>
    <?php echo form_hidden('fmd0106', $data->fmd0106) ?>
    <?php echo form_hidden('fmd0201', $data->fmd0201) ?>
    <?php echo form_hidden('fmd0203', $data->fmd0203) ?>
    <?php echo form_hidden('fmd0701', $data->fmd0701) ?>
    <?php echo form_hidden('master_id', $data->master_id) ?>
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-sm-1">勾選</th>
                        <th class="col-sm-11">註記內容</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rec01s as $rec01) : ?>
                    <tr>
                        <td><?php echo form_checkbox_input('id_'.$rec01->rec0101, $rec01->rec0101, '', FALSE); ?></td>
                        <td><?php echo $rec01->rec0106; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?php assets_css('bootstrap-clockpicker.min', 'clockpicker') ?>
<?php assets_js('bootstrap-clockpicker.min', 'clockpicker') ?>
<script type='text/javascript'>
    $('.time').clockpicker({
        autoclose: true
    });
</script>