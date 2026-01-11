<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">資料預覽</h3>
    </div>
    <div class="box-body">
        <table id="patrol_table" class="table table-striped dataTable table-bordered"><thead><?=$header?></thead><tbody><?=$body?></tbody></table>
    </div>

    <div class="box-footer">
        <?php echo form_open('', ['id' => 'commit_form','onsubmit'=> "return false;"]); ?>
        <?php echo form_hidden('excel_file', $excel_file) ?>
        <?php echo form_hidden('fmd0101', $fmd0101) ?>
        現有資料<span class="text-red"><?=$old_record_count?></span>筆，確認存檔後，將會被清除。
        <button class="btn btn-primary pull-right" onclick="do_import()"><i class="fa fa-play-circle"></i> 確認存檔</button>
        </form>
        <span id="message2" class="text-red pull-right" style="display: none;"></span>
    </div>
</div>