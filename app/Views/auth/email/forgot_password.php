<!DOCTYPE html>
<html>
<body>
<div style="font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: large">
    <h2><a href="<?= $base_url ?>" style="color: #444;font-size: 35px;"><?=$site_title?></a></h2>
	<?=sprintf(lang('email_forgot_password_body_1'),$user_name)?>
    <p><a href="<?= $reset_password_url ?>" style="font-size:14px;background:#3882e5;color:#fff;padding:9px 14px;margin-bottom:10px; text-decoration:none;border-radius:2px;display: inline-block;"><?= lang('email_forgot_password_link') ?></a></p>
	<?=lang('email_forgot_password_body_2')?>
    <p><?=now()?></p>
</div>
</body>
</html>