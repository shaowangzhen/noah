@extends('layouts.index')

@section('pagecss')
<link rel="stylesheet" href="/assets/css/beyond.min.css">
<style>
    .checkbox-inline{margin-left: 10px;}
</style>
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('admin/master')}}"><i class="fa fa-dashboard"></i>用户管理</a></li>
        <li class="active">用户编辑</li>
    </ol>
</section>

<!-- Main content -->
<section class="content" style="margin:0 15px 0 15px;">
    <div class="row" style="display: none;">
   
    <div id="ajaxtables">
        ...
    </div>
</div>
<!--加载 部分js-->                   
<!--树形2-->
<div class="col-sm-12 col-lg-12 col-xs-12">
    <div class="row">
        <!--创建新table-->
        <div style="padding:0px">
            <div class="tabbable">
                <ul class="nav nav-tabs tabs-flat1" id="myTab11">
                    <li class="active">
                        <a data-toggle="tab" href="#tap_one">
                            岗位权限
                        </a>
                    </li>
                </ul>
                <div class="tab-content tabs-flat">
                    <div id="tap_one" class="tab-pane in active">
                        <!-- 权限 -->
                        <div class="" style="padding:0px">
                            <div class="widget">
                                <div class="widget-body widget_body">
                                    <div class="htmleaf-container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group" style="margin-top:15px;">
                                                    <button type="button" class="btn btn-success btn-xs f" id="btn-check-all">全选</button>
                                                    <button type="button" class="btn btn-danger btn-xs" id="btn-uncheck-all">取消全选</button>
                                                    <button type="button" class="btn btns btn-default btn-primary btn-xs" data-toggle="modal">提交</button>
                                                </div>
                                                <div id="treeview-checkable" class=""  style=" /*height:400px; overflow-y:scroll;*/"></div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div id="checkable-output"></div>
                                            </div>
                                        </div>
                                        <form action="" method="post" class="form-horizontal">
                                            <table>
                                                <tr id="app">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                                <input type="hidden" name="role_id" value="{{$role_id}}"/>
                                                </tr>
                                            </table>
                                        </form>  
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <!-- 权限 end -->
                    </div>
                    <div id="tap_two" class="tab-pane">
                        <!--数据权限-->
                        <div class="widget">
                            <div class="widget-body widget_body">
                                <div class="city-container">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group" style="margin-top:15px;">
                                                <button type="button" class="btn btn-success btn-xs f" id="city-check-all">全选</button>
                                                <button type="button" class="btn btn-danger btn-xs" id="city-uncheck-all">取消全选</button>
                                                <button type="button" class="btn city_btns btn-default btn-primary btn-xs">提交</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!--数据权限end-->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-xs-12">
        </div>
    </div>
</div>
<!--树形2end-->
</section>
<!-- /.content -->
@endsection
@section('footerjs')
<script>
    var url = "{{url('/')}}";
    var role_id = "{{$role_id}}";
    var defaultData = <?php echo htmlspecialchars_decode($json);?>;
</script>
<script src="{{url('/')}}/assets/js/treeview/bootstrap-treeview.min.js"></script>
<script src="{{url('/')}}/assets/js/admin/roleaction.js?v=1"></script>
@endsection
