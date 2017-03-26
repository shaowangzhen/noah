$(function(){
    $('.btn_set').on('click',function(){
        set_sms();
    });
    $('.check_sms').on('click',function(){
        check_sms();
    });
    $('.btn_send').on('click',function(){
        var res = validateForm('send_sms');
        if(res){
            send_sms();
        }
    });
});

//查询短信情况
function check_sms()
{
    var m = $('.check_modal');
    $.get(base_url+'/admin/sms/check',function(e){
        var data = e.data;
        if(data){
            $('.ck_zz').html(data.zz_balance+'（条）');
            $('.ck_lt').html(data.lt_balance+'（元）');
            $('.check_modal').modal('show');
        }
    },'json');
}

//设置短信
function set_sms()
{
    var form = $('#set_sms');
    $.ajax({
        'type':'post',
        'url':base_url+'/admin/sms/set',
        'data':form.serialize(),
        'dataType':'json',
        success:function(e){
            if(e.code == 1){
                //toastr.success(e.msg);
                bootbox.alert(e.msg, function(){
                    $('.set_modal').modal('hide');
                });
                
            }else{
                toastr.warning(e.msg);
            }
        }
    });
}

//发送短信
function send_sms()
{
    var form = $('#send_sms');
    $.ajax({
        'type':'post',
        'url':base_url+'/admin/sms/send',
        'data':form.serialize(),
        'dataType':'json',
        success:function(e){
            if(e.code == 1){
                //toastr.success(e.msg);
                bootbox.alert(e.msg, function(){
                    hide_sms_send();
                });
                
            }else{
                toastr.warning(e.msg);
            }
        }
    });
}

function hide_sms_send()
{
    var s = $('.send_modal');
    var m = $('#s_mobile');
    var c = $('#s_content');
    m.val('');
    c.val('');
    s.modal('hide');
}