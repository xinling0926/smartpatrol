<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">上傳Excel檔案</h3>
    </div>
    <div class="box-body">
        <div class="alert alert-danger alert-dismissible" style="display: none;" id="message"></div>
        <div class="form-group">
            <label class="col-sm-4 control-label">報表名稱</label>
            <div class="col-sm-8"><span class="form-control"><?= $fmd01->fmd0104 ?></span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-8">
                <?php echo form_open('', ['id' => 'excel_form']); ?>
                <?php echo form_hidden('excel_file', '') ?>
                <?php echo form_hidden('fmd0101', $fmd0101) ?>
                <span class="btn btn-success fileinput-button">
                    <i class="fa fa-upload"></i>
                    <span>上傳Excel檔案</span>
                    <input id="file_upload" type="file" name="files[]" data-url="<?= base_url('tool/form_import/upload') ?>"
                           accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                           multiple>
                </span>
                <span id="upload_error"> </span>
                <div class="progress progress-striped" style="display: none">
                    <div class="progress-bar progress-bar-success" role="progressbar" style="width: 0%;"></div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php assets_js('jquery.ui.widget', 'jQuery-File-Upload/vendor') ?>
<?php assets_js('jquery.iframe-transport', 'jQuery-File-Upload') ?>
<?php assets_js('jquery.fileupload', 'jQuery-File-Upload') ?>
<?php assets_css('jquery.fileupload', 'jQuery-File-Upload/css') ?>
<?php assets_css('jquery.fileupload-ui', 'jQuery-File-Upload/css') ?>

<script>
    $(function () {
        $('#file_upload').fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        $('#upload_error').html(file.error);
                    } else {
                        $('input[name="excel_file"]').val(file.name);
                        show_data(file.name);
                    }
                    $('.progress').hide();
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('.progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            start: function (e) {
                $('#upload_error').html('');
                $('#message').html('');
                $('.progress').show();
            }
        });
    });
</script>