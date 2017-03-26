$(document).ready(function () {
    $("#loginBtn").click(function () {
        if (check()) {
            login();
        }
    });
    $('.code').click(function(){
        console.log($(this).val());
        if($(this).html() == '发送验证码'){
            sendCode();
        }
    });
    $('#username').change(function(){
        codeHide();
    });
    document.onkeydown = function (event) {
        if (event.keyCode == "13") {
            document.getElementById("loginBtn").click();
        }
    }
});


function check() {
    var username = $("#username").val();
    var password = $("#password").val();
    var code = $('#code').val();
    if (username == "" || username == "undefined") {
        tip("请输入用户名!");
        $("#username").focus();
        return false;
    }
    if (password == "" || password == "undefined") {
        tip("请输入密码!");
        $("#password").focus();
        return false;
    }
    if($('#hasCode').val() == 1){
        if (code == "" || code == "undefined") {
            tip("请输入验证码!");
            $("#code").focus();
            return false;
        }
    }
    untip();
    return true;
}

function login() {
    $.ajax({
        type: "POST",
        url: "/login/check",
        data: {username: $("#username").val(), password: $("#password").val(), _token: $("#_token").val(), code: $('#code').val()},
        dataType: "json",
        success: function (data) {
            if (data.code === 1) {
                location.href = "/";
            } else if (data.code === -5){
                codeShow();
                return false;
            } else if (data.code === -6){
                codeShow();
                tip(data.msg);
                return false;
            } else {
                tip(data.msg);
                return false;
            }
        }
    });
}

function sendCode(){
    $.ajax({
        type: "POST",
        url: "/login/sendSmsCode",
        data: {username: $("#username").val(), _token: $("#_token").val()},
        dataType: "json",
        success: function (data) {
            if (data.code === 1) {
                var time = 60;
                $('.code').html(time+'s');
                var timeshows = setInterval(function(){
                    if(time == 0){
                    console.log('asdasd');
                        $('.code').html('发送验证码');
                        clearInterval(timeshows);
                        return false;
                    }
                    $('.code').html(time+'s');
                    time -= 1;
                },1000);
            } else {
                tip(data.msg);
            }
        }
    });
}

function codeShow()
{
    $('#code,.code').show();
    $('#hasCode').val(1);
}
function codeHide()
{
    $('#code,.code').hide();
    $('#hasCode').val(0);
    $('#code').val('');
    $('.code').html('发送验证码');
}

function tip(msg)
{
    $("#tip").text(msg).css('display','block');
}

function untip()
{
    $("#tip").text('').css('display','none');
}