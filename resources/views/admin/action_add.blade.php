@extends('layouts.index')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('admin/action')}}"><i class="fa fa-dashboard"></i>权限管理</a></li>
        <li class="active">权限列表</li>
    </ol>
</section>

<!-- Main content -->
<section class="content" style="margin:0 15px 0 15px;">
    <div class="widget-body">
        <div id="horizontal-form">
            <form class="form-horizontal" role="form" method="post" id="action_form" action="{{url('admin/action/add')}}">
                <div class="form-group">
                    <label for="controller" class="col-sm-2 control-label no-padding-right">controller:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="controller" name="controller" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="action" class="col-sm-2 control-label no-padding-right">action:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="action" name="action" value="">
                    </div>
                </div>
                <div class="form-group">
                    <label for="actionname" class="col-sm-2 control-label no-padding-right">名称:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="actionname" name="actionname">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label no-padding-right">展示:</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="type">
                            @foreach($type as $k=>$v)
                            <option value="{{$k}}">{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="parent_actionid" class="col-sm-2 control-label no-padding-right">父级:</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="parent_actionid">
                            @foreach($parent_lists as $key=>$val)
                            <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="orderid" class="col-sm-2 control-label no-padding-right">排序:</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="orderid" name="orderid" value='0'>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right"></label>
                    <div class="col-sm-4">
                        <input id="_token" name="_token" type="hidden" value="{{csrf_token()}}" />
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
<script src="/assets/js/admin/action_add.js"></script>
@endsection
