<section class="content-header">
    <h1>
        权限管理
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/index') ?>"><i class="fa fa-dashboard"></i> 控制台</a></li>
        <li class="">权限控制</li>
        <li class="active">权限管理</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <button class="btn btn-primary btn-sm btn-flat" onclick="showModal()">
                        <i class="fa fa-plus"></i>
                        添加权限
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table style="" id="authTable" class="table table-hover radius" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>菜单名称</th>
                            <th>图标</th>
                            <th>路径</th>
                            <th>排序</th>
                            <th>类型</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th width="120">操作</th>
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
            bPaginate: false, // 翻页功能
            bProcessing: false,
            serverSide: true, // 启用服务器端分页
            ajax: function (data, callback, settings) {
                showLoading("正在加载...");
                // 封装请求参数
                var param = {};
                $.ajax({
                    type: 'post',
                    url: siteUrl + '/auth/get',
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
                data: "name",
            }, {
                data: "icon",
            }, {
                data: "url",
            }, {
                data: "sort",
            }, {
                data: function (mdata) {
                    let menu_name = '<span class="label label-default btn-flat">按钮</span>';
                    if (mdata.is_menu == 1) {
                        menu_name = '<span class="label label-danger">菜单</span>';
                    }
                    return menu_name;
                },
            }, {
                data: function (mdata) {
                    if (mdata.status == 0) {
                        return '<small class="badge bg-yellow" onclick="manageMenu('+mdata.id+', 1)" style="cursor: pointer;">已停用</small>';
                    } else {
                        return '<small class="badge bg-green" onclick="manageMenu('+mdata.id+', 0)" style="cursor: pointer;">已启用</small>';
                    }
                },
            }, {
                data: "date_entered",
            }, {
                data: function (mdata) {
                    var html = ''
                    html += ' <button type="button" class="btn btn-info btn-xs my-btn btn-flat" onclick="showModal(' + mdata.id + ')">修改</button>';
                    html += ' <button type="button" class="btn btn-danger btn-xs my-btn btn-flat" onclick="del(' + mdata.id + ')">删除</button>';
                    return html;
                },
                orderable: false
            }],
            fnInitComplete: function (oSettings, json) {
                hideLoading();
            },
            drawCallback: function () {
                hideLoading();
            },
            columnDefs: [{
                "orderable": false,
                "targets": 0
            }],
        };
        initTable = $("#authTable").dataTable($.dataTablesSettings);

    });

    function manageMenu(id, flag) {
        loading();
        $.get(siteUrl+"/auth/setMenuStatus?id="+id+"&status="+flag, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                initTable.api().draw(false);
            } else{
                layer.msg(res.msg, {icon: 2});
            }
        }, "json")
    }

    function showModal(id=0) {
        showLoading();
        $.get(siteUrl + "/auth/add?id="+id, function (data) {
            hideLoading();
            var add_auth = layer.open({
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
                closeLayer(add_auth);
            });
            $("#submit-form").on("click", function () {
                doSubmit(id, add_auth)
            });
        }, 'json');
    }
    function doSubmit(id, add_auth) {
        var obj = $("#form"), action = 'add_op';
        loadT = layer.msg('正在提交数据...', { time: 0, icon: 16, shade: [0.3, '#000'] });
        if (id > 0) {
            action = 'edit_op';
        }
        $.post(siteUrl+"/auth/"+action, obj.serialize(), function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(add_auth);
                initTable.api().draw(false);
            }else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }
    function del(id) {
        var delete_auth = layer.open({
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
                    closeLayer(delete_auth);
                });
                $("#submit-form").on("click", function () {
                    doDelete(id, delete_auth)
                });
            }
        });
    }
    function doDelete(id, delete_auth) {
        loadT = layer.msg('正在提交数据...', { time: 0, icon: 16, shade: [0.3, '#000'] });
        $.get(siteUrl+"/auth/delete_op?id="+id, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(delete_auth);
                initTable.api().draw(false);
            } else{
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }
</script>