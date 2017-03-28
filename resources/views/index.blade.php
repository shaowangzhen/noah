<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Noah运营后台系统</title>
    <!-- Tell the browser to be responsive to screen width -->
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/assets/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/assets/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/assets/css/skins/_all-skins.min.css">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/assets/js/html5shiv.min.js"></script>
    <script src="/assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="/" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>Noah</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Noah</b>运营后台系统</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li>
                        <a><span>您好！{{session('userInfo')['users']['fullname']}}</span></a>
                    </li>
                    <li>
                        <a><span class="span_showdata"></span></a>
                    </li>
                    <li>
                        <a href="/logout"><span>退出</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <ul class="sidebar-menu">
                {{--<li class="header">功能列表</li>--}}
                <li class="active">
                    <a href="/">
                        <i class="fa fa-dashboard"></i> <span>首页</span>
                    </a>
                </li>
                @foreach($menus as $menu)
                    <li class="treeview">
                        <a href="#">
                            <i class="fa {{!empty($menu['icon'])?$menu['icon']:'fa-circle-o'}}"></i>
                            <span>{{$menu['name']}}</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            @foreach($menu['child'] as $child)
                                <li><a href="#" url="{{$child['url']}}"><i
                                                class="fa fa-circle-o"></i> {{$child['name']}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="-webkit-overflow-scrolling: touch;overflow-y: scroll;">
        <iframe style="width:100%;min-height:900px;" scrolling="auto" frameborder="0" class="" id="pagebody_iframe"
                src="/main"></iframe>
    </div>
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.1.4 -->
<script src="/assets/js/jQuery/jQuery-1.9.1.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/assets/plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.5 -->
<script src="/assets/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="/assets/js/raphael-min.js"></script>
<script src="/assets/plugins/morris/morris.min.js"></script>
<!-- Slimscroll -->
<script src="/assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/assets/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/assets/js/demo.js"></script>
<script type="text/javascript">
    $(".sidebar-menu .treeview-menu a").click(function () {
        url = $(this).attr('url');
        $("ul.sidebar-menu li.active").removeClass("active");
        $(this).parent().parent().parent().addClass("active");
        $(this).parent().addClass("active");
        $("#pagebody_iframe").attr("src", url);
        $("#pagebody_iframe").load(function () {
//                alert('ok');
        });
    });
    function settimeshowdate()
    {
        var myDate = new Date();
        var time=myDate.getFullYear()+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate()+" "+myDate.toLocaleTimeString();
        $('.span_showdata').html(time);
    }
    settimeshowdate();//避免1秒延迟
    setInterval(settimeshowdate,1000);

</script>
</body>
</html>