<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Noah运营后台系统·登录</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" href="/assets/css/base.css">
    <link rel="stylesheet" href="/assets/css/jquery.fancybox.css">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
    <style type="text/css">
        .Sybg{background:url(/assets/img/login/login_bg.png) center 0 no-repeat;}
        .Login{width:340px;position:absolute;left:50%;margin-left:-170px;top:40%;margin-top:-156px;}
        .Login img{display:block;margin:10px auto 0;}
        .Login .entry{width:340px;background:#fff;border-radius:10px;margin-top:2px;position:relative;}
        .Login input{height:60px;width:340px;font:normal 16px/60px "Microsoft Yahei";color:#838383;text-indent:40px;border:none;}
        .Login .ipt-name{background:url(/assets/img/login/phone.png) no-repeat 15px center;border-bottom:1px solid #f7f7f7;}
        .Login .ipt-lock{background:url(/assets/img/login/pd.png) no-repeat 15px center;}
        .Login .entry p{position:absolute;left:355px;top:15px;width:90px;height:30px;background:#ff6c00;font:normal 14px/30px "Microsoft Yahei";color:#fff;text-align:center;border-radius:4px;-webkit-border-radius:4px;display:none;}
        .Login .code{cursor:pointer;position:absolute;left:245px;top:138px;width:90px;height:30px;background:#ff6c00;font:normal 14px/30px "Microsoft Yahei";color:#fff;text-align:center;border-radius:4px;-webkit-border-radius:4px;display:none;}
        .Login a{display:block;height:55px;width:340px;background:#2197f1;border-radius:10px;-webkit-border-radius:10px;
            -moz-border-radius:10px;font:normal 16px/55px "Microsoft Yahei";color:#fff;text-align:center;margin-top:14px;background:#d28711;}
    </style>
</head>
<body class="Sybg">
<div class="Login">
    <img src="/assets/img/login/noah.png">
    <form method="post">
        <div class="entry">
            <input type="text" id="username" name="username" placeholder="请输入您的用户名" class="ipt-name">
            <input type="password" id="password" name="password" placeholder="请输入密码" class="ipt-lock">
            <input type="text" id="code" name="code" placeholder="请输入手机验证码" class="ipt-name" style="display:none;">
            <span class="code btn">发送验证码</span>
            <p class="error" id="tip" style="width:160px;"></p>
        </div>
        <input id="_token" name="_token" type="hidden" value="{{csrf_token()}}" />
        <input id="hasCode" type="hidden" value="" />
        <a href="javascript:;" id="loginBtn" class="login-btn">登录</a>
    </form>
</div>
<div id="maskLayer" class="fancybox-overlay fancybox-overlay-fixed" style="width: auto; height: auto; display: none;">
    <div id="contentLayer" class="fancybox-wrap fancybox-desktop fancybox-type-iframe fancybox-opened" tabindex="-1" style="width: 630px; height: auto; position: absolute; top: 100px; left: 100px; opacity: 1; overflow: visible;">
        <div class="fancybox-skin" style="padding: 15px; width: auto; height: auto;">
            <div class="fancybox-outer">
                <div class="fancybox-inner" style="overflow: auto; width: 600px; height: 350px;">
                    <iframe id="fancybox-frame1461914177253" name="fancybox-frame1461914177253" width="600" height="600" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" scrolling="auto" src="/login/resetpassword"></iframe>
                </div>
            </div>
            <a id="closeBtn" class="fancybox-item fancybox-close" href="javascript:;"></a>
        </div>
    </div>
</div>
<script src="/assets/js/jQuery/jQuery-1.9.1.min.js"></script>
<script src="/assets/plugins/jQueryUI/jquery-ui.min.js"></script>
<script src="/assets/js/login.js?v=3"></script>
<script src="/assets/js/jquery.fancybox.pack.js"></script>
<script>
    $(function(){
        if(parent.location.href != this.location.href){
            parent.location.href = this.location.href;
        }
        pageHeight();
        //switchClass();
        function fResize(){
            pageHeight();
        }
        //设置page高度
        function pageHeight() {
            $('.Sybg').height($(window).height());
        }
        $(window).bind('resize',function(){
            fResize();
        });
        setWindowPos();
        $('#closeBtn').click(function() {
            $('#maskLayer').fadeOut();
        });
        $('#resetBtn').click(function() {
            $('#maskLayer').fadeIn();
        });
    });
    function setWindowPos() {
        var width = document.documentElement.clientWidth;
        var height = document.documentElement.clientHeight;
        var winWidth = $('#contentLayer').width();
        var winHeight = $('#contentLayer').height();
        var posLeft = (width/2) - (winWidth/2);
        var posTop = (height/2) - (winHeight/2);
        $('#contentLayer').css({'left': posLeft + 'px', 'bottom': posTop + 'px'});
    }
</script>
<!--[if lte IE 6]>
<script src="/assets/js/common/DD_belatedPNG.js"></script>
<![endif]-->
</body>
</html>
