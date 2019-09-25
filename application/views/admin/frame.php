<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AdminLite</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url()?>static/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url()?>static/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url()?>static/css/ionicons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url()?>static/css/AdminLTE.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo base_url()?>static/js/jquery.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url()?>static/css/datatables/dataTables.bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url()?>static/css/iCheck/all.css">
    <script src="<?php echo base_url()?>static/js/plugins/datatables/jquery.dataTables.js"></script>
    <script src="<?php echo base_url()?>static/js/plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="<?php echo base_url()?>static/plugins/blockUI/jquery.blockUI.js"></script>
    <script>
        var siteUrl = '<?php echo site_url("admin")?>';
        var baseUrl = '<?php echo base_url()?>';
        // 加载显示
        function showLoading(msg) {
            var options = {
                boxed: true,
                message: msg,
                overlayColor: false
            }
            $.blockUI({
                message: '<div class="loading-message ' + (options.boxed ? 'loading-message-boxed' : '') + '"><img src="' + baseUrl + '/static/plugins/blockUI/loading.gif" /><span>&nbsp;&nbsp;' + (options.message ? options.message : '加载中...') + '</span></div>',
                fadeIn: 1000,
                css: {
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none',
                    zIndex: '3000',
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor ? options.overlayColor : '#555',
                    opacity: options.boxed ? 0.05 : 0.1,
                    cursor: 'wait'
                }
            });
        }
        // 隐藏显示
        function hideLoading() {
            $.unblockUI();
        }
        function loading() {
            loadT = layer.msg('正在提交数据...', { time: 0, icon: 16, shade: [0.3, '#000'] });
        }
    </script>
    <style>
        table{
            font-size: 12px;
            border: 1px solid #dddddd;
        }
        table > thead{
            background-color: #f3f4f5;
        }
        .table>thead>tr>th{
            border: none;
            font-weight: normal;
        }
        .pagination > li:first-of-type a{
            border-bottom-left-radius: 4px;
            border-top-left-radius: 4px;
        }
        .pagination > li:last-of-type a{
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .pagination > li:first-of-type a, .pagination > li:last-of-type a{
            padding: 4px 8px;
            font-size: 12px;
        }
        .pagination > li > a{
            padding: 4px 12px;
            font-size: 12px;
        }
        div.dataTables_info{
            font-size: 12px;
        }
        /*按钮样式*/
        .btn-xs{
            padding: 4px 5px;
        }
        .my-btn{
            padding: 0 4px;
        }
        /*加载动画*/
        /***
        UI Loading
        ***/
        .loading-message {
            display: inline-block;
            min-width: 125px;
            margin-left: -60px;
            padding: 10px;
            margin: 0 auto;
            color: #000 !important;
            font-size: 13px;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
        }

        .loading-message.loading-message-boxed {
            border: 1px solid #ddd;
            background-color: #fff;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            -ms-border-radius: 4px;
            -o-border-radius: 4px;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
            -moz-box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
        }

        .loading-message > span {
            line-height: 20px;
            vertical-align: middle;
        }

        label.error {
            background:url("<?php echo base_url()?>static/plugins/validation/demo/images/unchecked.gif") no-repeat 0px 2.5px;
            padding-left: 16px;
            color: #f56954;
        }
        label.success {
            background:url("<?php echo base_url()?>static/plugins/validation/demo/images/checked.gif") no-repeat 0px 2.5px;
            padding-left: 16px;
            color: #00a65a ;
        }
        .bt-form{
            height: 100%;
            font-size: 12px;
        }
        .pb70 {
            padding-bottom: 70px !important;
        }
        .pd20 {
            padding: 20px;
        }
        .line {
            padding: 5px 0;
        }
        .line .tname {
            display: block;
            float: left;
            height: 32px;
            line-height: 32px;
            overflow: hidden;
            padding-right: 20px;
            text-align: right;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100px;
        }
        .line .info-r {
            margin-bottom: 5px;
            margin-left: 100px;
            position: relative;
        }
        .bt-input-text {
            border: 1px solid #ccc;
            height: 30px;
            line-height: 30px;
            padding-left: 5px;
            border-radius: 2px;
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }
        .mr5 {
            margin-right: 5px;
        }
        .bt-form-submit-btn {
            background: #f6f8f8;
            border-top: 1px solid #edf1f2;
            bottom: 0;
            left: 0;
            padding: 8px 20px 10px;
            position: absolute;
            text-align: right;
            width: 100%;
        }
        .bt-form-submit-btn .btn:first-child {
            margin-right: 4px;
        }
        .btn-group-sm>.btn, .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }
        .btn-my{
            background-color: #cbcbcb;
            border-color: #cbcbcb;
            color: #fff;
        }
        .btn-my:hover{
            color: #fff;
            background-color: #d9534f;
            border-color: #d43f3a;
        }
        .layui-layer-setwin .layui-layer-close2:hover{
            transform:rotate(360deg) !important;
        }
    </style>
