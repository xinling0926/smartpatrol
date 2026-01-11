<div class="row toolbar">
	<div class="col-sm-8">
	</div>
	<div class="col-sm-4 text-right">
		<a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('Globe.toolbar_close')?></a>
	</div>
</div>
<div class="row media">
    <div class="col-md-12">
        <img style='float:left; max-width:540px; margin-right:10px; margin-bottom:10px;' data-id="<?= 'imageContainer_' . $data->pad0701 ?>" />
        <h4>
            <?= $data->sys0103 . $data->sys0104 ?> <small>(<?= lang('Photograph.v_dev0104') ?>：<?= $data->dev0104 ?>,&nbsp;<?= lang('Photograph.v_ent1004') ?>：<?= $data->ent1004 ?>)</small>
        </h4>
        <p><?= $data->pad0705 ?></p>
        <p>&nbsp;</p>
        <em><?= lang('Photograph.v_take_photo') ?> <?= $data->pad0707 ?></em>
    </div>
</div>

<script type='text/javascript'>
$(function() {
	$("img[data-id^='imageContainer_']").each(function() {
		var pad0701 = $(this).attr('data-id').match(/\d+/)[0];
		$(this).attr('src', base_url + folder + controller + '/getPhotograph/' + pad0701 + '/0'); 
	})
})
</script>