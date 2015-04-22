<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/20
 * Time: 19:55
 */
?>

<html lang="zh" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Crowd Crowd Crowd | Test website</title>
    <script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/jquery-ui.css">
    <script type="text/javascript" src="<?php echo base_url()?>assets/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/main.css">
    <script type="text/javascript">
        function reset_captcha(){
            $('#captcha_img').attr("src", "/inCrowd/verifycode?" + Math.random());
        }

        $(document).ready(function(){
            $('#captcha_img').click(function(){
                reset_captcha();
            });
            $('#get_in')
                .button()
                .click(function(){
                var code_num = $('#veri_code').val();
                $.post("/inCrowd/verifycode", {captcha:code_num}, function(msg){
                    if(msg == '1'){
                        alert('DONE');
                        window.location.href= '/inCrowd/assignment';
                    } else {
                        alert('FAIL');
                        reset_captcha();
                        $('#veri_code').val("");
                    }
                });
            });
        });

    </script>


</head>
<body>
    <div id="headers">
        <!--Logo and headers-->
        <p id="logo">Crowd? Crowd. Crowd!</p>
        <div role="navigation">
            <!--Leave for further use-->
        </div>
    </div>
    <div id="container">
        <div id="terms_div" class="div_center">
            <!--Terms and declaration here-->
            <p class="title">条款及须知</p>
            <p>此山是我开，此树是我栽。要想过此路，留下买路财。</p>
        </div>
        <div id="verification" class="div_center">
            <!--Verification form-->
            <p>麻烦输入验证码</p>

            <input type="text" id="veri_code">
            <div id="verify">
                <p>
                    <img id="captcha_img" src="/inCrowd/verifycode?rnd=<?php echo rand(0,1000) ?>">
                </p>
                <button id="get_in">Get in!</button>
            </div>
        </div>
    </div>

</body>
</html>