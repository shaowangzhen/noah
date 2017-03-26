$(document).ready(function () {
    $("#loginBtn").click(function () {
        if (check()) {
            login();
        }
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
    untip();
    return true;
}

function login() {
    $.ajax({
        type: "POST",
        url: "/login/check",
        data: {username: $("#username").val(), password: $("#password").val(), _token: $("#_token").val()},
        dataType: "json",
        success: function (data) {
            if (data.code === 1) {
                location.href = "/api/applyStat";
            } else {
                tip(data.msg);
                return false;
            }
        }
    });
}

function tip(msg)
{
    $("#tip").text(msg).css('display','block');
}

function untip()
{
    $("#tip").text('').css('display','none');
}