<div class="row toolbar">
    <div class="col-sm-8">
		<?php if (!isset($fmd20) or ($fmd20->fmd2003 == 2)) : ?>
            <a href="#" class="btn btn-primary" onclick="check_out(this)" data-id="<?= $fmd01->fmd0101 ?>"> <i
                        class="fa fa-edit"></i> <?= lang('toolbar_edit') ?></a>
		<?php else : ?>
			<?php echo form_open(base_url('approve_setting/edit/' . $fmd01->fmd0101), array('id' => 'edit_form', "class" => "form-horizontal")); ?>
            <a href="#" class="btn btn-primary" onclick="s()"> <i class="fa fa-edit"></i> <?= lang('toolbar_commit') ?></a>
			<?= form_close() ?>
		<?php endif ?>
    </div>
    <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?= lang('toolbar_close') ?></a>
    </div>
</div>
<div class="alert alert-danger alert-dismissible" style="display: none;" id="message2"></div>
<div class="row form-horizontal">
    <div class="form-group col-sm-3">
        <label class="col-sm-4 control-label"><?= lang('f_ent1004') ?></label>
        <div class="col-sm-8">
            <div class="form-control"><?= $fmd01->ent1004 ?></div>
        </div>
    </div>
    <div class="form-group col-sm-3">
        <label class="col-sm-4 control-label"><?= lang('f_fmd0103') ?></label>
        <div class="col-sm-8">
            <div class="form-control"><?= $fmd01->fmd0103 ?></div>
        </div>
    </div>
    <div class="form-group col-sm-6">
        <label class="col-sm-2 control-label"><?= lang('f_fmd0104') ?></label>
        <div class="col-sm-10">
            <div class="form-control"><?= $fmd01->fmd0104 ?></div>
        </div>
    </div>
    <div class="form-group col-sm-3">
        <label class="col-sm-4 control-label"><?= lang('f_fmd0105') ?></label>
        <div class="col-sm-8">
            <div class="form-control"><?= $fmd0105_opt[$fmd01->fmd0105] ?></div>
        </div>
    </div>
    <div class="form-group col-sm-3">
        <label class="col-sm-4 control-label"><?= lang('f_fmd0107') ?></label>
        <div class="col-sm-8">
            <div class="form-control"><?= $fmd01->fmd0107 ?></div>
        </div>
    </div>
    <div class="form-group col-sm-6">
        <label class="col-sm-2 control-label"><?= lang('f_fmd2003') ?></label>
        <div class="col-sm-4">
            <div class="form-control"><?php if (!isset($fmd20)) {
					echo lang('no_setting');
				} else {
					echo $fmd2003_opt[$fmd20->fmd2003];
				} ?></div>
        </div>
    </div>
</div>
<?php if (isset($fmd20)) : ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?= lang('sign_info_heading') ?></h3>
            <div class="pull-right">
				<?php if ((isset($fmd20) and ($fmd20->fmd2003 == 1))) : ?>
					<?php if ($fmd02s) : ?>
                        <button class="btn btn-sm btn-primary" onclick="edit_dialog(this)" data-action="edit_fmd21" data-fmd0101="<?= $fmd01->fmd0101
						?>"><i class="fa fa-plus"></i> <?= lang('edit_fmd21_btn') ?></button>
					<?php endif ?>
                    <button class="btn btn-sm btn-primary" onclick="edit_dialog(this)" data-action="edit_fmd22" data-fmd0101="<?= $fmd01->fmd0101 ?>">
                        <i class="fa fa-plus"></i> <?= lang('edit_fmd22_btn') ?></button>
				<?php endif ?>
            </div>
        </div>
        <div class="box-body">
			<?php
			if ($fmd02s) {
				echo "<table class=\"table table-striped table-hover dataTable\">";
				echo "<tr><th>" . lang('no') . "</th><th>" . lang('f_fmd2104') . "</th><th>" . lang('f_fmd2105') . "</th><th>" . lang('f_fmd2204')
					. "</th><th>" . lang('f_fmd2205') . "</th><th>" . lang('f_fmd2206') . "</th><th>" . lang('f_fmd2207') . "</th></tr>";
				foreach ($fmd21s as $fmd21) {
					$n = 0;
					$fmd22_output = '';
					foreach ($fmd22s as $fmd22) {
						if ($fmd22->fmd2203 == $fmd21->fmd2101) {
							if ($n) $fmd22_output .= '</tr><tr>';
							$fmd22_output .= "<td>{$fmd22->fmd2204}</td>";
							if ($fmd20->fmd2003 == 1) {
								$fmd22_output .= "<td class='a' onclick='edit_dialog(this)' data-action='edit_fmd22'";
								$fmd22_output .= " data-id='{$fmd22->fmd2201}'>{$fmd22->fmd2205}</td>";
							} else {
								$fmd22_output .= "<td>{$fmd22->fmd2205}</td>";
							}
							$fmd22_output .= "<td>{$ent10s[$fmd22->fmd2206]}</td><td>{$fmd22->fmd2207}</td>";
							$n++;
						}
					}
					if ($n > 1) {
						$rowspan = ' rowspan="' . $n . '"';
					} else {
						$rowspan = '';
					}
					if ($n == 0) {
						$fmd22_output = '<td></td><td></td><td></td><td></td>';
					}
					echo "<tr><td{$rowspan}>{$fmd21->fmd2103}</td>";
					if ($fmd20->fmd2003 == 1) {
						echo "<td{$rowspan} class='a' onclick='edit_dialog(this)' data-action='edit_fmd21'";
						echo " data-id='{$fmd21->fmd2101}'>{$fmd21->fmd2104}</td>";
					} else {
						echo "<td{$rowspan}>{$fmd21->fmd2104}</td>";
					}
					echo "<td{$rowspan}>";
					$s = [];
					foreach (explode(',', $fmd21->fmd2105) as $item) {
						$s[] = $fmd02s[$item];
					}
					echo implode(',', $s);
					echo "</td>";
					echo $fmd22_output;
					echo "</tr>";
				}
				echo "</table>";
			} else {
				echo "<table class=\"table table-striped table-hover dataTable\">";
				echo "<tr><th>" . lang('f_fmd2204') . "</th><th>" . lang('f_fmd2205') . "</th><th>" . lang('f_fmd2206') . "</th><th>" . lang('f_fmd2207') . "</th></tr>";
				foreach ($fmd22s as $fmd22) {
					echo "<tr><td>{$fmd22->fmd2204}</td><td class='a' onclick='edit_dialog(this)'";
					echo " data-action='edit_fmd22' data-id='{$fmd22->fmd2201}'>{$fmd22->fmd2205}</td>";
					echo "<td>{$ent10s[$fmd22->fmd2206]}</td><td>{$fmd22->fmd2207}</td></tr>";
				}
				echo "</table>";
			}
			?>
        </div>
    </div>
<?php endif ?>
<script type='text/javascript'>
    function s() {
        $("#message2").html('');
        $("#message2").hide();
        ajax_post_view($('#edit_form').attr('action'),
            $('#edit_form').serialize(),
            function (data) {
                var dataObj = json_decode(data);
                if (dataObj.message == 'OK') {
                    detail(dataObj.id, dataObj.title);
                    setpage();
                } else {
                    $("#message2").html(dataObj.message);
                    $("#message2").show();
                }
            }
        );
    }
</script>
