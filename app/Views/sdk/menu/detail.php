<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">功能表项目信息</h3>
    </div>
    <div class="box-body">
        <div class="row toolbar">
            <div class="col-sm-10">
                <a href="#" class="btn btn-primary" onclick="edit2(this)" data-id="<?= $data->sys0501 ?>"> <i class="fa fa-edit"></i> 修改</a>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-4 control-label">项目名称</label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0502 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Icon</label>
                <div class="col-sm-8">
                    <div class="form-control"><i class="fa <?= $data->sys0503 ?>"></i> <?= $data->sys0503 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">功能项目</label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0402 ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">显示序号</label>
                <div class="col-sm-8">
                    <div class="form-control"><?= $data->sys0504 ?></div>
                </div>
            </div>
        </div>
    </div>
</div>