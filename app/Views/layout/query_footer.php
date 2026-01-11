<div class="row">
    <div class="col-sm-5">
        <div class="dataTables_info">
            <?php if (isset($total_rows) && isset($page_size) && isset($current_page)): ?>
                <?= sprintf(lang('Globe.page_info'), (($current_page - 1) * $page_size) + 1, min($current_page * $page_size, $total_rows), $total_rows) ?>
            <?php endif ?>
            <?php if (isset($show_export) && $show_export): ?>
                <a class="btn btn-primary" onclick="download_excel()" href="#"><i class="fa fa-cloud-download"></i> <?=lang('Globe.toolbar_excel')?></a>
            <?php endif ?>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="dataTables_paginate">
            <?= $pager ?? '' ?>
        </div>
    </div>
</div>