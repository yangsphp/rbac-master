<form class="bt-form pd20 pb70" id="form">
    <div class="line ">
        <span class="tname">权限等级 <span style="color: #f00;">*</span></span>
        <div class="info-r ">
            <select name="post[parent_id]" id="parent_id" class="bt-input-text mr5" style="width:330px">
                <option value="0">顶级权限</option>
                <?php foreach ($menu as $k => $v){?>
                <option <?php if(@$data['parent_id']==$v['id']){echo "selected";}?> value="<?php echo $v['id']?>">|--<?php echo str_repeat("--", $v['level']*2).$v['name']?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="line ">
        <span class="tname">名称 <span style="color: #f00;">*</span></span>
        <div class="info-r ">
            <input name="post[name]" required placeholder="请输入权限名称" class="bt-input-text mr5 " type="text" style="width:330px" value="<?php echo @$data['name']?>">
        </div>
    </div>
    <div class="line ">
        <span class="tname">访问路径</span>
        <div class="info-r ">
            <input name="post[url]" class="bt-input-text mr5" type="text" style="width:330px" value="<?php echo @$data['url']?>">
        </div>
    </div>
    <div class="line ">
        <span class="tname">菜单图标</span>
        <div class="info-r ">
            <input name="post[icon]" class="bt-input-text mr5" type="text" style="width:330px" value="<?php echo @$data['icon']?>">
        </div>
    </div>
    <div class="line ">
        <span class="tname">排序</span>
        <div class="info-r ">
            <input name="post[sort]" class="bt-input-text mr5" type="number" min="0" style="width:330px" value="<?php echo isset($data['sort'])?$data['sort']:0?>">
        </div>
    </div>
    <div class="line ">
        <span class="tname">是否菜单</span>
        <div class="info-r " style="padding-top: 6px;">
            <input name="post[is_menu]" id="is_menu_1" <?php echo $data['is_menu']==1?'checked':''?> class="bt-input-text mr5 minimal-blue" type="radio" style="width:30px" value="1">
            &nbsp;
            <label for="is_menu_1" style="vertical-align: -2px;cursor: pointer"> 是</label>
            &nbsp;&nbsp;
            <input name="post[is_menu]" id="is_menu_2" <?php echo $data['is_menu']==0?'checked':''?> class="bt-input-text mr5 minimal-blue" type="radio" style="width:30px" value="0">
            &nbsp;
            <label for="is_menu_2" style="vertical-align: -2px;cursor: pointer"> 否</label>
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
    $('input[type="radio"].minimal-blue').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
</script>