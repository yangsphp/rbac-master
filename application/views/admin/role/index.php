<section class="content-header">
    <h1>
        角色管理
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/index') ?>"><i class="fa fa-dashboard"></i> 控制台</a></li>
        <li class="">权限控制</li>
        <li class="active">角色管理</li>
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
                        添加角色
                    </button>
                    <?php }?>
                    <div class="input-group" style="display: inline-flex;float:right;">
                        <input type="text" id="keyword" name="table_search" class="form-control input-sm pull-right"
                               style="width: 150px;" placeholder="Search">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-primary " onclick="search()"><i class="fa fa-search"></i>
                            </button>
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
                    <table id="roleTable" class="table table-hover radius" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>人数</th>
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
                    url: siteUrl + '/role/get',
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
                data: "number",
            }, {
                data: "date_entered",
            }, {
                data: function (mdata) {
                    var html = '', disabled = "";
                    if (mdata.id == 1) {
                        disabled = "disabled='true'"
                    }
                    <?php if($edit_flag){?>
                    html += ' <button type="button" ' + disabled + ' class="btn btn-info btn-xs my-btn btn-flat" onclick="showModal(' + mdata.id + ')">修改</button>';
                    <?php }if ($delete_flag){?>
                    html += ' <button type="button" ' + disabled + ' class="btn btn-danger btn-xs my-btn btn-flat" onclick="del(' + mdata.id + ')">删除</button>';
                    <?php }?>
                    return html;
                },
                orderable: false
            }],
            fnInitComplete: function (oSettings, json) {
                hideLoading();
                // 全选、反选
            },
            drawCallback: function () {
                hideLoading();
            },
            columnDefs: [{
                "orderable": false,
                "targets": 0
            }],
        };
        initTable = $("#roleTable").dataTable($.dataTablesSettings);

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

    function showModal(id = 0) {
        showLoading();
        $.get(siteUrl + "/role/add?id=" + id, function (data) {
            hideLoading();
            var add_role = layer.open({
                type: 1,
                title: data.title,
                area: '600px',
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
                closeLayer(add_role);
            });
            $("#submit-form").on("click", function () {
                doSubmit(id, add_role)
            });
        }, 'json');
    }

    function doSubmit(id, add_role) {
        var obj = $("#form"), action = 'add_op';
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        if (id > 0) {
            action = 'edit_op';
        }
        $.post(siteUrl + "/role/" + action, obj.serialize(), function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(add_role);
                initTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function del(id) {
        var delete_role = layer.open({
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
                    closeLayer(delete_role);
                });
                $("#submit-form").on("click", function () {
                    doDelete(id, delete_role)
                });
            }
        });
    }

    function doDelete(id, delete_role) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/role/delete_op?id=" + id, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(delete_role);
                initTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }
</script>