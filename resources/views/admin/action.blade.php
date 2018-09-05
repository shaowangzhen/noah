@extends('layouts.index')

@section('pagecss')
<link rel="stylesheet" href="/assets/css/beyond.min.css">
@endsection

@section('content')
<section class="content-header">
    <h1>
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('admin/action')}}"><i class="fa fa-dashboard"></i>权限管理</a></li>
        <li class="active">权限列表</li>
    </ol>
</section>
<section class="content" style="margin:0 15px 0 15px;">
    <div class="row">
        <div class="col-sm-4 col-lg-4 col-xs-5" style="padding:40px 10px 0px 20px;">
            <div class="widget">
                <div class="widget-header bordered-bottom bordered-blue">
                    <i class="widget-icon glyphicon glyphicon-plus orange"></i>
                    <span class="widget-caption">权限结构</span>
                </div>
                <div class="widget-body">
                    <div id="MyTree" class="tree">
                        <div class="tree-folder" style="display: none;">
                            <div class="tree-folder-header">
                                <i class="fa fa-folder"></i>
                                <div class="tree-folder-name"></div>
                            </div>
                            <div class="tree-folder-content">
                            </div>
                            <div class="tree-loader" style="display: none;"></div>
                        </div>
                        <div class="tree-item" style="display: none;" >
                            <i class="tree-dot"></i>
                            <div class="tree-item-name"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-lg-8 col-xs-7" style="padding:20px 0px 0px 0px;">
            <div class="widget">
                <span class="form-title" style="padding:0px 0px 20px 20px;display:block;">权限信息</span>
                <div class="widget-body">
                    <div id="horizontal-form">
                        <form class="form-horizontal" role="form" method="post" id ='auth_form' action="{!!URL('admin/action/add')!!}" >
                            <div class="form-group">
                                <label for="actionname" class="col-sm-2 control-label no-padding-right">权限名称:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control validate" validateContent='{ "ValidateId":"actionname","ValidateTypes":"1",
                                           "Content":[{ "ErrorMsg":"权限名称不能为空" }]}' id="actionname" name='actionname' placeholder="权限名称">

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="icon" class="col-sm-2 control-label no-padding-right">图标样式:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="icon" name='icon' placeholder="填写图标样式的类名">
                                    <input type="hidden" class="form-control" id="code" name='code'>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="controller" class="col-sm-2 control-label no-padding-right">Controller:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="controller" name='controller' placeholder="控制器名">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="actions" class="col-sm-2 control-label no-padding-right">Actions:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="actions" name="actions" placeholder="动作名">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="url" class="col-sm-2 control-label no-padding-right">Url:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="url" name="url" placeholder="Url">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="order_id" class="col-sm-2 control-label no-padding-right">排序:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="order_id" name="order_id" placeholder="填写数字,按大到小排序">
                                </div>
                            </div>
                            <div class="form-group auth-type">
                                <label for="authtype" class="col-sm-2 control-label no-padding-right">权限类型:</label>
                                <div class="col-sm-3">
                                    <select class="form-control authtype validate" name="type"  validateContent='{ "ValidateId":"type","ValidateTypes":"13",
                                            "Content":[{ "ErrorMsg":"权限类型至少选择一项","ExceptValue":"0" }]}'>
                                        <option value="0">----请选择----</option>
                                        @foreach($type as $k=>$v)
                                        <option value="{{$k}}">{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-2 control-label no-padding-right">是否启用:</label>
                                <div class="col-xs-4 padding-10">
                                    <label>
                                        <input checked class="checkbox-slider colored-blue check_status" type="checkbox" value="1" name="status" id="auth_status">
                                        <span class="text"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="hidden" name="_token" value="{!!csrf_token()!!}" data-info="{!!csrf_token()!!}" class="ctm_2">
                                    <input type="hidden" name="_method" value="POST" data-info="POST" class="ctm_3">
                                    <input type="hidden" name="actionid" value="-1" id="actionid">
                                    <input type="hidden" name="pid" value="-1" id="pid">
                                    <button type="button" class="btn btn-blue active saveinfo">保存信息</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">提示信息</h4>
            </div>
            <div class="modal-body tip_body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary del_confirm" data-dismiss="modal" data-id="-1">确定</button>
            </div>
        </div>
    </div>
</div>


@endsection
@section('footerjs')
<script>
    var _token = '{!!csrf_token()!!}';
    var _type = <?php echo json_encode($type);?>;
    var _url_tree = '{!!URL("admin/action/tree")!!}';
    var _url_add = '{!!URL("admin/action/add")!!}';
    var _url_del = '{!!URL("admin/action/del")!!}';
    var _url_get = '{!!URL("admin/action/get")!!}';
</script>
<script src='/assets/js/treeview/tree-custom.min.js'></script>
<script src='/assets/js/treeview/treeview-init.js'></script>
<script src='/assets/js/admin/action.js'></script>
<!--Beyond Scripts-->
@endsection