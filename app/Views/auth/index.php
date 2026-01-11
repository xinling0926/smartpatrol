<div class="login-box-body">
    <p class="login-box-msg"><?= lang('Auth.login_title') ?></p>
    <?php if (!empty($message)): ?>
    <div class="alert alert-danger alert-dismissible">
        <?= $message ?>
    </div>
    <?php endif ?>
    <?= form_open('auth/login') ?>
    <div class="form-group has-feedback">
        <input type="text" class="form-control" id="identity" name="identity" placeholder="<?= lang('Auth.f_' . ($identity_column ?? 'sys0102')) ?>" value="<?= set_value('identity') ?>">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback">
        <input type="password" class="form-control" id="password" name="password" placeholder="<?= lang('Auth.f_sys0105') ?>">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
    </div>
    <?php if (!empty($use_sec_code)): ?>
    <div class="row">
        <div class="col-xs-4">
            <input type="text" class="form-control" id="sec_code" name="sec_code" placeholder="<?= lang('Auth.sec_code') ?>" onfocus="show_seccode();" autocomplete="off">
        </div>
        <div class="col-xs-4" style="padding: 0;margin-left: -10px;">
            <img id="seccode_img" style="display:none;" onclick="this.src='<?= base_url('auth/secode') ?>?x=' + Math.floor(Math.random()*1000+1)" />
        </div>
    </div>
    <?php endif ?>
    <div class="row">
        <div class="col-xs-8">
            <div class="checkbox">
                <label><input type="checkbox" name="remember"> <?= lang('Auth.login_remember_label') ?></label>
            </div>
        </div>
        <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat"><?= lang('Auth.login_submit_btn') ?></button>
        </div>
    </div>
    <?= form_close() ?>
    <a href="<?= base_url('auth/forgot_password') ?>"><?= lang('Auth.login_forgot_password') ?></a><br>
    <script>
        function show_seccode() {
            if($("#seccode_img").css('display') == "none"){
                $("#seccode_img").attr('src', '<?= base_url('auth/secode') ?>');
                $('#seccode_img').show();
                $('#seccode_img').click();
            }
        }

        $(function() {
            $('input').eq(0).focus();
        });

        if(top.location != '<?= base_url('auth/login') ?>'){
            top.location.href= '<?= base_url('auth/login') ?>';
        }
    </script>
</div>
