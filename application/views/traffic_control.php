<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <title>名额已满 | Crowd Crowd Crowd</title>
    <style type="text/css">

        ::selection { background-color: #E13300; color: white; }
        ::-moz-selection { background-color: #E13300; color: white; }

        body {
            background-color: #fff;
            margin: 40px;
            font-family: "微软雅黑 Light", "微软雅黑", "黑体";
            color: #4F5155;
        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
            text-decoration: none;
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 14px 15px 10px 15px;
            font-family: "微软雅黑", "黑体";
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
        }

        #container {
            margin: 10px;
            border: 1px solid #D0D0D0;
            box-shadow: 0 0 8px #D0D0D0;
            text-align: center;
        }

        #payment_check a{
            text-decoration: none;
            color: whitesmoke;
            background-color: #444;
            padding: 3px;
            border-radius: 3px;
            border: 0px;
        }

        #copyright{
            text-align: center;
            font-size: 12px;
        }

        p {
            margin: 12px 15px 12px 15px;
        }
    </style>
</head>
<body>
<div id="container">
    <h1><?php echo $heading; ?></h1>
    <p><?php echo $message; ?></p>
    <p style="display: none">我有邀请码:
        <input type="text" name="invite_code">
        <a href="#">验证</a>
    </p>
    <p id="payment_check"><a href="/inCrowd/chkpayment">酬金支付查询</a></p>
</div>
<div id="copyright">
    All rights reserved. 2015 <a href="http://in.zju.edu.cn" target="_blank">inLab@ZJU</a>
    <p>如有问题请邮件boweihe[at]zju.edu.cn</p>
</div>
</body>
</html>