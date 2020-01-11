<style>
    tr {
        vertical-align: middle !important;
    }
</style>
<section class="content-header">
    <h1>
        数据管理
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/index') ?>"><i class="fa fa-dashboard"></i> 控制台</a></li>
        <li class="">系统管理</li>
        <li class="active">数据管理</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table style="" id="databaseTable" class="table table-hover radius" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th width="20">
                                <div class="checkbox" onclick="checkboxAll(this)">
                                    <input id="checkbox0" type="checkbox">
                                    <label for="checkbox0"></label>
                                </div>
                            </th>
                            <th>表名</th>
                            <th>表注释</th>
                            <th colspan="2">表大小（M）</th>
                            <th>索引</th>
                            <th>碎片</th>
                            <th>记录数</th>
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

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <button class="btn btn-danger btn-sm btn-flat" style="margin-right: 10px;"
                            onclick="backUpDatabase(1)">
                        <i class="fa fa-plus"></i>
                        备份全部表
                    </button>
                    <button class="btn btn-primary btn-sm btn-flat" onclick="backUpDatabase(0)">
                        <i class="fa fa-plus"></i>
                        备份选中表
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table style="" id="backTable" class="table table-hover radius" cellspacing="0"
                           width="100%">
                        <thead>
                        <tr>
                            <th>SQL文件</th>
                            <th>文件大小</th>
                            <th>备份时间</th>
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
    let initDatabaseTable = null, total_size = 0, initBackTable;
    $(document).ready(function () {
        $.dataTablesSettings = {
            bPaginate: false, // 翻页功能
            bProcessing: false,
            serverSide: true, // 启用服务器端分页
            iDisplayLength: 100,
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
                    url: siteUrl + '/database/get?flag=table',
                    data: param,
                    dataType: 'json',
                    success: function (res) {
                        var returnData = {};
                        returnData.draw = parseInt(data.draw);// 这里直接自行返回了draw计数器,应该由后台返回
                        returnData.recordsTotal = res.total;
                        returnData.recordsFiltered = res.total;// 后台不实现过滤功能，每次查询均视作全部结果
                        returnData.data = res.data;
                        total_size = res.total_size;
                        callback(returnData);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        hideLoading();
                        alert("获取失败");
                    }
                });
            },
            columns: [{
                data: function (mdata) {
                    let html = '';
                    html += '<div class="checkbox"><input name="checks[]" value="' + mdata.name + '" class="select_checkbox" id="checkbox_' + mdata.name + '" type="checkbox"><label for="checkbox_' + mdata.name + '"></label></div>';
                    return html;
                }
            }, {
                data: "name",
            }, {
                data: "note",
            }, {
                data: function (mdata) {
                    let per = Math.round(100 * mdata.tsize / total_size);
                    return '<div class="progress xs"><div class="progress-bar progress-bar-red" style="width: ' + per + '%;"></div></div>';
                },
            }, {
                data: "tsize",
            }, {
                data: "index",
            }, {
                data: "chip",
            }, {
                data: "rows",
            }, {
                data: function (mdata) {
                    let html = '';
                    html += ' <button type="button" class="btn btn-info btn-xs my-btn btn-flat" onclick="repair(\'' + mdata.name + '\')">修复</button>';
                    html += ' <button type="button" class="btn btn-success btn-xs my-btn btn-flat" onclick="optimize(\'' + mdata.name + '\')">优化</button>';
                    html += ' <button type="button" class="btn btn-danger btn-xs my-btn btn-flat" onclick="dict(\'' + mdata.name + '\')">字典</button>';

                    return html;
                },
                orderable: false
            }],
            fnInitComplete: function (oSettings, json) {
                hideLoading();
                // 全选、反选
                checkboxAll('checkbox0');
            },
            drawCallback: function () {
                hideLoading();
                $("input").iCheck({
                    labelHover: false,
                    cursor: true,
                    checkboxClass: 'icheckbox_minimal-blue'
                });
            },
            columnDefs: [{
                "orderable": false,
                "targets": 0
            }],
        };
        initDatabaseTable = $("#databaseTable").dataTable($.dataTablesSettings);

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
                    url: siteUrl + '/database/get?flag=back_list',
                    data: param,
                    dataType: 'json',
                    success: function (res) {
                        let returnData = {};
                        returnData.draw = parseInt(data.draw);// 这里直接自行返回了draw计数器,应该由后台返回
                        returnData.recordsTotal = res.total;
                        returnData.recordsFiltered = res.total;// 后台不实现过滤功能，每次查询均视作全部结果
                        returnData.data = res.data;
                        total_size = res.total_size;
                        callback(returnData);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        hideLoading();
                        alert("获取失败");
                    }
                });
            },
            columns: [{
                data: function (mdata) {
                    return "<a href='#' onclick='showSql("+mdata.id+")'>"+mdata.name+"</a>";
                },
            }, {
                data: "size",
            }, {
                data: "date_entered",
            }, {
                data: function (mdata) {
                    let html = '';
                    html += ' <button type="button" class="btn btn-info btn-xs my-btn btn-flat" onclick="callBack(\'' + mdata.id + '\')">还原</button>';
                    html += ' <button type="button" class="btn btn-success btn-xs my-btn btn-flat" onclick="downLoad(\''+mdata.name+'\',\'' + baseUrl + mdata.path + '\')">下载</button>';
                    html += ' <button type="button" class="btn btn-danger btn-xs my-btn btn-flat" onclick="delBack(\'' + mdata.id + '\')">删除</button>';

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
        initBackTable = $("#backTable").dataTable($.dataTablesSettings);


        $('#keyword').on('keyup', function (event) {
            if (event.keyCode == "13") {
                // 回车执行查询
                initDatabaseTable.api().ajax.reload();
            }
        });
    });

    function search() {
        initDatabaseTable.api().ajax.reload();
    }

    function backUpDatabase(flag) {
        let tables = "";
        let title = "您确定要备份选定的表吗？";
        if (flag === 1) {
            title = "您确定要备份所有的表吗？";
        } else {
            $(".select_checkbox").each(function () {
                if ($(this).is(':checked')) {
                    tables += $(this).val() + ',';
                }
            });
            tables = tables.slice(0, tables.length - 1);
            if (tables.length === 0) {
                layer.msg("请选择要备份的表", {icon: 2});
                return false;
            }
        }
        let back_up = layer.open({
            type: 1,
            title: "信息",
            area: '300px',
            closeBtn: 2,
            shadeClose: false,
            shade: false,
            offset: 'auto',
            shade: [0.3, '#000'],
            content: `<form class="bt-form pd20 pb70" id="form"><div class="line">` + title + `</div><div class="bt-form-submit-btn"><button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button><button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button></div> </form>`,
            cancel: function () {

            },
            success() {
                $("#close-modal").on("click", function () {
                    closeLayer(back_up);
                });
                $("#submit-form").on("click", function () {
                    doBackUp(tables, back_up)
                });
            }
        });
    }

    function doBackUp(tables, back_up) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/database/backup?tables=" + tables, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(back_up);
                initDatabaseTable.api().draw(false);
                initBackTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function repair(table) {
        var repair_database = layer.open({
            type: 1,
            title: "信息",
            area: '300px',
            closeBtn: 2,
            shadeClose: false,
            shade: false,
            offset: 'auto',
            shade: [0.3, '#000'],
            content: `<form class="bt-form pd20 pb70" id="form"><div class="line">您确定要修复吗？该操作可能会影响其他用户正在操作的数据</div><div class="bt-form-submit-btn"><button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button><button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button></div> </form>`,
            cancel: function () {

            },
            success() {
                $("#close-modal").on("click", function () {
                    closeLayer(repair_database);
                });
                $("#submit-form").on("click", function () {
                    doRepair(table, repair_database)
                });
            }
        });
    }

    function doRepair(table, repair_database) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/database/repair?table=" + table, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(repair_database);
                initDatabaseTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function optimize(table) {
        var optimize_database = layer.open({
            type: 1,
            title: "信息",
            area: '300px',
            closeBtn: 2,
            shadeClose: false,
            shade: false,
            offset: 'auto',
            shade: [0.3, '#000'],
            content: `<form class="bt-form pd20 pb70" id="form"><div class="line">您确定要优化吗？该操作可能会影响其他用户正在操作的数据</div><div class="bt-form-submit-btn"><button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button><button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button></div> </form>`,
            cancel: function () {

            },
            success() {
                $("#close-modal").on("click", function () {
                    closeLayer(optimize_database);
                });
                $("#submit-form").on("click", function () {
                    doOptimize(table, optimize_database)
                });
            }
        });
    }

    function doOptimize(table, optimize_database) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/database/optimize?table=" + table, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(optimize_database);
                initDatabaseTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function dict(table) {
        showLoading();
        $.get(siteUrl + "/database/dict?table=" + table, function (data) {
            hideLoading();
            let dict = layer.open({
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
                closeLayer(dict);
            });
            $("#submit-form").on("click", function () {
                doSubmit(dict)
            });
        }, 'json');
    }

    function showSql(id) {
        showLoading();
        $.get(siteUrl + "/database/sql?id=" + id, function (data) {
            hideLoading();
            let sql = layer.open({
                type: 1,
                title: data.title,
                area: '800px',
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
                closeLayer(sql);
            });
            $("#submit-form").on("click", function () {
                doSubmit(dict)
            });
        }, 'json');
    }

    function doSubmit(edit_column) {
        var obj = $("#form");
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.post(siteUrl + "/database/edit_op", obj.serialize(), function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(edit_column);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function callBack(id) {
        let callBack = layer.open({
            type: 1,
            title: "信息",
            area: '300px',
            closeBtn: 2,
            shadeClose: false,
            shade: false,
            offset: 'auto',
            shade: [0.3, '#000'],
            content: `<form class="bt-form pd20 pb70" id="form"><div class="line">您确定要还原数据吗？</div><div class="bt-form-submit-btn"><button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button><button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button></div> </form>`,
            cancel: function () {

            },
            success() {
                $("#close-modal").on("click", function () {
                    closeLayer(callBack);
                });
                $("#submit-form").on("click", function () {
                    doCallBack(id, callBack)
                });
            }
        });
    }

    function doCallBack(id, callBack) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/database/callback?id=" + id, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(callBack);
                initBackTable.api().draw(false);
                initDatabaseTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function delBack(id) {
        let delete_back = layer.open({
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
                    closeLayer(delete_back);
                });
                $("#submit-form").on("click", function () {
                    doDelete(id, delete_back)
                });
            }
        });
    }

    function doDelete(id, delete_back) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/database/delete_op?id=" + id, function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(delete_back);
                initBackTable.api().draw(false);
                initDatabaseTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }

    function downLoad(name, path) {
        let tempa = document.createElement('a');
        tempa.download = name;
        tempa.href = path;
        document.body.appendChild(tempa);
        tempa.click();
        tempa.remove()
    }
</script>