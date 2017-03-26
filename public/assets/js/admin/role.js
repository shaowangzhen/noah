$('.rm_btn').on('click',function(){
    var url = $(this).attr('url');
    $('.rm_content').html('');
    $.ajax({
        'type':'post',
        'url':url,
        'data':{_token:_token,not_log:true},
        success:function(str){
            $('.rm_content').html(str);
            $('.rolemasters').modal('show');
        }
    });
});
$('.role_btn').on('click',function(){
    var type = $(this).data('type');
    var title = '',name = '',content = '',status = '1',url = base_url+'/admin/role/add/';
    var form = $('#form-horizontals');
    if(type == 'add'){
        title = '添加角色';
    }else{
        title = '编辑角色';
        url = base_url+'/admin/role/edit/'+$(this).data('id');
        name = $(this).data('name'),content = $(this).data('content'),status = $(this).data('status');
    }
    form.attr('action',url);
    form.find('input[name=name]').val(name);
    form.find('input[name=content]').val(content);
    form.find('input[name=status][value='+status+']').click();
    $('#myModalLabel').html(title);
    $('#roleModal').modal('show');
});

function add_role()
{
    var res = validateForm('form-horizontals');
    if(res){
        var data = $('#form-horizontals').serialize();
        $.ajax({
            type: 'POST',
            url: $('#form-horizontals').attr('action'),
            data: data,
            dataType: 'json',
            success: function (e){
                if(e.code == 1){
                    toastr.success(e.msg);
                    location.reload();
                }else{
                    toastr.success(e.msg);
                }
            },error:function (XMLHttpRequest, textStatus, errorThrown){
                if(XMLHttpRequest.status == 422){
                    var error = eval("("+XMLHttpRequest.responseText+")");
                    var str = '';
                    $.each(error,function(name,value) {
                        str += value+'<br/>';
                    });
                    str += "信息有误,请检查";
                    toastr.warning(str);
                    location.reload();
                }
            }
        });
    }
}
function disp_confirm(roleid) {
    var r = confirm('删除将不可恢复，请谨慎操作');
    if (r == true) {
        $.get(base_url+'/admin/role/del/'+roleid, function(res) {
            if(res.code == 1){
                toastr.success(res.msg);
                location.reload();
            }else{
                toastr.warning(res.msg);
            }
        },'json');
    }
}