</head>
<?php
    $menuList = $this->session->userdata("menuList");
    $user = $this->session->userdata("user");
?>
<body class="skin-blue">
<header class="header">
    <a href="<?php echo site_url('admin/index')?>" class="logo">
        综合管理系统
    </a>
    <nav class="navbar navbar-<?php echo base_url()?>static-top" role="navigation" style="border-radius: 0;">
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope"></i>
                        <span class="label label-success">4</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 4 messages</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- start message -->
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?php echo base_url()?>static/img/avatar3.png" class="img-circle" alt="User Image"/>
                                        </div>
                                        <h4>
                                            Support Team
                                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li><!-- end message -->
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?php echo base_url()?>static/img/avatar2.png" class="img-circle" alt="user image"/>
                                        </div>
                                        <h4>
                                            AdminLTE Design Team
                                            <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?php echo base_url()?>static/img/avatar.png" class="img-circle" alt="user image"/>
                                        </div>
                                        <h4>
                                            Developers
                                            <small><i class="fa fa-clock-o"></i> Today</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?php echo base_url()?>static/img/avatar2.png" class="img-circle" alt="user image"/>
                                        </div>
                                        <h4>
                                            Sales Department
                                            <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <div class="pull-left">
                                            <img src="<?php echo base_url()?>static/img/avatar.png" class="img-circle" alt="user image"/>
                                        </div>
                                        <h4>
                                            Reviewers
                                            <small><i class="fa fa-clock-o"></i> 2 days</small>
                                        </h4>
                                        <p>Why not buy a new awesome theme?</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">See All Messages</a></li>
                    </ul>
                </li>
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-warning"></i>
                        <span class="label label-warning">10</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 10 notifications</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li>
                                    <a href="#">
                                        <i class="ion ion-ios7-people info"></i> 5 new members joined today
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-warning danger"></i> Very long description here that may not fit
                                        into the page and may cause design problems
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-users warning"></i> 5 new members joined
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="ion ion-ios7-cart success"></i> 25 sales made
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="ion ion-ios7-person danger"></i> You changed your username
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="#">View all</a></li>
                    </ul>
                </li>
                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-tasks"></i>
                        <span class="label label-danger">9</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">You have 9 tasks</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Design some buttons
                                            <small class="pull-right">20%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">20% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Create a nice theme
                                            <small class="pull-right">40%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-green" style="width: 40%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">40% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Some task I need to do
                                            <small class="pull-right">60%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-red" style="width: 60%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">60% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            Make beautiful transitions
                                            <small class="pull-right">80%</small>
                                        </h3>
                                        <div class="progress xs">
                                            <div class="progress-bar progress-bar-yellow" style="width: 80%"
                                                 role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                 aria-valuemax="100">
                                                <span class="sr-only">80% Complete</span>
                                            </div>
                                        </div>
                                    </a>
                                </li><!-- end task item -->
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="#">View all tasks</a>
                        </li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-user"></i>
                        <span><?php echo $user['username']?> <i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header bg-light-blue">
                            <img src="<?php echo base_url()?>static/img/avatar3.png" class="img-circle" alt="User Image"/>
                            <p>
                                <?php echo $user['username']?> - <?php echo $user['role_name']?>
                                <small>Member since Nov. 2012</small>
                            </p>
                        </li>
                        <!-- Menu Body -->
<!--                        <li class="user-body">-->
<!--                            <div class="col-xs-4 text-center">-->
<!--                                <a href="#">Followers</a>-->
<!--                            </div>-->
<!--                            <div class="col-xs-4 text-center">-->
<!--                                <a href="#">Sales</a>-->
<!--                            </div>-->
<!--                            <div class="col-xs-4 text-center">-->
<!--                                <a href="#">Friends</a>-->
<!--                            </div>-->
<!--                        </li>-->
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">个人中心</a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo site_url('admin/login/logout')?>" class="btn btn-default btn-flat">退出登录</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo base_url()?>static/img/avatar3.png" class="img-circle" alt="User Image"/>
                </div>
                <div class="pull-left info">
                    <p>Hello, <?php echo $user['username']?></p>

                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- search form -->
            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search..."/>
                    <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i
                                            class="fa fa-search"></i></button>
                            </span>
                </div>
            </form>
            <!-- /.search form -->
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="active">
                    <a href="<?php echo site_url('admin/index')?>">
                        <i class="fa fa-dashboard"></i> <span>控制台</span>
                    </a>
                </li>
                <?php if ($menuList) {foreach ($menuList as $k => $v){?>
                <li id="menu-<?php echo $k?>" class="<?php if (isset($v['submenu'])){echo 'treeview';}?>">
                    <a href="<?php if(empty($v['url'])){echo '#';}else{echo site_url($v['url']);}?>">
                        <i class="fa fa-bar-chart-o"></i>
                        <span><?php echo $v['name']?></span>
                        <?php
                            if (isset($v['submenu'])){
                                echo '<i class="fa fa-angle-left pull-right"></i>';
                            }
                        ?>
                    </a>
                    <?php if (isset($v['submenu'])){?>
                    <ul class="treeview-menu">
                        <?php foreach ($v['submenu'] as $k1 => $v1){?>
                        <li>
                            <a href="<?php echo site_url($v1['url']).'?_='.$k.'_'.$k1?>"><i class="fa fa-angle-double-right"></i> <?php echo $v1['name']?></a>
                        </li>
                        <?php }?>
                    </ul>
                    <?php }?>
                </li>
                <?php }}?>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="right-side">
        <?php
            if ($layout) {
                echo $layout;
            }
        ?>
    </aside><!-- /.right-side -->
