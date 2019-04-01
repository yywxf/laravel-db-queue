<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">

    <link rel="shortcut icon" href="{{ asset('img/dbqueue.ico') }}" />
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.css') }}">
    {{--    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}">--}}
    <script defer src="{{ asset('fontawesome/js/all.min.js') }}"></script>

    <title>队列管理</title>
    <style>
        .mytable th, .table td {
            vertical-align: middle;
        }

        .mytable th {
            text-align: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-yin-yang fa-spin fa-2x"></i></a>
        <strong class="mr-auto">Laravel 队列管理</strong>
    </div>
</nav>
<div class="container mt-5">
    <div class="row">
        <div class="col-2">
            <ul class="nav nav-pills flex-column position-fixed">
                <li class="nav-item">
                    <a class="nav-link" href="#statistics" id="btn_statistics"><i class="fas fa-tachometer-alt"></i> 总览</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#failed" id="btn_failed"><i class="far fa-times-circle"></i> 失败的任务</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#working" id="btn_working"><i class="fas fa-tasks"></i> 积压的任务</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#manage" id="btn_manage"><i class="fas fa-cog"></i> 队列管理</a>
                </li>
            </ul>
        </div>
        <div class="col-10">
            <div class="card">
                <div class="card-header">总览</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">任务总数</h5>
                                    <h3 class="card-text" id="job_total">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h5 class="card-title">失败的任务</h5>
                                    <h3 class="card-text" id="job_failed">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title">积压的任务</h5>
                                    <h3 class="card-text" id="job_work">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h5 class="card-title">等待的任务</h5>
                                    <h3 class="card-text" id="job_wait">-</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" id="card_header"></div>
                <div class="card-body">
                    <table class="table mytable table-hover" id="dataTable">
                        <thead>
                        <tr></tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div id="paginationBox" class="col">

                </div>
            </div>

        </div>
    </div>

</div>

{{--failedDetail--}}
<div id="failedDetail" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="failedDetailTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="modalTable" class="table" style="table-layout: fixed;">

                </table>
            </div>
        </div>
    </div>
</div>

{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>--}}
<script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('layer/layer.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
{{--<script src="{{ asset('layui/layui.js') }}"></script>--}}
<script>
    $(function () {
        var layerIndex;

        function getStatistics() {
            $.ajax({
                url: '/api/queue/statistics',
                type: 'get',
                success: function (data) {
                    $('#job_work').text(data.work_count);
                    $('#job_wait').text(data.wait_count);
                    $('#job_failed').text(data.failed_count);
                    $('#job_total').text(data.total);

                    $('#paginationBox').hide();
                },
                'beforeSend': function () {
                    layerIndex = layer.load(0, {
                        shade: [0.1, '#fff'] //0.1透明度的白色背景
                    });
                },
                'error': function () {
                    layer.msg('请求出错！');
                },
                'complete': function () {
                    layer.close(layerIndex);
                },
            });
        }

        getStatistics();

        // 分页
        $('#paginationBox').on('click', '.pagination li a', function () {
            getFailed($(this).attr('data-page'));
        }).on('change', '#selectPerPage', function () {
            getFailed();
        });

        // 队列积压总览
        $('#btn_statistics').click(function () {
            $(this).addClass('active').parent().siblings().find('a').removeClass('active');
            getStatistics();
            $.ajax({
                url: '/api/queue/statistics2',
                type: 'get',
                success: function (data) {
                    $('#card_header').html('队列积压总览');
                    var headHtml = '<th scope="col">#</th>\n' +
                        '<th scope="col">队列</th>\n' +
                        '<th scope="col">数量</th>';
                    $("#dataTable thead tr").html(headHtml);
                    var len = data.length;
                    var strHtml = '';
                    var bodyEle = $("#dataTable tbody");
                    bodyEle.html("");
                    if (len === 0) {
                        layer.msg('无数据');
                    } else {
                        var i = 0;
                        $.each(data, function (k, v) {
                            i++;
                            strHtml = '<tr>' +
                                '<th scope="row">' + i + '</th>' +
                                '<td>' + k + '</td>' +
                                '<td class="text-center">' + v + '</td>' +
                                '</tr>';
                            bodyEle.append(strHtml);
                        });
                    }
                },
                'beforeSend': function () {
                    layerIndex = layer.load(0, {
                        shade: [0.1, '#fff'] //0.1透明度的白色背景
                    });
                },
                'error': function () {
                    layer.msg('请求出错！');
                },
                'complete': function () {
                    layer.close(layerIndex);
                },
            });
        });

        function getFailed(page = 1) {
            $.ajax({
                url: '/api/queue/failed',
                type: 'get',
                data: {
                    page: page,
                    perPage: $('#selectPerPage').val(),
                },
                success: function (data) {
                    $('#card_header').html('失败的任务<button type="button" title="全部重试" class="float-right btn btn-sm btn-light text-success" onclick="doJob(this,\'retry\',\'all\')"><i class="fas fa-sync-alt"></i></button>');
                    var headHtml = '<th scope="col">#</th>\n' +
                        '<th scope="col">队列</th>\n' +
                        '<th scope="col">异常</th>\n' +
                        '<th scope="col">失败时间</th>\n' +
                        '<th scope="col">操作</th>';
                    $("#dataTable thead tr").html(headHtml);
                    var realdata = data.data.data;
                    var len = realdata.length;
                    var strHtml = '';
                    var bodyEle = $("#dataTable tbody");
                    bodyEle.html("");
                    if (len === 0) {
                        layer.msg('无数据');
                    } else {
                        for (var i = 0; i < len; i++) {
                            strHtml = '<tr>' +
                                '<th scope="row">' + (i + 1) + '</th>' +
                                '<td><a href="#failedDetail" onclick="getJob(\'' + realdata[i].id + '\')">' + realdata[i].queue + '</a></td>' +
                                // '<td>' + data.data[i].payload + '</td>' +
                                '<td><div class="text-truncate" style="max-width: 450px;">' + realdata[i].exception + '</div></td>' +
                                '<td>' + realdata[i].failed_at + '</td>' +
                                '<td style="width: 92px;"><button type="button" class="btn btn-sm btn-light text-success" onclick="doJob(this,\'retry\',\'' + realdata[i].id + '\')"><i class="fas fa-sync-alt"></i></button> ' +
                                '<button type="button" class="btn btn-sm btn-light text-danger" onclick="doJob(this,\'forget\',\'' + realdata[i].id + '\')"><i class="fas fa-trash-alt"></i></button></td>' +
                                '</tr>';
                            bodyEle.append(strHtml);
                        }
                    }
                    $('#paginationBox').html(data.links).show();
                },
                'beforeSend': function () {
                    layerIndex = layer.load(0, {
                        shade: [0.1, '#fff'] //0.1透明度的白色背景
                    });
                },
                'error': function () {
                    layer.msg('请求出错！');
                },
                'complete': function () {
                    layer.close(layerIndex);
                },
            });
        }

        // 失败的任务
        $('#btn_failed').click(function () {
            $(this).addClass('active').parent().siblings().find('a').removeClass('active');
            getStatistics();
            getFailed();
        });

        // 积压的任务
        $('#btn_working').click(function () {
            $(this).addClass('active').parent().siblings().find('a').removeClass('active');
            getStatistics();
            $.ajax({
                url: '/api/queue/working',
                type: 'get',
                success: function (data) {
                    $('#card_header').html('积压的任务');
                    var headHtml = '<th scope="col">#</th>\n' +
                        '<th scope="col">队列</th>\n' +
                        '<th scope="col">reserved_at</th>\n' +
                        '<th scope="col">available_at</th>';
                    $("#dataTable thead tr").html(headHtml);
                    var len = data.data.length;
                    var strHtml = '';
                    var bodyEle = $("#dataTable tbody");
                    bodyEle.html("");
                    if (len === 0) {
                        layer.msg('无数据');
                    } else {
                        for (var i = 0; i < len; i++) {
                            strHtml = '<tr>' +
                                '<th scope="row">' + (i + 1) + '</th>' +
                                '<td>' + data.data[i].queue + '</td>' +
                                // '<td><div class="text-truncate" style="max-width: 500px;">' + data.data[i].payload + '</div></td>' +
                                '<td>' + data.data[i].reserved_at + '</td>' +
                                '<td>' + data.data[i].available_at + '</td>' +
                                '</tr>';
                            bodyEle.append(strHtml);
                        }
                    }
                },
                'beforeSend': function () {
                    layerIndex = layer.load(0, {
                        shade: [0.1, '#fff'] //0.1透明度的白色背景
                    });
                },
                'error': function () {
                    layer.msg('请求出错！');
                },
                'complete': function () {
                    layer.close(layerIndex);
                },
            });
        });

        // 队列管理
        $('#btn_manage').click(function () {
            $(this).addClass('active').parent().siblings().find('a').removeClass('active');
            getStatistics();
            $.ajax({
                url: '/api/queue/status',
                type: 'get',
                success: function (data) {
                    $('#card_header').html('队列管理');
                    var headHtml = '<th scope="col">#</th>\n' +
                        '<th scope="col">队列</th>\n' +
                        '<th scope="col">状态</th>\n' +
                        '<th scope="col">时间</th>\n' +
                        '<th scope="col">操作</th>';
                    $("#dataTable thead tr").html(headHtml);
                    var len = data.length;
                    var strHtml = '';
                    var bodyEle = $("#dataTable tbody");
                    bodyEle.html("");
                    if (len === 0) {
                        layer.msg('无数据');
                    } else {
                        for (var i = 0; i < len; i++) {
                            if (data[i].status === 'RUNNING') {
                                disabledStart = ' disabled ';
                                disabledStop = '';
                            } else if (data[i].status === 'STOPPED') {
                                disabledStart = '';
                                disabledStop = ' disabled ';
                            }
                            strHtml = '<tr>' +
                                '<th scope="row">' + (i + 1) + '</th>' +
                                '<td>' + data[i].queue + '</td>' +
                                // '<td><div class="text-truncate" style="max-width: 500px;">' + data.data[i].payload + '</div></td>' +
                                '<td>' + data[i].status + '</td>' +
                                '<td>' + data[i].time + '</td>' +
                                '<td><button type="button" class="btn btn-sm btn-light text-success"' + disabledStart + ' onclick="doQueue(\'start\',\'' + data[i].queue + '\')"><i class="fas fa-play fa-sm"></i></button> ' +
                                '<button type="button" class="btn btn-sm btn-light text-danger"' + disabledStop + ' onclick="doQueue(\'stop\',\'' + data[i].queue + '\')"><i class="fas fa-stop"></i></button></td>' +
                                '</tr>';
                            bodyEle.append(strHtml);
                        }
                    }
                },
                'beforeSend': function () {
                    layerIndex = layer.load(0, {
                        shade: [0.1, '#fff'] //0.1透明度的白色背景
                    });
                },
                'error': function () {
                    layer.msg('请求出错！');
                },
                'complete': function () {
                    layer.close(layerIndex);
                },
            });
        });

    });

    // 获取失败任务详情
    function getJob(id) {
        $.ajax({
            url: '/api/queue/failed',
            type: 'get',
            data: {id: id},
            success: function (data) {
                $('#failedDetailTitle').html(data.queue);
                var bodyEle = $("#modalTable");
                strHtml = '<tr>\n' +
                    '    <th style="width: 100px;">ID</th>\n' +
                    '    <td>' + data.id + '</td>\n' +
                    '</tr>\n' +
                    '<tr>\n' +
                    '    <th>Queue</th>\n' +
                    '    <td>' + data.queue + '</td>\n' +
                    '</tr>\n' +
                    '<tr>\n' +
                    '    <th>Failed At</th>\n' +
                    '    <td>' + data.failed_at + '</td>\n' +
                    '</tr>\n' +
                    '<tr>\n' +
                    '    <td colspan="2">\n' +
                    '        <b>Exception</b>\n' +
                    '        <pre>' + data.exception + '</pre>\n' +
                    '    </td>\n' +
                    '</tr>\n' +
                    '<tr>\n' +
                    '    <th>Data</th>\n' +
                    '    <td><pre>' + JSON.stringify(JSON.parse(data.payload), null, 2) + '</pre></td>\n' +
                    '</tr>';
                bodyEle.html(strHtml);
                $('#failedDetail').modal('show');
            },
            'beforeSend': function () {
                layerIndex = layer.load(0, {
                    shade: [0.1, '#fff'] //0.1透明度的白色背景
                });
            },
            'error': function () {
                layer.msg('请求出错！');
            },
            'complete': function () {
                layer.close(layerIndex);
            },
        });
    }

    // 队列管理操作
    function doQueue(action, queue) {
        var layerIndex;
        $.ajax({
            url: '/api/queue/doQueue',
            type: 'post',
            data: {
                action: action,
                queue: queue,
            },
            success: function (data) {
                if (data.code === 0) {
                    layer.msg('操作成功！');
                } else {
                    layer.msg('操作失败！');
                }
            },
            'beforeSend': function () {
                layerIndex = layer.load(0, {
                    shade: [0.1, '#fff'] //0.1透明度的白色背景
                });
            },
            'error': function () {
                layer.msg('请求出错！');
            },
            'complete': function () {
                layer.close(layerIndex);
                $('#btn_manage').click();
            },
        });
    }

    // 失败任务操作
    function doJob(ele, action, queue) {
        var layerIndex;
        $.ajax({
            url: '/api/queue/doJob',
            type: 'post',
            data: {
                action: action,
                queue: queue,
            },
            success: function (data) {
                layer.msg(data.msg);
            },
            'beforeSend': function () {
                $(ele).find('svg').addClass('fa-spin');
            },
            'error': function () {
                layer.msg('请求出错！');
            },
            'complete': function () {
                $(ele).find('svg').removeClass('fa-spin');
                $('#btn_failed').click();
            },
        });
    }
</script>
</body>
</html>