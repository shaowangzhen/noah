@extends('layouts.index')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/master')}}"><i class="fa fa-dashboard"></i>用户管理</a></li>
            <li class="active">用户列表</li>
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
                                            <input type="mastername" class="form-control" name="mastername" id="mastername" value='{{$params['mastername'] or ''}}' />
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile">手机</label>
                                            <input type="mobile" class="form-control" name="mobile" id="mobile" value='{{$params['mobile'] or ''}}' />
                                        </div>
                                        <button type="submit" class="btn btn-default">查询</button>
                                        <a type="button" href="master/add" class="btn btn-default" style="float:right;">添加</a>
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
                                                <th> ID </th>
                                                <th> 用户名 </th>
                                                <th> 姓名 </th>
                                                <th> 手机 </th>
                                                <th> 角色 </th>
                                                <th> 状态 </th>
                                                <th> 创建时间 </th>
                                                <th> 操作 </th>
                                            </tr>
                                            @foreach ($lists as $list)
                                            <tr>
                                                <td>{{$list['masterid']}}</td>
                                                <td>{{$list['mastername']}}</td>
                                                <td>{{$list['fullname']}}</td>
                                                <td>{{$list['mobile']}}</td>
                                                <td>
                                                    @foreach($list['roles'] as $role)
                                                    {{$role['name']}}<br />
                                                    @endforeach
                                                </td>
                                                <td>{{$list['statusname']}}</td>
                                                <td>{{$list['createtime']}}</td>
                                                <td><a href="{{url('admin/master/edit/'.$list['masterid'])}}" class="btn btn-primary btn-xs">修改</a></td>
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
