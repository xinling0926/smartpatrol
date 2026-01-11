<?php
foreach ($fmd03s as $fmd03) {
	echo "<div class=\"form-group\"><label class=\"col-sm-2 control-label\">{$fmd03->fmd0304}</label><div class=\"col-lg-4 col-sm-8\">";
	echo form_text_input('item'.$fmd03->fmd0303.'_name');
	echo "</div></div>";
}