</div>

<!-- jQuery UI 1.10.3 -->
<script src="<?php echo base_url()?>static/js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
<!-- Bootstrap -->
<script src="<?php echo base_url()?>static/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Morris.js charts -->
<script src="<?php echo base_url()?>static/js/raphael-min.js"></script>
<!--<script src="--><?php //echo base_url()?><!--static/js/plugins/morris/morris.min.js" type="text/javascript"></script>-->
<!-- Sparkline -->
<script src="<?php echo base_url()?>static/js/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
<!-- jvectormap -->
<script src="<?php echo base_url()?>static/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>static/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
<!-- fullCalendar -->
<script src="<?php echo base_url()?>static/js/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo base_url()?>static/js/plugins/jqueryKnob/jquery.knob.js" type="text/javascript"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url()?>static/js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url()?>static/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
<!-- iCheck -->
<script src="<?php echo base_url()?>static/js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

<!-- AdminLTE App -->
<script src="<?php echo base_url()?>static/js/AdminLTE/app.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>static/plugins/validation/dist/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>static/plugins/validation/dist/localization/messages_zh.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>static/plugins/layer/layer.js" type="text/javascript"></script>


<script type="text/javascript">
    // 设置选中导航
    setNavBarSelect();

    function setNavBarSelect() {
        var navIndex = getQueryString("_");
        if (navIndex) {
            var navArr = navIndex.split("_");
            $("#menu-" + navArr[0]).addClass("active");
            $("#menu-" + navArr[0] + " .treeview-menu li").eq(navArr[1]).addClass("active");
        }
    }

    // 获取queryString
    function getQueryString(name) {
        var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
        var r = window.location.search.substr(1).match(reg);
        if (r != null) {
            return unescape(r[2]);
        }
        return null;
    }

    //汉化表格
    var oLanguage={
        "oAria": {
            "sSortAscending": ": 升序排列",
            "sSortDescending": ": 降序排列"
        },
        "oPaginate": {
            "sFirst": "首页",
            "sLast": "末页",
            "sNext": "下页",
            "sPrevious": "上页"
        },
        "sEmptyTable": "没有相关记录",
        "sInfo": "第 _START_ 到 _END_ 条记录，共 _TOTAL_ 条",
        "sInfoEmpty": "第 0 到 0 条记录，共 0 条",
        "sInfoFiltered": "(从 _MAX_ 条记录中检索)",
        "sInfoPostFix": "",
        "sDecimal": "",
        "sThousands": ",",
        "sLengthMenu": "每页显示条数: _MENU_",
        "sLoadingRecords": "正在载入...",
        "sProcessing": "正在载入...",
        "sSearch": "搜索:",
        "sSearchPlaceholder": "",
        "sUrl": "",
        "sZeroRecords": "没有相关记录"
    }
    $.fn.dataTable.defaults.oLanguage=oLanguage;
    // 默认禁用搜索和排序
    $.extend( $.fn.dataTable.defaults, {
        deferRender: true,// 当处理大数据时，延迟渲染数据，有效提高Datatables处理能力
        bStateSave: true,//表格状态保持
        searching: false,//搜索框
        bPaginate: true, // 翻页功能
        bLengthChange: false, // 改变每页显示数据数量
        bFilter: false, // 过滤功能
        bInfo: true,// 页脚信息
        bAutoWidth: false,// 是否自动计算表格各列宽度
        iDisplayLength: 10,
        bProcessing: false,//加载动画
        bSort: false
    } );

    function getCookie(name){
        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
        if(arr != null) return unescape(arr[2]); return null;
    }

    function closeLayer(obj) {
        layer.close(obj);
    }
</script>
</body>
</html>