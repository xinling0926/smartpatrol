<a name="approve"></a>
<div class="box box-primary margin-bottom-none" id="sign_info" style="margin-top: 20px">
	<div class="box-header">
		<h3 class="box-title"><?=lang('QueryReport.sign_info_title')?><?php if (!$fmd21s) {
				if ($iso_ds) {
					switch ($iso_ds[0]->c08) {
						case 100:
							echo "<span class=\"label bg-green\">".lang('QueryReport.v_c08_100')."</span>";
							break;
						case 101:
							echo "<span class=\"label bg-red\">".lang('QueryReport.v_c08_101')."</span>";
							break;
						default:
							echo "<span class=\"label bg-orange\">".lang('QueryReport.v_c08_1~99')."</span>";
							break;
					}
				}
			} ?></h3>
	</div>
	<div class="box-body">
		<?php
		if ($fmd21s) {
			echo "<table class=\"table table-striped table-hover dataTable\">";
			echo "<tr><th>".lang('QueryReport.f_fmd0204')."</th><th>".lang('Globe.f_common_no')."</th><th colspan='2'>".lang('QueryReport.f_sign_user')."</th><th>".lang('QueryReport.f_sign_time')."</th><th>".lang('QueryReport.f_sign_result')."</th><th>".lang('QueryReport.f_sign_remark')."</th></tr>";
			foreach ($fmd21s as $fmd21) {
				$n = 0;
				$sign_info = '';
				$c08 = '';
				foreach ($iso_ds as $d) {
					if ($d->c04 == $fmd21->fmd2101) {
						$c08 = $d->c08;
						if ($n) $sign_info .= '</tr><tr>';
						$user = model('Sys01Model')->get($d->d05);
						$sign_info .= "<td>{$d->d03}</td><td>".user_display_name($user)."</td>";
						if ($user->sys0120) {
							$sign_img = base_url("data/sign/" . $user->sys0101 . '/' . $user->sys0120);
						} else {
							$sign_img = base_url('assets/img/no_sign.png');
						}
						$sign_info .= "<td><img src=\"{$sign_img}\" class=\"form-control\" alt=\"还没有签名档\" style=\"width:129px;height:auto\"></td>";
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
					$sign_info = '<td colspan="5">'.lang('QueryReport.v_not_send_data').'</td>';
				}

				if ($c08) {
					switch ($c08) {
						case 100:
							$c08 = "<span class=\"label bg-green\">".lang('QueryReport.v_c08_100')."</span>";
							break;
						case 101:
							$c08 = "<span class=\"label bg-red\">".lang('QueryReport.v_c08_101')."</span>";
							break;
						default:
							$c08 = "<span class=\"label bg-orange\">".lang('QueryReport.v_c08_1~99')."</span>";
							break;
					}
				}

				echo "<tr><td{$rowspan}>{$fmd21->fmd2104} {$c08}</td>{$sign_info}</tr>";
			}
			echo "</table>";
		} else {
			echo "<table class=\"table table-striped table-hover dataTable\">";
			echo "<tr><th>".lang('Globe.f_common_no')."</th><th colspan='2'>".lang('QueryReport.f_sign_user')."</th><th>".lang('QueryReport.f_sign_time')."</th><th>".lang('QueryReport.f_sign_result')."</th><th>".lang('QueryReport.f_sign_remark')."</th></tr>";
			if ($iso_ds) {
				foreach ($iso_ds as $d) {
					$user = model('Sys01Model')->get($d->d05);
					echo "<td>{$d->d03}</td><td>".user_display_name($user)."</td>";
					if ($user->sys0120) {
						$sign_img = base_url("data/sign/" . $user->sys0101 . '/' . $user->sys0120);
					} else {
						$sign_img = base_url('assets/img/no_sign.png');
					}
					echo "<td><img src=\"{$sign_img}\" class=\"form-control\" alt=\"".lang('QueryReport.sign_img_hint')."\" style=\"width:129px;height:auto\"></td>";
					echo "<td>{$d->d04}</td>";
					echo "<td>{$approve_state[$d->d06]}</td><td>{$d->d07}</td></tr>";
				}
			} else {
				echo '<tr><td colspan="5">'.lang('QueryReport.v_not_send_data').'</td></tr>';
			}
			echo "</table>";
		}
		?>
	</div>
</div>
