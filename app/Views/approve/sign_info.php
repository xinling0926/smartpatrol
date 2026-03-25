<div class="box box-primary" id="sign_info">
	<div class="box-header">
		<h3 class="box-title"><?=lang('sign_info_heading');?><?php if (!$fmd21s) {
				if ($iso_ds) {
					switch ($iso_ds[0]->c08) {
						case 100:
							echo "<span class=\"label bg-green\">".lang('v_c08_100')."</span>";
							break;
						case 101:
							echo "<span class=\"label bg-red\">".lang('v_c08_101')."</span>";
							break;
						default:
							echo "<span class=\"label bg-orange\">".lang('v_c08_1')."</span>";
							break;
					}
				}
			} ?></h3><a name="approve"></a>
	</div>
	<div class="box-body">
		<?php
		if ($fmd21s) {
			echo "<table class=\"table table-striped table-hover dataTable\">";
			echo "<tr><th>".lang('f_fmd2104')."</th><th>".lang('f_d03')."</th><th colspan='2'>".lang('f_d05')."</th><th>";
			echo lang('f_d04')."</th><th>".lang('f_d06')."</th><th>".lang('f_d07')."</th></tr>";
			foreach ($fmd21s as $fmd21) {
				$n = 0;
				$sign_info = '';
				$c08 = '';
				foreach ($iso_ds as $d) {
					if ($d->c04 == $fmd21->fmd2101) {
						$c08 = $d->c08;
						if ($n) $sign_info .= '</tr><tr>';
						$user = model('Sys01Model')->find($d->d05);
						$sign_info .= "<td>{$d->d03}</td><td>".user_display_name($user)."</td>";
						if ($user->sys0120) {
							$sign_img = base_url("data/sign/" . $user->sys0101 . '/' . $user->sys0120);
						} else {
							$sign_img = base_url('assets/img/no_sign.png');
						}
						$sign_info .= "<td><img src=\"{$sign_img}\" class=\"form-control\" alt=\"".lang('no_sign')."\" style=\"width:129px;height:auto\"></td>";
						$sign_info .= "<td>{$d->d04}</td>";
						$sign_info .= "<td>{$approve_state[$d->d06]}</td><td>{$d->d07}</td>";
						$n++;
					}
				}
				if ($n > 1) {
					$rowspan = ' rowspan="' . $n . '"';
				} else {
					$rowspan = '';
				}
				if ($n == 0) {
					$sign_info = '<td colspan="5">'.lang('no_approve_data').'</td>';
				}

				if ($c08) {
					switch ($c08) {
						case 100:
							$c08 = "<span class=\"label bg-green\">".lang('v_c08_100')."</span>";
							break;
						case 101:
							$c08 = "<span class=\"label bg-red\">".lang('v_c08_101')."</span>";
							break;
						default:
							$c08 = "<span class=\"label bg-orange\">".lang('v_c08_1')."</span>";
							break;
					}
				}
				echo "<tr><td{$rowspan}>{$fmd21->fmd2104} {$c08}</td>{$sign_info}</tr>";
			}
			echo "</table>";
		} else {
			echo "<table class=\"table table-striped table-hover dataTable\">";
			echo "<tr><th>".lang('f_d03')."</th><th colspan='2'>".lang('f_d05')."</th><th>";
			echo lang('f_d04')."</th><th>".lang('f_d06')."</th><th>".lang('f_d07')."</th></tr>";
			foreach ($iso_ds as $d) {
			    echo "<tr>";
				$user = model('Sys01Model')->find($d->d05);
				echo "<td>{$d->d03}</td><td>".user_display_name($user)."</td>";
				if ($user->sys0120) {
					$sign_img = base_url("data/sign/" . $user->sys0101 . '/' . $user->sys0120);
				} else {
					$sign_img = base_url('assets/img/no_sign.png');
				}
				echo "<td><img src=\"{$sign_img}\" class=\"form-control\" alt=\"".lang('no_sign')."\" style=\"width:129px;height:auto\"></td>";
				echo "<td>{$d->d04}</td>";
				echo "<td>{$approve_state[$d->d06]}</td><td>{$d->d07}</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		?>
	</div>
	<div class="box-footer">
	<button class="btn btn-default pull-right" style="margin-left:15px;margin-right:10px" onclick="document.documentElement.scrollTop=0;">回到最上面</button>
	</div>
</div>