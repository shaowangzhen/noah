@extends('layouts.index')
@section('pagecss')
    <style>
        .form-horizontal{
            margin-top:20px;
        }
    </style>

@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('admin/whitelist')}}"><i class="fa fa-dashboard"></i>白名单管理</a></li>
        <li class="active">白名单添加</li>
    </ol>
</section>

<!-- Main content -->
<section class="content" style="margin:0 15px 0 15px;">
    <div class="widget-body">
        <div id="horizontal-form">
            <form class="form-horizontal" role="form" method="post" id="master_form" action="{{url('admin/whitelist/addCheck')}}">
                <div class="form-group">
                    <label for="content" class="col-sm-2 control-label no-padding-right">姓名&nbsp;设备号:</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="content" name="content" placeholder="请按照‘姓名+空格+设备号’的形式列出，每行写一组，如‘小明 abcd123def’" style="width:500px;height:400px;"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right"></label>
                    <div class="col-sm-4">
                        <input id="_token" name="_token" type="hidden" value="{{csrf_token()}}" />
                        <input name="masterid" id="masterid" type="hidden" value="{{$masterid}}" />
                        <input style="width:70px;height:40px;" type="submit" id="btn_submit" value="提交"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection

