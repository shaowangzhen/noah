@extends('layouts.index')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('admin/whitelist')}}"><i class="fa fa-dashboard"></i>白名单管理</a></li>
        <li class="active">白名单列表</li>
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
                                <form class="form-inline" action="" method="get" id="searchForm">
                                    <div class="form-group">
                                        <label>姓名:</label>
                                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder=""
                                               value="@if(isset($params['fullname'])){{$params['fullname']}}@endif">
                                    </div>
                                    <div class="form-group">
                                        <label>设备号:</label>
                                        <input type="text" class="form-control" id="device" name="device" placeholder=""
                                               value="@if(isset($params['device'])){{$params['device']}}@endif">
                                    </div>
                                    <button type="submit" class="btn btn-primary">查询</button>
                                    <a type="button" href="{{url('/admin/whitelist/add')}}" class="btn btn-default" style="float:right;">添加白名单</a>
                                </form>
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">共{{$lists->count}}条</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover table-bordered table-striped">
                                        <tr>
                                            <th> 序号 </th>
                                            <th> ID </th>
                                            <th> 姓名 </th>
                                            <th> 设备号 </th>
                                            <th> 创建时间 </th>
                                            <th> 操作员 </th>
                                            <th> 操作 </th>
                                        </tr>
                                        @foreach ($lists as $list)
                                            <tr>
                                                <td>{{ $list->index }}</td>
                                                <td>{{ $list->id }}</td>
                                                <td>{{ $list->name }}</td>
                                                <td>{{ $list->device }}</td>
                                                <td>{{ $list->created_at }}</td>
                                                <td>{{ $list->fullname }}</td>
                                                <td><button id = "delete" class="btn btn-danger btn-xs" style="width:60px;" onclick="delete_data({{$list['id']}})">删除</button>
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
</section>
<!-- /.content -->
@endsection
@section('pagejs')
    <script type="text/javascript">
        function delete_data(id) {
            if (confirm('确定要删除该设备号吗？')) {
                $.post('/admin/whitelist/del', {id: id});
                location.replace(location);
            }
        }
    </script>
@endsection

