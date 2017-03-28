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
            <li><a href="{{url('admin/sms')}}"><i class="fa fa-dashboard"></i>短信管理</a></li>
            <li class="active">短信列表</li>
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
                                            <label for="start_date">时间选择:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control datepicker" id="start_date" name="start_date" value="{{$params['start_date'] or ''}}">
                                                <div class="input-group-addon">至</div>
                                                <input type="text" class="form-control datepicker" id="end_date" name="end_date" value="{{$params['end_date'] or ''}}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="mobile">手机</label>
                                            <input type="mobile" class="form-control" name="mobile" id="mobile" value='{{$params['mobile'] or ''}}' />
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
                                        <a type="button" class="btn btn-primary" data-toggle="modal" data-target=".set_modal">短信设置</a>
                                        <a type="button" class="btn btn-primary check_sms">通道余额查询</a>
                                        <a type="button" class="btn btn-danger" data-toggle="modal" data-target=".send_modal">发送短信</a>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive no-padding">
                                        <table class="table table-hover table-bordered table-striped">
                                            <tr>
                                                <th width="5%"> 短信ID </th>
                                                <th width="10%"> 手机号 </th>
                                                <th width="5%"> 发送状态 </th>
                                                <th width="15%"> 原因说明 </th>
                                                <th width="10%"> 用户发送时间 </th>
                                                <th width="10%"> 通道发送时间 </th>
                                                <th width="10%"> 发送标识 </th>
                                                <th width="25%"> 短信内容 </th>
                                                <th width="10%"> 操作人IP </th>
                                            </tr>
                                            @foreach ($lists as $list)
                                            <tr>
                                                <td>{{$list['messageid']}}</td>
                                                <td>{{$list['mobile']}}</td>
                                                <td>{{$status[$list['status']]}}</td>
                                                <td>{{$list['reason']}}</td>
                                                <td>{{date('Y-m-d H:i:s',$list['createtime'])}}</td>
                                                <td>{{date('Y-m-d H:i:s',$list['updatetime'])}}</td>
                                                <td title="{{$list['rrid']}}">{{str_limit($list['rrid'],7,'....')}}</td>
                                                <td title="{{$list['content']}}">{{str_limit($list['content'],40,'....')}}</td>
                                                <td>{{$list['ip']}}</td>
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
        <!--set modal start-->
        <div class="modal fade set_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">短信设置</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="set_sms">
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right">是否从消息队列发:</label>
                            <div class="col-sm-6">
                                <label class="radio-inline">
                                    <input type="radio" name="sms_send_from_q" value="1" @if($sms_config['sms_send_from_q']==1) checked @endif> 是
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="sms_send_from_q" value="0" @if($sms_config['sms_send_from_q']==0) checked @endif> 否
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label text-right">短信通道:</label>
                            <div class="col-sm-6">
                                @foreach($sms_config['sms_account'] as $k=>$v)
                                <label class="radio-inline">
                                    <input type="radio" name="sms_account_key" value="{{$k}}" @if($k==$sms_pipe) checked @endif > {{$v['sms_account_name']}}
                                </label>
                                @endforeach
                            </div>
                        </div>
                            <input type='hidden' name='_token' value='{!!csrf_token()!!}' />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary btn_set">提交</button>
                    </div>
                </div>
            </div>
        </div>
        <!--set modal end-->
        <!--check modal start-->
        <div class="modal fade check_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">通道余额查询</h4>
                    </div>
                    <div class="modal-body">
                        <span class='col-sm-5'>[天润]通道余额：<span class='ck_zz'></span></span>
                        <span class='col-sm-6'>[联通]通道余额：<span class='ck_lt'></span></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        <!--check modal end-->
        <!--send modal start-->
        <div class="modal fade send_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">短信设置</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="send_sms">
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">手机:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control validate" rows="3" id='s_mobile' name="mobile" autocomplete="off" validateContent='{ "ValidateTypes":"1","Content":[{ "ErrorMsg":"手机号不能为空"}]}'></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-right">内容:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control validate" rows="3" id='s_content' name="content" autocomplete="off" validateContent='{ "ValidateTypes":"1","Content":[{ "ErrorMsg":"内容不能为空"}]}'></textarea>
                            </div>
                        </div>
                            <input type='hidden' name='_token' value='{!!csrf_token()!!}' />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary btn_send">提交</button>
                    </div>
                </div>
            </div>
        </div>
        <!--send modal end-->
    </section>
    <!-- /.content -->
@endsection
@section('pagejs')
    <script src="/assets/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js"></script>
    <script src="/assets/plugins/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
    <script type="text/javascript">
        $('.datepicker').datetimepicker({
            language: "zh-CN",
            format: 'yyyy-mm-dd',
            autoclose: true,
            minView: "month"
        });
        var base_url = '{!!url()!!}';
    </script>
    <script src="/assets/js/admin/sms.js?v=3"></script>
@endsection