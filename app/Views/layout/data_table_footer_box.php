<div class="box-footer">
    <div class="dataTables_info">
        <?php if (isset($total_rows)): ?>
            共 <?= $total_rows ?> 筆資料
        <?php endif ?>
        <?php if (isset($show_export) and $show_export): ?>
            <a class="btn btn-primary" onclick="download_excel()" href="#"><i class="fa fa-cloud-download"></i> <?=lang('toolbar_excel')?></a>
        <?php endif ?>
    </div>
    <div class="dataTables_paginate">
        <?php if (isset($pager)): ?>
            <?= $pager ?>
        <?php endif ?>
    </div>
</div>