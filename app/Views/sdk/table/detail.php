<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#pane_info" data-toggle="tab">Info</a></li>
        <li><a href="#pane_field" data-toggle="tab">Fields</a></li>
        <li class="pull-right">
            <button class="btn btn-default" onclick="edit(this)"><i class="fa fa-fw fa-plus-square-o"></i>Create Table</button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="pane_info">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Table Name</label>
                    <div class="col-sm-2">
                        <div class="form-control"><?= $data->sys3002 ?></div>
                    </div>
                    <label class="col-sm-2 control-label">Comment</label>
                    <div class="col-sm-6">
                        <div class="form-control" style="height: 114px"><?= str_replace("\n", "<br>", $data->sys3003) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="pane_field">
        </div>
    </div>
</div>