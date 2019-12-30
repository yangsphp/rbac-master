<style>
    .am-modal-dialog ul > li{

    }
    .am-modal-dialog img {
        border: 1px solid #CCC;
        padding: 2px;
        background: #FFF;
        width: 100%;
        height: 100%;
    }
    .am-modal-dialog .user-info{
        width: 100%;
        height: auto;
        float: right;
        text-align: left;
        box-sizing: border-box;
        text-align: center;
    }
    .am-modal-dialog .user-truename{
        width: 100%;
        font-size: 13px;
        margin-top: 5px;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }
    .am-modal-dialog .user-img{
        width: 100%;
        height: 90px;
        float: left;
    }
</style>
<div class="am-modal-dialog" style="width: 400px;">
    <div class="am-modal-hd" style="text-align: left;">新朋友
        <a href="javascript: void(0)" class="am-close" data-am-modal-close>&times;</a>
    </div>
    <div class="am-modal-bd" style="padding: 15px 20px;">
        <ul class="" style="max-height: 600px;overflow-y: auto;margin: 0;padding: 0;text-align: left;">
            <?php foreach ($friend as $k => $v){?>
                <li>
                    <div class="user_head">
                        <img src="<?php echo $v['head_img']?>"/>
                    </div>
                    <div class="user_text">
                        <p class="user_name"><?php echo $v['truename']?></p>
                        <p class="user_message"><?php echo $v['last_msg']?></p>
                    </div>
                    <?php if ($v['status'] == 0){?>
                    <div  onclick="doAddFriendOk(<?php echo $v['id']?>)" class="user_time" style="background-color: #59923d;color: #fff;padding: 0 8px;margin-top: 8px;cursor: pointer">接受</div>
                    <?php }else{?>
                        <div class="user_time" style="padding: 0 3px;margin-top: 8px">已添加</div>
                    <?php }?>
                </li>
            <?php }?>
        </ul>
    </div>
    <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>">
</div>
<script>
    function doAddFriendOk(uid) {
        var msg_data = '{"type":"add_friend_ok","to_user_id": "' + uid + '"}';
        ws.send(msg_data);
        $("#modal-box").modal('close');
    }
</script>