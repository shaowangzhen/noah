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
            <li><a href="{{url('admin/master_pwd_list')}}"><i class="fa fa-dashboard"></i>密码管理</a></li>
            <li class="active">密码修改</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" style="margin:0 15px 0 15px;">
        <div class="widget-body">
            <div id="horizontal-form">
                <form class="form-horizontal" role="form" method="post" id="master_form" action="{{url('admin/master/pwdModify')}}">
                    <div class="form-group">
                        <label for="mastername" class="col-sm-2 control-label no-padding-right">用户名:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="mastername" name="mastername" value="{{$master['mastername']}}" disabled="true">
                        </div>

                        <span id="tips_mastername" style="padding-left:10px;color:#c23321;"></span>
                    </div>
                    <div class="form-group">
                        <label for="fullname" class="col-sm-2 control-label no-padding-right">姓名:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control validate" id="fullname" name="fullname" value="{{$master['fullname']}}" disabled="true" validateContent='{ "ValidateTypes":"1,15","Content":[{ "ErrorMsg":"姓名不能为空"},{ "ErrorMsg":"请输入中文姓名"}]}'>
                            <input id="masterid" name="masterid" type="hidden" value="{{$master['masterid']}}" />
                        </div>
                        <span id="tips_fullname" style="padding-left:10px;color:#c23321;"></span>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-2 control-label no-padding-right">手机号:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control validate" id="mobile" name="mobile" value="{{$master['mobile']}}" disabled="true" validateContent='{ "ValidateTypes":"1,8","Content":[{ "ErrorMsg":"手机号码不能为空"},{ "ErrorMsg":"请输入正确的手机号码"}]}'>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label no-padding-right">邮箱:</label>
                        <div class="col-sm-4">
                            <input type="type" class="form-control" id="email" name="email" value="{{$master['email']}}" disabled="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="deptname" class="col-sm-2 control-label no-padding-right">部门:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="deptname" name="deptname" value="{{$master['deptname']}}" disabled="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="oldpassword" class="col-sm-2 control-label no-padding-right">旧密码:</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="oldpassword" name="oldpassword" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="newpassword" class="col-sm-2 control-label no-padding-right">新密码:</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="newpassword" name="newpassword" value="">
                            <span id="tips_newpassword" style="padding-left:10px;color:#c23321;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="againpassword" class="col-sm-2 control-label no-padding-right">确认密码:</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="againpassword" name="againpassword" value="">
                            <span id="tips_againpassword" style="padding-left:10px;color:#c23321;"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right"></label>
                        <div class="col-sm-4">
                            <button id="" type="button" class="btn btn-github btn_sub">修改</button>
                            <span id="commit" style="padding-left:10px;color:#c23321;"></span>
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
    <script src="/assets/js/admin/master_pwd_edit.js?v=1"></script>
    <script>
        $(".select2").select2();
    </script>
@endsection
