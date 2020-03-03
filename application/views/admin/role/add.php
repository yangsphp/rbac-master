<style>
    input[type=checkbox]{
        vertical-align: -2px;
    }
</style>
<form class="bt-form pd20 pb70" id="form">
    <div class="line ">
        <span class="tname">名称 <span style="color: #f00;">*</span></span>
        <div class="info-r ">
            <input name="post[name]" required placeholder="请输入角色名称" class="bt-input-text mr5 " type="text" style="width:330px" value="<?php echo @$data['name']?>">
        </div>
    </div>
    <div class="line ">
        <span class="tname">授权菜单</span>
        <div class="info-r ">
            <table class="table" style="width: 430px;">
                <tr>
                    <th width="100">一级菜单</th>
                    <th>二级菜单</th>
                </tr>
                <?php foreach ($menu as $k => $v){?>
                <tr>
                    <td><input type="checkbox" name="post[auth][]" <?php if (@in_array($v['id'], $data['auth'])){echo "checked='true'";}?> value="<?php echo $v['id']?>" onclick="selectAll(this, <?php echo $v['id']?>)" name="" id="role_<?php echo $v['id']?>"><label for="role_<?php echo $v['id']?>">&nbsp;<?php echo $v['name']?></label></td>
                    <td>
                        <?php if (@$v['_child']){foreach ($v['_child'] as $k1 => $v1){?>
                        <div>
                            <input type="checkbox" onclick="selectOne(this, <?php echo $v['id']?>)" name="post[auth][]" <?php if (@in_array($v1['id'], $data['auth'])){echo "checked='true'";}?> class="child_<?php echo $v['id']?>" data-parentid="<?php echo $v['id']?>" id="role_<?php echo $v1['id']?>" value="<?php echo $v1['id']?>"><label for="role_<?php echo $v1['id']?>"> &nbsp;<?php echo $v1['name']?></label>
                        </div>
                        <?php }}?>
                    </td>
                </tr>
                <?php }?>
            </table>
        </div>
    </div>
    <div class="bt-form-submit-btn">
        <button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button>
        <button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button>
    </div>
    <input type="hidden" name="id" value="<?php echo $id;?>">
    <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>">
</form>
<script>
    function selectAll(obj, id) {
        var flag = $(obj).is(":checked");
        if (flag==true) {
            $(".child_"+id).prop("checked", true);
        }else{
            $(".child_"+id).prop("checked", false);
        }
    }

    function selectOne(obj, parent_id) {
        //设置父级的选中状态
        var flag = $(".child_"+parent_id).is(":checked");
        if (flag == true) {
            $("#role_"+parent_id).prop("checked", true);
        } else{
            $("#role_"+parent_id).prop("checked", false);
        }
    }
</script>