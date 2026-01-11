<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $title ?></h3>
    </div>
    <div class="box-body">
        <div class="row toolbar">
            <div class="col-sm-6 col-sm-offset-6 text-right">
                <a href="#" class="btn btn-success" onclick="save2()"><i class="fa fa-save"></i> 存檔</a>
                <a href="#" class="btn btn-default" onclick="detail2()"><i class="fa fa-undo"></i> 取消</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
            </div>
        </div>
        <?php echo form_open('', array('id' => 'edit_form', "class" => "form-horizontal")); ?>
        <?php if ($data) echo form_hidden('sys0501', $data->sys0501) ?>
        <?php if (!$data) echo form_hidden('sys0506', $sys0506) ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">項目名称</label>
            <div class="col-sm-8"><?= form_text_field('sys0502', $data) ?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Icon</label>
            <div class="col-sm-8"><?= form_text_field('sys0503', $data) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">功能项目</label>
            <div class="col-sm-8"><?=form_dropdown_field('sys0505',$pages, $data)?></div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">显示序号</label>
            <div class="col-sm-8"><?= form_text_field('sys0504', $data) ?>
            </div>
        </div>
        <?=form_close()?>
    </div>
</div>