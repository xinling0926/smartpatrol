<div class="row toolbar">
    <div class="col-sm-8">
    </div>
    <div class="col-sm-4 text-right">
		<a href="#" class="btn btn-primary" onclick="edit(this)" data-id="<?= $data->fmd1001 ?>"><i class="fa fa-edit"></i> <?=lang('toolbar_edit')?></a>
        <a href="#" class="btn btn-default" onclick="close_detail()"><i class="fa fa-close"></i> <?=lang('toolbar_close')?></a>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_fmd1004')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->fmd1004 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_fmd1005')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->fmd1005 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_dev0104')?></label>
                <div class="col-sm-8">
                    <div class="form-control" style="height:auto;"><?php foreach($data->dev01s as $v){echo $v->dev0104, "<br/>" ;} ?>&nbsp;</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_sys0104')?></label>
                <div class="col-sm-8">
                    <div class="form-control" style="height:auto;"><?php foreach($data->sys01s as $v){echo $v->sys0103,$v->sys0104, "<br/>" ;} ?>&nbsp;</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_fmd1006')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?php if($data->fmd1006==1){echo lang('v_fmd1006_1');}elseif($data->fmd1006==2){echo lang('v_fmd1006_2');}elseif($data->fmd1006==3){echo lang('v_fmd1006_3');} ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php if($data->fmd1006==1){echo lang('v_fmd1006_1');}elseif($data->fmd1006==2){echo lang('v_fmd1006_2');}elseif($data->fmd1006==3){echo lang('v_fmd1006_3');} ?><?=lang('label_fmd1007')?></label>
                <div class="col-sm-8">
                    <div class="form-control" style="height:auto;"><?= $data->fmd1007 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_fmd1008')?></label>
                <div class="col-sm-8">
                    <div class="form-control">
					<?php
					$fmd1008	= explode(",", $data->fmd1008);
					if(in_array(1, $fmd1008))
						echo lang('v_fmd1008_1');
					if(in_array(2, $fmd1008))
						echo lang('v_fmd1008_2');
					if(in_array(3, $fmd1008))
						echo lang('v_fmd1008_3');
					if(in_array(4, $fmd1008))
						echo lang('v_fmd1008_4');
					?>
					</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_fmd1009')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?php if($data->fmd1009){echo lang('v_fmd1009_1') . $data->fmd1009 . lang('v_fmd1009_2');}else{echo lang('v_fmd1009_3');} ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?=lang('label_fmd1010')?></label>
                <div class="col-sm-8">
                    <div class="form-control"><?php if($data->fmd1010){echo lang('v_fmd1010_1');}else{echo '<font color="red">'.lang('v_fmd1010_2').'</font>';} ?></div>
                </div>
            </div>
        </div>
    </div>
</div>