<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>后台管理</title>
    <link href="<?php echo base_url()?>/static/login/css/style.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url()?>/static/login/css/body.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="container">
    <section id="content">
        <form id="login">
            <h1>会员登录</h1>
            <div>
                <input name="post[username]" type="text" id="username" value="" placeholder="用户名"/>
            </div>
            <div>
                <input name="post[password]" type="password" id="password" placeholder="登录密码"/>
            </div>
            <div style="padding: 0 10px;">
                <input type="button" value="登录" class="btn btn-primary" id="js-btn-login" onclick="doLogin()" style="margin-left: 0;clear: left;width: 100%;height: 45px;outline: none;"/>
            </div>
            <input type="hidden" name="<?php echo $csrf['name'];?>" value="<?php echo $csrf['hash'];?>">
        </form>
        <div class="button">
            <span class="help-block u-errormessage" id="js-server-helpinfo">&nbsp;</span>
            <a href="#">下载网盘</a>
        </div> <!-- button -->
    </section><!-- content -->
</div>

<script src="<?php echo base_url()?>static/js/jquery.min.js"></script>
<script src="<?php echo base_url()?>static/plugins/layer/layer.js" type="text/javascript"></script>
<script>
    var siteUrl = '<?php echo site_url("admin")?>';
    function doLogin() {
        loadT = layer.msg('正在提交数据...', { time: 0, icon: 16, shade: [0.3, '#000'] });
        $.post(siteUrl + "/login/doLogin", $("#login").serialize(), function (res) {
            if(res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                window.location.href=siteUrl+"/index";
            }else{
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }
</script>
</body>
</html>
