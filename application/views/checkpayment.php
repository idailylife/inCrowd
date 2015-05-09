<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title>Crowd Crowd Crowd | Check Payment </title>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/checkpayment.css">
    <script type="text/javascript">
        function reset_captcha(){
            $('#captcha_img').attr("src", "/inCrowd/verifycode?" + Math.random());
        }

        function check_payment(){
            var url = '/inCrowd/chkpayment';
            var postData = {
                'payment_info' : $('#payment_info').val()
            };
            $.post(url, postData, post_callback);
        }

        function post_callback(data, ret_status){
            console.log('post_callback:' + ret_status);
            console.log(data);

            reset_captcha();
        }

        $(document).ready(function(){
            $('#captcha_img').click(function(){
                reset_captcha();
            });
            $('#chk_payment')
                .click(function(){
                    var code_num = $('#veri_code').val();
                    $.post("/inCrowd/verifycode", {captcha:code_num}, function(msg){
                        if(msg == '1'){
                            check_payment();
                        } else {
                            reset_captcha();
                            $('#veri_code').val("");
                            $('#get_in').text('重试');
                        }
                    });
                });
            $('#continue').click(function(){
                window.location.href= '/inCrowd/assignment';
            });

        });

    </script>


</head>
<body>
<div id="headers">
    <!--Logo and headers-->
    <p id="logo">基于众包的设计竞赛评价研究</p>
</div>
<div id="container">
    <div id="pm_title" class="div_center">
        报酬支付查询
    </div>
    <p class="div_center">
        支付宝账号
        <input type="text" id="payment_info">
    </p>

    <div id="verification" class="div_center">
        <!--Verification form-->
        验证码
        <input type="text" id="veri_code">
        <div id="captcha_info">
            验证码错误，请重试:(
        </div>
        <div id="verify">
            <img id="captcha_img" src="/inCrowd/verifycode?rnd=<?php echo rand(0,1000) ?>">
        </div>
    </div>

    <div class="div_center">
        <div class="submit_btn" id="chk_payment">查询</div>
    </div>
    <div class="query_result">

    </div>
</div>

</body>
</html>