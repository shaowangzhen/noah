
var newPasswordChecked = false;
var againPasswordChecked = false;
$(function () {
    $('.btn_sub').on('click', function (e) {
        e.preventDefault();
        if(newPasswordChecked && againPasswordChecked)
            passwordEdit();
        else{
            $('#commit').html('密码填写有误，请重新填写');
        }
    });

    $('#newpassword').keyup(function() {
        newPasswordChecked = false;
        if(passwordCheck($(this).val(), $('#tips_newpassword')))
            newPasswordChecked = true;
        return false;
    });
    $('#againpassword').keyup(function() {
        againPasswordChecked = false;
        var tempAgainPwd = $(this).val();
        if(checkAgainPwd(tempAgainPwd, $('#tips_againpassword'), $('#newpassword').val()))
            againPasswordChecked = true;
        return false;
    });

});

function checkAgainPwd(passString, tipsObj, newPwd){
    if(!passwordCheck(passString, tipsObj)) return false;
    if(passString != newPwd){
        $(tipsObj).html('新密码两次输入不同');
        return false;
    }else
        return true;
}


function passwordCheck(passString, tipsObj) {
    $('#commit').html('');
    return true;
    if(passString.length >= 6) {
        $(tipsObj).html('');
    } else {
        $(tipsObj).html('密码长度不够6位!');
        return false;
    }

    if(isLegalString(passString)) {
        $(tipsObj).html('');
    } else {
        $(tipsObj).html('密码中含有非法字符<br /> < > \' ; & # " $ * [ ] { } % ` | : , \ /');
        return false;
    }
    var patt = new RegExp(/[A-Z]/);
    if(patt.test(passString)) {
        $(tipsObj).html('');
    } else {
        $(tipsObj).html('密码中至少包含大写字母');
        return false;
    }
    return true;
}



//用户编辑
function passwordEdit()
{
    var oldPwd = $('#oldpassword').val();
    var newPwd = $('#newpassword').val();
    var againPwd = $('#againpassword').val();
    var masterId = $('#masterid').val();
    $.ajax({
        'type': 'POST',
        'url': $('#master_form').attr('action'),
        'data': {old:oldPwd,new:newPwd,again:againPwd,masterid:masterId},
        'dataType': 'json',
        success: function(e){
            toastr.options.positionClass= 'toast-bottom-right';
            if(e.code == 0){
                toastr.success(e.msg);
                setTimeout(reload, 1000);
            }else{
                toastr.warning(e.msg);
            }
        }
    });
}

function reload() {
    top.location.href = '/home/index?noah=0';
}





