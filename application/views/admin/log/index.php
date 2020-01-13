<script src="<?php echo base_url() ?>/static/plugins/My97DatePicker/WdatePicker.js"></script>
<section class="content-header">
    <h1>
        日志管理
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('log/index') ?>"><i class="fa fa-dashboard"></i> 控制台</a></li>
        <li class="">系统管理</li>
        <li class="active">后台日志</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <?php if ($clear_flag){?>
                    <button class="btn btn-danger btn-sm btn-flat" onclick="del()">
                        <i class="fa fa-trash-o"></i>
                        清理日志
                    </button>
                    <?php }?>
                    <div class="input-group" style="display: inline-flex;float:right;">
                        <input type="text" id="keyword" name="table_search" class="form-control input-sm pull-right"
                               style="width: 150px;" placeholder="名称/管理员/IP">
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-primary" onclick="search()"><i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group" style="display: inline-flex;float:right;margin-right: 10px;">
                        <input style="width: 100px;" type="text" name="start_date" value=""
                               onFocus="WdatePicker({dateFmt : 'yyyy-MM-dd',lang:'zh-cn'})"
                               id="start_date" class="form-control input-sm pull-right" autocomplete="off" placeholder="开始日期"/>
                        <div style="line-height: 30px;">~</div>
                        <input style="width: 100px;" width="100" type="text" name="end_date" value=""
                               onFocus="WdatePicker({dateFmt : 'yyyy-MM-dd',lang:'zh-cn'})" id="end_date"
                               class="form-control input-sm pull-right" autocomplete="off" placeholder="结束日期"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-body">
                    <table style="" id="logTable" class="table table-hover radius" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>IP</th>
                            <th>操作名称</th>
                            <th>管理员</th>
                            <th>ID</th>
                            <th width="150">创建时间</th>
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
                param.start_date = $("#start_date").val();
                param.end_date = $("#end_date").val();

                $.ajax({
                    type: 'post',
                    url: siteUrl + '/log/get',
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
            columns: [ {
                data: "ip",
            }, {
                data: "name",
            }, {
                data: "username",
            }, {
                data: "_id",
            },{
                data: "date_entered",
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
        initTable = $("#logTable").dataTable($.dataTablesSettings);

        $(document).on('keyup', '#keyword, #start_date, #end_date', function (event) {
            if (event.keyCode == "13") {
                // 回车执行查询
                initTable.api().ajax.reload();
            }
        });
    });

    function search() {
        initTable.api().ajax.reload();
    }

    function del() {
        var delete_log = layer.open({
            type: 1,
            title: "信息",
            area: '300px',
            closeBtn: 2,
            shadeClose: false,
            shade: false,
            offset: 'auto',
            shade: [0.3, '#000'],
            content: `<form class="bt-form pd20 pb70" id="form"><div class="line">为了系统安全，系统仅删除30天之前的日志</div><div class="bt-form-submit-btn"><button type="button" class="btn btn-sm btn-my" id="close-modal">关闭</button><button type="button" class="btn btn-sm btn-success" id="submit-form">提交</button></div> </form>`,
            cancel: function () {

            },
            success() {
                $("#close-modal").on("click", function () {
                    closeLayer(delete_log);
                });
                $("#submit-form").on("click", function () {
                    doDelete(delete_log)
                });
            }
        });
    }

    function doDelete(delete_log) {
        loadT = layer.msg('正在提交数据...', {time: 0, icon: 16, shade: [0.3, '#000']});
        $.get(siteUrl + "/log/delete_op", function (res) {
            if (res.code == 0) {
                layer.msg(res.msg, {icon: 1});
                closeLayer(delete_log);
                initTable.api().draw(false);
            } else {
                layer.msg(res.msg, {icon: 2});
            }
        }, "json");
    }
</script>