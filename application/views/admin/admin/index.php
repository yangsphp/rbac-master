<section class="content-header">
    <h1>
        管理员管理
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/index')?>"><i class="fa fa-dashboard"></i> 控制台</a></li>
        <li class="">权限控制</li>
        <li class="active">管理员管理</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <?php if ($add_flag){?>
                    <button class="btn btn-primary btn-sm btn-flat" onclick="showModal()">
                        <i class="fa fa-plus"></i>
                        添加管理员
                    </button>
                    <?php }?>
                    <div class="input-group" style="display: inline-flex;float:right;">
                            <input type="text" id="keyword" name="table_search" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search">
                            <div class="input-group-btn">
                                <button class="btn btn-sm btn-primary " onclick="search()"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table style="" id="adminTable" class="table table-hover radius" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>角色</th>
                            <th>管理员名称</th>
                            <th>登录IP</th>
                            <th>最后一次登录时间</th>
                            <th>创建时间</th>
                            <th width="150">操作</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    var initTable = null;
    $(document).ready(function () {
        $.dataTablesSettings = {
            bPaginate: true, // 翻页功能
            bProcessing: false,
            serverSide: true, // 启用服务器端分页
            ajax: function (data, callback, settings) {
                showLoading("正在加载...");
                // 封装请求参数
                var param = {};
                param.limit = data.length;// 页面显示记录条数，在页面显示每页显示多少项的时候
                param.start = data.start;// 开始的记录序号
                param.page = (data.start / data.length) + 1;// 当前页码

                //搜索字段。。。
                param.keyword = $("#keyword").val();

                $.ajax({
                    type: 'post',
                    url: siteUrl + '/admin/get',
                    data: param,
                    dataType: 'json',
                    success: function (res) {
                        var returnData = {};
                        returnData.draw = parseInt(data.draw);// 这里直接自行返回了draw计数器,应该由后台返回
                        returnData.recordsTotal = res.total;
                        returnData.recordsFiltered = res.total;// 后台不实现过滤功能，每次查询均视作全部结果
                        returnData.data = res.data;

                        callback(returnData);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        hideLoading();
                        alert("获取失败");
                    }
                });
            },
            columns: [{
                data: "id",
            }, {
                data: "role_name",
            }, {
                data: "username",
            }, {
                data: "login_ip",
            }, {
                data: "last_login_entered",
            },{
                data: "date_entered",
            },{
                data: function (mdata) {
                    var html = '', disabled = "";
                    if (mdata.id == 1){
                        disabled = "disabled='true'"
                    }
                    <?php if($edit_flag){?>
                    html += ' <button type="button" '+disabled+' class="btn btn-info btn-xs my-btn btn-flat" onclick="showModal(' + mdata.id + ')">修改</button>';
                    <?php }if ($delete_flag){?>
                    html += ' <button type="button" '+disabled+' class="btn btn-danger btn-xs my-btn btn-flat" onclick="del(' + mdata.id + ')">删除</button>';
                    <?php }?>

                    return html;
                },
                orderable: false
            }],
            fnInitComplete: function (oSettings, json) {
                hideLoading();
                // 全选、反选
                //checkedOrNo('checkbox0', 'select_checkbox');
            },
            drawCallback: function () {
                hideLoading();
            },
            columnDefs: [{
                "orderable": false,
                "targets": 0
            }],
        };
        initTable = $("#adminTable").dataTable($.dataTablesSettings);

        $('#keyword').on('keyup', function (event) {
            if (event.keyCode == "13") {
                // 回车执行查询
                initTable.api().ajax.reload();
            }
        });
    });

    function search() {
        initTable.api().ajax.reload();
    }

    function showModal(id=0) {
        showLoading();
        $.get(siteUrl + "/admin/add?id="+id, function (data) {
            hideLoading();
            var add_admin = layer.open({
                type: 1,
                title: data.title,
                area: '530px',
                closeBtn: 2,
                shadeClose: false,
                shade: false,
                offset: 'auto',
                shade: [0.3, '#000'],
                content: data.html,
                cancel: function () {

                }
            });
            $("#close-modal").on("click", function () {
                closeLayer(add_admin);
            });
            $("#submit-form").on("click", function () {
                doSubmit(id, add_admin)
            });
        }, 'json');
    }
    function doSubmit(id, add_admin) {
        var obj = $("#form"), action = 'add_op';
        loadT = layer.msg('正在提交数据...', { time: 0, icon: 16, shade: [0.3, '#000'] });
        if (id > 0) {
            action = 'edit_op';
        }
        $.post(siteUrl+"/admin/"+action, obj.serialize(), function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(add_admin);
                initTable.api().draw(false);
            }else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function del(id) {
        var delete_admin = layer.open({
            type: 1,
            title: "信息",
            area: '300px',
            closeBtn: 2,
            shadeClose: false,
            shade: false,
            offset: 'auto',
            shade: [0.3, '#000'],
            content: `<form class="bt-form pd20 pb70" id="form"><div class="line">您确定要删除吗？</div><div class="bt-form-submit-btn"><button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button><button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button></div> </form>`,
            cancel: function () {

            },
            success() {
                $("#close-modal").on("click", function () {
                    closeLayer(delete_admin);
                });
                $("#submit-form").on("click", function () {
                    doDelete(id, delete_admin)
                });
            }
        });
    }
    function doDelete(id, delete_admin) {
        loadT = layer.msg('正在提交数据...', { time: 0, icon: 16, shade: [0.3, '#000'] });
        $.get(siteUrl+"/admin/delete_op?id="+id, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(delete_admin);
                initTable.api().draw(false);
            } else{
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }
</script>