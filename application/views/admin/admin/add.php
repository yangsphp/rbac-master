<form class="bt-form pd20 pb70" id="form">
    <div class="line ">
        <span class="tname">角色 <span style="color: #f00;">*</span></span>
        <div class="info-r ">
            <select id="role_id" name="post[role_id]" class="bt-input-text mr5" style="width:330px">
                <option value="">请选择角色</option>
                <?php foreach ($role as $k => $v){?>
                <option <?php if (@$data['role_id']==$v['id']){echo "selected";}?> value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="line ">
        <span class="tname">名称 <span style="color: #f00;">*</span></span>
        <div class="info-r ">
            <input name="post[username]" required placeholder="请输入管理员名称" class="bt-input-text mr5 " type="text" style="width:330px" value="<?php echo @$data['username']?>">
        </div>
    </div>
    <div class="line ">
        <span class="tname">密码 <span style="color: #f00;">*</span></span>
        <div class="info-r ">
            <input name="post[password]" required placeholder="<?php if(@$data['password']){echo '不填，则不做修改';}else{echo '请输入管理员密码';}?>" class="bt-input-text mr5 " type="password" style="width:330px" value="">
        </div>
    </div>
    <div class="line ">
        <srpan class="tname">确认密码 <span style="color: #f00;">*</span></srpan>
        <div class="info-r ">
            <input name="post[rpassword]" required placeholder="请重复输入密码" class="bt-input-text mr5 " type="password" style="width:330px" value="">
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

</script>