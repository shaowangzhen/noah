@extends('layouts.index')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{url('admin/role')}}"><i class="fa fa-dashboard"></i>角色管理</a></li>
            <li class="active">角色列表</li>
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
                                            <label for="name">角色名</label>
                                            <input type="name" class="form-control" name="name" id="name" />
                                        </div>
                                        <button type="submit" class="btn btn-default">查询</button>
                                        <a type="button" class="btn btn-default role_btn" data-type="add" style="float:right;">添加</a>
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
                                                <th width="10%"> 角色ID </th>
                                                <th width="20%"> 角色名称 </th>
                                                <th width="25%"> 描述 </th>
                                                <th width="10%"> 状态 </th>
                                                <th width="15%"> 创建时间 </th>
                                                <th width="20%"> 操作 </th>
                                            </tr>
                                            @foreach ($lists as $list)
                                            <tr>
                                                <td>{{$list['roleid']}}</td>
                                                <td>{{$list['name']}}</td>
                                                <td>{{$list['content']}}</td>
                                                <td>{{$status[$list['status']]}}</td>
                                                <td>{{$list['createtime']}}</td>
                                                <td>
                                                    <a url="{{url('admin/role/masters/'.$list['roleid'])}}" class="btn btn-primary btn-xs rm_btn">用户</a>
                                                    <a class="btn btn-primary btn-xs role_btn" data-id='{{$list['roleid']}}' data-name='{{$list['name']}}' data-content='{{$list['content']}}' data-status='{{$list['status']}}'>编辑</a>
                                                    <a href="{{url('admin/roleaction/set/'.$list['roleid'])}}" class="btn btn-primary btn-xs">权限分配</a>
                                                    <a class="btn btn-danger btn-xs" onclick="disp_confirm({{$list['roleid']}})">删除</a>
                                                </td>
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
        <div class="modal fade" id="roleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">添加角色</h4>
                    </div>
                    <div class="modal-body">

                        <form class="form-horizontal" id="form-horizontals" role="form" action="" method="post" >
                            <div class="form-group">
                                <label for="create_rolename" class="col-sm-2 control-label">角色名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate" id="create_rolename" name="name"
                                           placeholder="请输入角色名称" validateContent='{ "ValidateTypes":"1","Content":[{ "ErrorMsg":"角色名不能为空"}]}'>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="lastname" class="col-sm-2 control-label">角色描述</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control validate" id="content" name="content"
                                           placeholder="请输角色描述" validateContent='{ "ValidateTypes":"1","Content":[{ "ErrorMsg":"角色描述不能为空"}]}'>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lastname" class="col-sm-2 control-label">是否启用</label>
                                <div class="radio">
                                    @foreach($status as $k=>$v)
                                    <label>
                                        <input name="status" type="radio" value="{{$k}}">
                                        <span class="text">{{$v}}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-5 col-sm-6">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                    <button type="button"  onclick='add_role();' class="btn btn-primary">提交</button>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!--rolemasters-->
        <div class="modal fade bs-example-modal-lg rolemasters" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">用户列表</h4>
                    </div>
                    <div class="modal-body rm_content"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
@section('pagejs')
<script>
    var base_url = "{!!url('/')!!}";
    var _token = "{!!csrf_token()!!}";
</script>
<script src="{{url('/')}}/assets/js/admin/role.js"></script>
@endsection