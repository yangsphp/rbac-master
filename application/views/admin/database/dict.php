<form class="bt-form pd20" id="form" style="padding-bottom: 50px;">
    <div class="line ">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <th>字段名</th>
                <th>字段类型</th>
                <th>注释</th>
            </tr>
            <?php foreach ($dict as $k => $v){?>
            <tr>
                <td><?php echo $v['Field']?></td>
                <td><?php echo $v['Type']?></td>
                <td>
                    <input class="" type="text" name="Field[<?php echo $v['Field']?>]" value="<?php echo $v['comment']?>">
                    <input class="" type="hidden" name="Type[<?php echo $v['Field']?>]" value="<?php echo $v['Type']?>">
                </td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
    <div class="bt-form-submit-btn">
        <button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button>
        <button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button>
    </div>
    <input type="hidden" name="table" value="<?php echo $table; ?>">
    <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
</form>
<script>

</script>