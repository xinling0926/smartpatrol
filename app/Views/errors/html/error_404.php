<?php $base_url = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']) ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>404錯誤</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?= $base_url ?>/favicon.ico"/>

    <style type="text/css">
        body, code, dd, div, dl, dt, fieldset, form, h1, h2, h3, h4, h5, h6, input, legend, li, ol, p, pre, td, textarea, th, ul {
            margin: 0;
            padding: 0
        }

        body {
            font: 14px/1.5 'Microsoft YaHei', '微软雅黑', Helvetica, Sans-serif;
            background: #f0f1f3;
        }

        :focus {
            outline: 0
        }

        h1, h2, h3, h4, h5, h6, strong {
            font-weight: 700
        }

        a {
            color: #428bca;
            text-decoration: none
        }

        a:hover {
            text-decoration: underline
        }

        .error-page {
            background: #d2d6de;
        }

        .error-page-container {
            position: relative;
            z-index: 1
        }

        .error-page-main {
            position: relative;
            background: #f9f9f9;
            margin: 0 auto;
            width: 617px;
            -ms-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 50px 50px 70px
        }

        .error-page-main:before {
            content: '';
            display: block;
            background: url(<?= $base_url ?>/assets/img/errorPageBorder.png);
            height: 7px;
            position: absolute;
            top: -7px;
            width: 100%;
            left: 0
        }

        .error-page-main h3 {
            font-size: 24px;
            font-weight: 400;
            border-bottom: 1px solid #d0d0d0
        }

        .error-page-main h3 strong {
            font-size: 54px;
            font-weight: 400;
            margin-right: 20px
        }

        .error-page-main h4 {
            font-size: 20px;
            font-weight: 400;
            color: #333
        }

        .error-page-actions {
            font-size: 0;
            z-index: 100
        }

        .error-page-actions div {
            font-size: 14px;
            display: inline-block;
            padding: 30px 0 0 10px;
            width: 50%;
            -ms-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            color: #838383
        }

        .error-page-actions ol {
            list-style: decimal;
            padding-left: 20px
        }

        .error-page-actions li {
            line-height: 2.5em
        }

        .error-page-actions:before {
            content: '';
            display: block;
            position: absolute;
            z-index: -1;
            bottom: 17px;
            left: 50px;
            width: 200px;
            height: 10px;
            -moz-box-shadow: 4px 5px 31px 11px #999;
            -webkit-box-shadow: 4px 5px 31px 11px #999;
            box-shadow: 4px 5px 31px 11px #999;
            -moz-transform: rotate(-4deg);
            -webkit-transform: rotate(-4deg);
            -ms-transform: rotate(-4deg);
            -o-transform: rotate(-4deg);
            transform: rotate(-4deg)
        }

        .error-page-actions:after {
            content: '';
            display: block;
            position: absolute;
            z-index: -1;
            bottom: 17px;
            right: 50px;
            width: 200px;
            height: 10px;
            -moz-box-shadow: 4px 5px 31px 11px #999;
            -webkit-box-shadow: 4px 5px 31px 11px #999;
            box-shadow: 4px 5px 31px 11px #999;
            -moz-transform: rotate(4deg);
            -webkit-transform: rotate(4deg);
            -ms-transform: rotate(4deg);
            -o-transform: rotate(4deg);
            transform: rotate(4deg)
        }
        .shulianlog {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }
        .logo {
             font-size: 35px;
             text-align: center;
             margin-bottom: 25px;
             font-weight: 300;
        }

        .logo a{
            color: #444;
        }
    </style>
</head>
<body class="error-page">
<div style="margin: 7% auto;" class="error-page-container">
    <div class="logo">
        <a href="<?= $base_url ?>"><b>Smart</b> Patrol</a>
    </div>
    <div class="error-page-main">
        <h3>
            <strong>404</strong>無法打開頁面
        </h3>
        <div class="error-page-actions">
            <div>
                <h4>可能原因：</h4>
                <ol>
                    <li>網路連線中斷</li>
                    <li>找不到請求的頁面</li>
                    <li>輸入的網址不正確</li>
                </ol>
            </div>
            <div>
                <h4>可以嘗試：</h4>
                <ul>
                    <li><a href="<?= $base_url ?>/">返回首頁</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="shulianlog"><a style="display:none" href="http://www.fetnet.net" target="_blank">
        <img src='<?= $base_url ?>/assets/img/fareastone_logo.png' style="width:100px"></a></div>
</body>
</html>
