<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title;?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?=base_url('/favicon.ico')?>"/>
    <?php assets_css('css/bootstrap.min','bootstrap')?>
    <?php assets_css('css/AdminLTE','AdminLTE2')?>
    <!--[if lt IE 9]>
    <?php assets_js('html5shiv,min')?>
    <?php assets_js('respond.min')?>
    <![endif]-->
    <?php assets_js('jQuery-2.1.4.min')?>
	<style type="text/css">
		.shulianlog {
			position: absolute;
			bottom: 20px;
			right: 20px;
		}
		@media (max-width: 767px) {
			.shulianlog {
				position: relative;
				text-align: right;
			}
		}
	</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-logo">
		<a href="<?=base_url()?>"><b>Smart</b> Patrol</a>
	</div>
	<?php echo $content;?>
</div>
<div class="shulianlog">
	<a style="display:none" href="http://www.fetnet.net" target="_blank"><?=assets_img('fareastone_logo.png',array('width'=>'100px'))?></a>
</div>
<?php assets_js('js/bootstrap.min','bootstrap')?>
<?php assets_js('layer','layer')?>
<?php assets_css('skin/layer', 'layer') ?>
<script type='text/javascript'>
	<?php if (isset($js)){echo $js;}?>
    $(".form-control").eq(0).focus();
</script>
</body>
</html>
