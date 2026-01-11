<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title ?? ''; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<?php assets_css('css/bootstrap', 'bootstrap') ?>
	<?php assets_css('css/font-awesome.min', 'font-awesome-4.7.0') ?>
	<?php assets_css('css/ionicons.min', 'ionicons-2.0.1') ?>
	<?php assets_css('css/AdminLTE', 'AdminLTE2') ?>
	<?php assets_css('css/skins/_all-skins.min', 'AdminLTE2') ?>
	<?php assets_css('spacepro') ?>
    <!--[if lt IE 9]>
    <?php assets_js('html5shiv,min')?>
    <?php assets_js('respond.min')?>
    <![endif]-->
    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>"/>
	<?php assets_js('jQuery-2.1.4.min') ?>
</head>
<body class="layout-top-nav skin-red-light">
<div class="wrapper">
    <header class="main-header">
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <span class="sidebar-toggle"></span>
            <a href="<?php echo base_url() ?>" class="system_title">
                <span><?= $site_title ?? 'Smart Patrol' ?></span>
            </a>
        </nav>
    </header>
    <div class="content-wrapper">
        <section class="content">
            <div class="row toolbar">
                <div class="col-sm-8">
                    <h4><?=lang('EmmaLink.table_title')?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_dev0104')?></label>
                            <div class="col-sm-8">
                                <div class="form-control"><?= $data->dev0104 ?? '' ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_pad0303')?></label>
                            <div class="col-sm-8">
                                <div class="form-control"><?= $data->pad0303 ?? '' ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_pad0305')?></label>
                            <div class="col-sm-8">
                                <div class="form-control"><?= $data->pad0305 ?? '' ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_pad0304')?></label>
                            <div class="col-sm-8">
                                <div class="form-control" style="height:auto;min-height:34px;"><?= $data->pad0304 ?? '' ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_pad0306')?></label>
                            <div class="col-sm-8">
                                <div class="form-control"><?php if (($data->pad0306 ?? 0) == 0) {
										echo '<font color="red">'.lang('EmmaLink.v_pad0306_0').'</font>';
									} elseif ($data->pad0306 == 1) {
										echo lang('EmmaLink.v_pad0306_1');
									} elseif ($data->pad0306 == 2) {
										echo lang('EmmaLink.v_pad0306_2');
									} elseif ($data->pad0306 == 3) {
										echo lang('EmmaLink.v_pad0306_3');
									} elseif ($data->pad0306 == 4) {
										echo lang('EmmaLink.v_pad0306_4');
									} elseif ($data->pad0306 == 5) {
										echo '<font color="#2a7026">'.lang('EmmaLink.v_pad0306_5').'</font>';
									} ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_sys0104')?></label>
                            <div class="col-sm-8">
                                <div class="form-control"><?= $data->sys0103 ?? '' ?><?= $data->sys0104 ?? '' ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><?=lang('EmmaLink.label_pad03z2')?></label>
                            <div class="col-sm-8">
                                <div class="form-control"><?= $data->pad03z2 ?? '' ?></div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php if (isset($data->pad04s) && is_array($data->pad04s) && count($data->pad04s)): ?>
					<?php $i = 1; ?>
					<?php foreach ($data->pad04s as $v): ?>
                        <div class="col-md-6">
                            <div class="box box-warning">
                                <div class="box-header">
                                    <h3 class="box-title"><?=lang('EmmaLink.label_pad0403')?> <?= $i++ ?></h3>
                                </div>
                                <div class="box-body">
                                    <img id="image1" src="<?= base_url($v->pad0403) ?>" style="width:100%;"/>
                                </div>
                            </div>
                        </div>
					<?php endforeach ?>
				<?php endif ?>
            </div>
        </section>
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">Version <b><?= defined('VERSION') ? VERSION : '1.0.0' ?></b>
			<?php echo (ENVIRONMENT === 'development') ? " ,Page rendered in <strong>{elapsed_time}</strong> seconds. Memory usage <strong>{memory_usage}</strong>." : '' ?>
        </div>
        &copy; 2016 <a href="http://www.fetnet.net"> <?=lang('EmmaLink.foot_str')?></a>
    </footer>
</div>
</body>
</html>
