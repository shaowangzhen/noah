@extends('layouts.index')
@section('pagecss')
    <link rel="stylesheet" href="/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css">
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/log')}}"><i class="fa fa-dashboard"></i>日志管理</a></li>
            <li class="active">日志列表</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" style="margin:0 15px 0 15px;">
        <div class="row">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Form -->
                                <div class="box box-default" style="padding:12px;">
                                    <!-- form start -->
                                    <form class="form-inline">
                                        <div class="form-group">
                                            <label for="mastername">用户名</label>
                                            <input type="text" class="form-control" name="mastername" id="mastername" value='{{$params['mastername'] or ''}}' />
                                        </div>
                                        <div class="form-group">
                                            <label for="url">URL</label>
                                            <input type="text" class="form-control" name="url" id="url" value='{{$params['url'] or ''}}' />
                                        </div>
                                        <button type="submit" class="btn btn-default">查询</button>
                                    </form>
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title">共{{$lists->total()}}条</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover table-bordered table-striped">
                                            <tr>
                                                <th width="10%"> 日志ID </th>
                                                <th width="15%"> 操作者用户名 </th>
                                                <th width="15%"> URL </th>
                                                <th width="15%"> 操作时间 </th>
                                                <th width="15%"> 操作IP </th>
                                                <th width="10%"> 浏览器 </th>
                                                <th width="10%"> 操作系统 </th>
                                                <th width="10%"> 日志详情 </th>
                                            </tr>
                                            @foreach ($lists as $list)
                                            <tr>
                                                <td>{{$list['logid']}}</td>
                                                <td>{{$list['mastername']}}</td>
                                                <td>{{$list['url']}}</td>
                                                <td>{{$list['createtime']}}</td>
                                                <td>{{$list['ip']}}</td>
                                                <td>{{$list['browser']}}</td>
                                                <td>{{$list['platform']}}</td>
                                                <td><button class="btn btn-default btn-xs btn_info" data-logid="{{$list['logid']}}">查看详情</button></td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer clearfix">
                                        {!! $lists->appends($params)->render() !!}
                                    </div>
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!--check modal start-->
        <div class="modal fade info_modal bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">日志详情</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered info_table">
                            <tr><td>用户名</td><td></td></tr>
                            <tr><td>URL</td><td></td></tr>
                            <tr><td>操作时间</td><td></td></tr>
                            <tr><td>操作ip</td><td></td></tr>
                            <tr><td>浏览器</td><td></td></tr>
                            <tr><td>操作系统</td><td></td></tr>
                            <tr><td>GET</td><td></td></tr>
                            <tr><td>POST</td><td></td></tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        <!--check modal end-->
    </section>
    <!-- /.content -->
@endsection
@section('pagejs')
<script>
var base_url = '{!!url()!!}';
var _token = '{!!csrf_token()!!}';
</script>
<script src="{{url()}}/assets/js/admin/log.js"></script>
@endsection