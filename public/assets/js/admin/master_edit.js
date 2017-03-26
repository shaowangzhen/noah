var nameChecked = false;
var passwordChecked = false;
$(function () {
    selectSearchButton($('#is_dealer').val());
    $("body").on('blur','.has-error .validate:enabled',function(e){
        e.preventDefault();
        validateForm('master_form');
    });
    $('.btn_sub').on('click', function (e) {
        e.preventDefault();
        var res = validateForm('master_form');
        if(res){
            masterEdit();
        }
        else{
            $('.has-error .validate:enabled').eq(0).focus();
        }
    });
    $('#is_dealer').change(function() {
        selectSearchButton($(this).val());
        return false;
    });
    $('#fullname').keyup(function() {
        nameChecked = false;
        fullnameCheck($(this).val());
        return false;
    });
    $('#password').keyup(function() {
        passwordChecked = false;
        passwordCheck($(this).val());
        return false;
    });
});

function fullnameCheck(fullnameStr) {
    if(isChinese(fullnameStr)) {
        $('#tips_fullname').html('');
        nameChecked = true;
    } else {
        $('#tips_fullname').html('请输入中文名!');
        nameChecked = false;
    }
    return false;
}

function passwordCheck(passString) {
    if(passString.length >= 6) {
        $('#tips_password').html('');
    } else {
        $('#tips_password').html('密码长度不够6位!');
        passwordChecked = false;
        return false;
    }
    if(isLegalString(passString)) {
        $('#tips_password').html('');
    } else {
        $('#tips_password').html('密码中含有非法字符<br /> < > \' ; & # " $ * [ ] { } % ` | : , \ /');
        passwordChecked = false;
        return false;
    }
    var patt = new RegExp(/[A-Z]/);
    if(patt.test(passString)) {
        $('#tips_password').html('');
    } else {
        $('#tips_password').html('密码中至少包含大写字母');
        passwordChecked = false;
        return false;
    }
    passwordChecked = true;
    return false;
}

function formCheck() {
    if($('#is_dealer').val() == 1) {
        fullnameCheck($('#fullname').val());
        var password = $('#password').val();
        if(password.trim() == '') {
            passwordChecked = true;
        } else {
            passwordCheck($('#password').val());
        }
    } else {
        nameChecked = true;
        passwordChecked = true;
    }

    if(nameChecked && passwordChecked) {
        $('#checked').val('1');
    } else {
        $('#checked').val('0');
    }
    return false;
}

//用户编辑
function masterEdit()
{
    formCheck();
    if($('#checked').val() == '0') {
        alert('部分信息不符合验证要求！');
        return false;
    }
    var data = $('#master_form').serialize();
    $.ajax({
        'type': 'POST',
        'url': $('#master_form').attr('action'),
        'data': data,
        'dataType': 'json',
        success: function(e){
            toastr.options.positionClass= 'toast-bottom-right';
            if(e.code == 1){
                toastr.success(e.msg);
                setTimeout(reload, 3000);
            }else{
                toastr.warning(e.msg);
            }
        }
    });
}

function reload() {
    self.location=document.referrer;
}

//判断显示搜索按钮
function selectSearchButton(data) {
    switch (data) {
        case '0':
            $('#tips_mastername').html('');
            $('#new_dealer_id').val(0);
            $('#ldap_search').show();
            $('#db_search').hide();
            $('#password').attr("disabled", true);
            $('#dealer_select').attr("disabled", true);
            break;
        case '1':
            $('#tips_mastername').html('');
            $('#db_search').show();
            $('#ldap_search').hide();
            $('#password').attr("disabled", false);
            $('#dealer_select').attr("disabled", false);
            break;
    }
    return false;
}

function searchLdapUser()
{
    var mastername = $('#mastername').val();
    mastername = mastername.trim();
    if(mastername == '') {
        $('#searched').val(0);
        $('#tips_mastername').html('请输入用户名!');
        return false;
    }
    $.getJSON('/admin/master/ajaxldapuser/', {mastername: mastername}, function (data) {
        if (data) {
            $('#fullname').val(data.fullname);
            $('#mobile').val(data.mobile);
            $('#email').val(data.email);
            $('#deptname').val(data.deptname);
            $('#searched').val(1);
            $('#tips_mastername').html('');
        } else {
            $('#fullname').val('');
            $('#mobile').val('');
            $('#email').val('');
            $('#deptname').val('');
            $('#searched').val(0);
            $('#tips_mastername').html('用户不存在!');
        }
    });
}

function searchDBUser()
{
    var mastername = $('#mastername').val();
    mastername = mastername.trim();
    if(mastername == '') {
        $('#searched').val(0);
        $('#tips_mastername').html('请输入用户名!');
        return false;
    }
    $.getJSON('/admin/master/ajaxDBUser/', {mastername: mastername}, function (data) {
        if (data) {
            switch (data.code) {
                case '0':
                    $('#searched').val(1);
                    $('#tips_mastername').html('用户名可用');
                    break;
                case '1':
                    $('#searched').val(0);
                    $('#tips_mastername').html('用户名已被占用!');
                    break;
            }
        } else {
            $('#fullname').val('');
            $('#mobile').val('');
            $('#email').val('');
            $('#deptname').val('');
            $('#searched').val(0);
            $('#tips_mastername').html('搜索失败!');
        }
    });
}