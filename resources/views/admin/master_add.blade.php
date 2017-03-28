@extends('layouts.index')
@section('pagecss')
<link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection
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
    <div class="widget-body">
        <div id="horizontal-form">
            <form class="form-horizontal" role="form" method="post" id="master_form" action="{{url('admin/master/add')}}">
                <div class="form-group">
                    <label for="mastername" class="col-sm-2 control-label no-padding-right">用户名:</label>
                    <div class="col-sm-4">
                        <div id="name_div" style="width: 93%; float: left;">
                            <input type="text" class="form-control validate" id="mastername" name="mastername" validateContent='{ "ValidateTypes":"1","Content":[{ "ErrorMsg":"用户名不能为空"}]}'>
                        </div>
                    </div>
                    <button type="button" id="ldap_search" class="btn btn-danger" onclick="searchDBUser()">搜索</button>
                    <span id="tips_mastername" style="padding-left:10px;color:#c23321;"></span>
                </div>
                <div class="form-group">
                    <label for="fullname" class="col-sm-2 control-label no-padding-right">姓名:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control validate" id="fullname" name="fullname" validateContent='{ "ValidateTypes":"1,15","Content":[{ "ErrorMsg":"姓名不能为空"},{ "ErrorMsg":"请输入中文姓名"}]}'>
                    </div>
                    <span id="tips_fullname" style="padding-left:10px;color:#c23321;"></span>
                </div>
                <div class="form-group">
                    <label for="mobile" class="col-sm-2 control-label no-padding-right">手机号:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control validate" id="mobile" name="mobile" validateContent='{ "ValidateTypes":"1,8","Content":[{ "ErrorMsg":"手机号码不能为空"},{ "ErrorMsg":"请输入正确的手机号码"}]}'>
                    </div>
                </div>
                <div class="form-group" id="email_div">
                    <label for="email" class="col-sm-2 control-label no-padding-right">邮箱:</label>
                    <div class="col-sm-4">
                        <input type="type" class="form-control" id="email" name="email">
                    </div>
                    <span id="tips_email" style="padding-left:10px;color:#c23321;"></span>
                </div>
                <div class="form-group">
                    <label for="deptname" class="col-sm-2 control-label no-padding-right">部门:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="deptname" name="deptname">
                    </div>
                </div>

                <div class="form-group auth-type">
                    <label for="status" class="col-sm-2 control-label no-padding-right">帐号状态:</label>
                    <div class="col-sm-3">
                        <select class="form-control authtype validate" name="status">
                            <option value="0">----请选择----</option>
                            @foreach($status as $key=>$val)
                            <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="roles" class="col-sm-2 control-label no-padding-right">功能角色:</label>
                    <div class="col-sm-4">
                        @foreach($roles as $role)
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name='roleids[]' value="{{$role['roleid']}}">{{$role['rolename']}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right"></label>
                    <div class="col-sm-4">
                        <input id="_token" name="_token" type="hidden" value="{{csrf_token()}}" />
                        <input id="searched" name="searched" type="hidden" value="0" />
                        <input id="checked" name="checked" type="hidden" value="0" />
                        <button type="button" class="btn btn-github btn_sub">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection
@section('footerjs')
<script src="/assets/plugins/select2/select2.full.min.js"></script>
<script src="/assets/js/admin/master_add.js?v=1"></script>
<script>
    $(".select2").select2();
</script>
@endsection
