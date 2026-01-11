<div class="row toolbar">
    <div class="col-sm-8">
		<?php if(in_array(30, explode(",", $this->session->permissions)) && $data->pad0306==0){ ?>
		<a href="javascript:;" class="btn btn-primary" onclick="sendto_repair(<?= $data->pad0301 ?>)"> <i class="fa fa-mail-forward"></i> <?=lang('designate_repair_btn')?></a>
		<a href="javascript:;" class="btn btn-danger" onclick="bt_jiean(<?= $data->pad0301 ?>)"> <i class="fa fa-mail-forward"></i> <?=lang('finish_repair_btn')?></a>
		<?php } ?>
    </div>
    <div class="col-sm-4 text-right">
        <a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_dev0104')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->dev0104 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad0303')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->pad0303 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad0305')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->pad0305 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad0304')?></label>
                <div class="col-sm-8">
                    <div class="form-control" style="height:auto;min-height:34px;"><?= $data->pad0304 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad0306')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?php if($data->pad0306==0){echo '<font color="red">'.lang('v_pad0306_0').'</font>';}elseif($data->pad0306==1){echo lang('v_pad0306_1');}elseif($data->pad0306==2){echo lang('v_pad0306_2');}elseif($data->pad0306==3){echo lang('v_pad0306_3');}elseif($data->pad0306==4){echo lang('v_pad0306_4');}elseif($data->pad0306==5){echo '<font color="#2a7026">'.lang('v_pad0306_5').'</font>';} ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad0308')?></label>
                <div class="col-sm-8">
                    <div class="form-control" style="height:auto;min-height:34px;"><?= $data->pad0308 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_sys0104')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0103 ?><?= $data->sys0104 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad03z2')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->pad03z2 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_pad0403')?></label>
                <div class="col-sm-8">
                    <div class="form-control" style="height:auto;min-height:34px;">
						<?php foreach($data->pad04s as $v){
							echo "<a href=\"javascript:;\" onclick=\"$('#image1').attr('src','" . $v->pad0403 . "');\">" . $v->pad0403 . "</a><br/>";
						}?>
					</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title"><?=lang('box_pad04_title')?></h3>
			</div>
			<div class="box-body">
				<div class="" style="height:auto;"><img id="image1" src="<?= is_array($data->pad04s) && count($data->pad04s) ? $data->pad04s[0]->pad0403 : '' ?>" style="max-width:100%;" /></div>
			</div>
        </div>
    </div>
</div>
<script type="text/javascript">
function sendto_repair(id)
{
	var url = "repair_from/sendto/" + id;
	ajax_load_view(url, function (data) {
        add_tab('<?=lang('designate_repair_btn')?>', 'sendto');
        $("#pane_sendto").html(data);
    });
}
function bt_jiean(id)
{
	layer.prompt({
		title: '<?=lang('box_finish_title')?>',
		formType: 2 //prompt风格，支持0-2
	}, function(data){
		ajax_post_view('repair/jiean/' + id, {pad0308:data}, function(data){
			var dataObj = json_decode(data);
			layer.alert(dataObj.info, {
				skin: 'layui-layer-molv' //样式类名
				,closeBtn: 0
				,title: '<?=lang('box_hint_title')?>'
			}, function(){
				layer.closeAll();
				if(dataObj.status == 'success')
				{
					close_detail();
					query();
				}
			});
		});
	});
}
</script>