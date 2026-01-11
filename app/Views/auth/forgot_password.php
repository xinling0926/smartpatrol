<div class="login-box-body">
    <p class="login-box-msg"><?=lang('forgot_password_heading');?></p>
    <p><?php echo sprintf(lang('forgot_password_subheading'), $identity_label);?></p>
    <?php if ($message): ?>
        <div class="alert alert-danger alert-dismissible">
            <?=$message?>
        </div>
    <?php endif ?>
    <?=form_open()?>
    <div class="form-group has-feedback">
        <?=form_text_input('identity',null,null,'placeholder='.$identity_label)?>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
    </div>
    <div class="row">
        <div class="col-xs-8">

        </div>
        <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat"><?=lang('forgot_password_submit_btn')?></button>
        </div>
    </div>
    <?=form_close()?>
    <?=anchor('auth/login', lang('forgot_password_login')) ?> <br>
    <script>
        $(function()
        {
            $('input').eq(0).focus();
        });
    </script>
</div>


