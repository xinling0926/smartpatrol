<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title; ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<?php assets_css('css/bootstrap', 'bootstrap') ?>
	<?php assets_css('css/font-awesome.min', 'font-awesome-4.7.0') ?>
	<?php assets_css('css/AdminLTE', 'AdminLTE2') ?>
	<?php assets_css('css/skins/_all-skins.min', 'AdminLTE2') ?>
	<?php assets_css('spacepro') ?>
	<!--[if lt IE 9]>
	<?php assets_js('html5shiv,min')?>
	<?php assets_js('respond.min')?>
	<![endif]-->
	<link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>"/>
	<?php assets_js('jQuery-2.1.4.min') ?>
	<script type='text/javascript'>
		<?php if (isset($js)) {
			echo $js;
		}?>
	</script>
	<style>
		.clockpicker-popover{
			z-index: 1200 !important;
		}
	</style>
</head>
<body class="sidebar-mini <?=$theme?>">
<div class="wrapper">
	<header class="main-header">
		<div class="logo">
			<span class="logo-mini"></span>
			<div class="logo-lg"></div>
		</div>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top" role="navigation">
			<!-- Sidebar toggle button-->
			<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
				<span class="sr-only">Toggle navigation</span>
			</a>
			<a href="<?php echo base_url() ?>" class="system_title">
				<span><?= $site_title ?? 'Smart Patrol' ?></span>
			</a>
			<!-- Navbar Right Menu -->
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img src="<?= base_url('data/avatar/' . ($user->avatar ?? 'man.png')) ?>" alt="User Image" class="user-image">
							<span class="hidden-xs"><?= $current_user->name ?? '' ?></span>
						</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header">
								<img src="<?= base_url('data/avatar/' . ($user->avatar ?? 'man.png')) ?>" alt="User Image" class="img-circle">
								<p>
									<?= $current_user->name ?? '' ?>
									<?php if (!empty($current_user->sys0119)): ?>
										<?= $current_user->sys0119 ?>
									<?php endif ?>
								</p>
							</li>
							<li class="user-footer">
								<div class="pull-left">
									<a href="<?= base_url('account/profile') ?>" class="btn btn-default btn-flat"><?= lang('Globe.user_profile') ?></a>
								</div>
								<div class="pull-right">
									<a href="<?php echo base_url('auth/logout') ?>" class="btn btn-default btn-flat"><?= lang('Globe.logout') ?></a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<aside class="main-sidebar">
		<div class="enterprise-panel">
			<div class="pull-left logo">
				<?php if (!empty($session_data['ent0105'])): ?>
					<img id="logo" src="<?= base_url("data/logo/" . $session_data['ent0105']) ?>" alt="<?= $session_data['ent0103'] ?? '' ?>">
				<?php else: ?>
					<img id="logo" src="<?= base_url("data/logo/no-logo.png") ?>" alt="<?= $session_data['ent0103'] ?? '' ?>">
				<?php endif ?>
			</div>
			<div class="pull-left name"><?= $session_data['ent0103'] ?? '' ?></div>
		</div>
		<section class="sidebar">
			<ul class="sidebar-menu">
				<li class="header"><?= lang('Globe.main_menu') ?></li>
				<?php echo $main_menu; ?>
			</ul>
		</section>
	</aside>
	<div class="content-wrapper">
		<section class="content-header">
			<h1><?= $page_title ?? '' ?>
				<small><?= ucwords(str_replace("_", " ", $controller_name ?? '')) ?></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> <?= lang('Globe.home') ?></a></li>
				<li class="active"><?= $page_title ?? '' ?></li>
			</ol>
		</section>
		<section class="content">
			<?php echo $content; ?>
		</section>
	</div>
	<footer class="main-footer">
		<div class="pull-right hidden-xs">Version <b><?= defined('VERSION') ? VERSION : '1.0.0' ?></b>
			<?= (ENVIRONMENT === 'development') ? " ,Page rendered in <strong>{elapsed_time}</strong> seconds. Memory usage <strong>{memory_usage}</strong>." : '' ?>
		</div>
	</footer>
</div>

<?php assets_js('jquery.cookie') ?>
<?php assets_js('js/bootstrap.min', 'bootstrap') ?>
<?php assets_js('layer', 'layer') ?>
<?php assets_css('skin/layer', 'layer') ?>
<?php assets_js('js/app', 'AdminLTE2') ?>
<?php assets_js('spacepro') ?>
</body>
</html>
