<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Noah运营后台系统</title>
    <!-- Tell the browser to be responsive to screen width -->
    <!--<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <link href="/assets/plugins/toastr/toastr.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="/assets/css/jquery.fancybox.css">
    <link rel="stylesheet" href="/assets/css/public.css">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
    @yield("pagecss")
            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/assets/js/html5shiv.min.js"></script>
    <script src="/assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="">

@yield("content")

        <!-- ./wrapper -->
<!-- jQuery 2.1.4 -->
<script src="/assets/js/jQuery/jQuery-1.9.1.min.js"></script>
<script src="/assets/plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="/assets/js/bootstrap.min.js"></script>
<!-- Slimscroll -->
<script src="/assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/assets/plugins/fastclick/fastclick.js?v=1"></script>
<!-- boobox -->
<script src="/assets/plugins/bootbox/bootbox.js"></script>
<!-- AdminLTE App -->
<script src="/assets/js/app.min.js"></script>
<script src="/assets/plugins/toastr/toastr.min.js"></script>
<script src="/assets/js/jquery.fancybox.pack.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="/assets/js/demo.js"></script>
<script src="/assets/js/public.js"></script>
<script src="/assets/js/xin_common.js?v=2"></script>
@yield("pagejs")
@yield("footerjs")
</body>
</html>