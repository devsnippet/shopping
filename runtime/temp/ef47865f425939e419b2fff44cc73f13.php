<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"F:\server\github\shopping\public/../application/admin\view\main\login.html";i:1462529065;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <title>登录 - luffyzhao shopping</title>
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
    <!-- 可选的Bootstrap主题文件 -->
    <link rel="stylesheet" href="/bootstrap/css/bootstrap-luffyzhao.css">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap-luffyzhao-login.css">
    <link rel="stylesheet" href="/bootstrap/js/messenger/css/messenger.css">
    <link rel="stylesheet" href="/bootstrap/js/messenger/css/messenger-theme-air.css">   

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <div class="container-fluid">
        <div class="login">
            <!-- <div class="alert alert-danger" role="alert"></div> -->
            <div class="panel panel-default">
                <div class="panel-heading"><span class="text-muted">用户登录-User Login</span></div>
                <div class="panel-body">
                    <form class="inline-block" action="<?php echo url('admin/main/doLogin'); ?>" method="post" target="_submit">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-envelope"></i></span>
                                <input type="text" class="form-control" placeholder="email" name="email" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-key"></i></span>
                                <input class="span2 form-control" type="password" placeholder="Password">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-info  btn-block">登录</button>
                    </form>
                </div>
                <div class="panel-footer text-center">
                    <span class="text-muted ">版权所有 2016 luffyzhaoshopping.com</span>
                </div>
            </div>
        </div>
    </div>
    <iframe style="display: none;" name="_submit" id="_submit" frameborder="0"></iframe>
</body>
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script type="text/javascript" src="/bootstrap/js/jquery.min.js"></script>

<script type="text/javascript" src="/bootstrap/js/messenger/js/messenger.min.js"></script>

<script type="text/javascript" src="/bootstrap/js/global.js"></script>
</html>
