<div class="login-box-body">
    <p class="login-box-msg"><?= lang('Auth.reset_password_heading') ?></p>
    <p><?php echo sprintf(lang('Auth.reset_password_subheading'), $min_password_length); ?></p>
    <?php if ($message): ?>
        <div class="alert alert-danger alert-dismissible">
            <?= $message ?>
        </div>
    <?php endif ?>
    <?= form_open() ?>
    <div class="form-group has-feedback">
        <?= form_password_input('new', null, null, 'placeholder=' . lang('Auth.reset_password_new_password_label')) ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback">
        <?= form_password_input('new_confirm', null, null, 'placeholder=' . lang('Auth.reset_password_new_password_confirm_label')) ?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <div class="row">
        <div class="col-xs-8">

        </div>
        <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat"><?= lang('Auth.reset_password_submit_btn') ?></button>
        </div>
    </div>
    <?= form_close() ?>
    <?= anchor('auth/login', lang('Auth.forgot_password_login')) ?> <br>
    <script>
        $(function()
        {
            $('input').eq(0).focus();
        });
    </script>
</div>
