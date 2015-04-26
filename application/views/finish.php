<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/26
 * Time: 20:02
 */
?>
<html lang="zh" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Information Gathering | Crowd Crowd Crowd</title>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/finish.css">
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#btn_submit').click(function(){
                var validity = check_payment_info();
                alert("" + validity);
            });
        });
        function check_payment_info(){
            //检查两次输入的内容是否一致
            var info = $('#payment').val();
            var info1= $('#payment_re').val();
            if(info == info1){
                $('#verify_info').css('display', 'none');
                return true;
            } else {
                $('#verify_info').css('display', 'inline');
                return false;
            }
        }
    </script>
</head>
<body>
    <h1>Holy crap, you made it!</h1>
    <p>向您支付的报酬有一个最低标准，且参照您先前答题的能力向上浮动.</p>
    <p>麻烦提供您的支付宝账号(邮箱或手机号)
        <input type="text" id="payment" name="payment"/>
    </p>
    <p>烦请再输入一遍以确认
        <input type="text" id="payment_re" name="payment_re"/>
    </p>
    <p id="verify_info">:(两次输入的内容不一致.</p>
    <input type="button" id="btn_submit" value="提交"/>
</body>
</html>