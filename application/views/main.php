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
    <script type="text/javascript">
        function reset_captcha(){
            $('#captcha_img').attr("src", "/inCrowd/verifycode?" + Math.random());
        }

        $(document).ready(function(){
            $('#captcha_img').click(function(){
                reset_captcha();
            });
            $('#get_in').click(function(){
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
    </div>
    <div id="terms_div">
        <!--Terms and declaration here-->
        <p>Terms and conditions</p>
    </div>
    <div id="verification">
        <!--Verification form-->
        <input type="text" id="veri_code">
        <div id="verify">
            <img id="captcha_img" src="/inCrowd/verifycode?rnd=<?php echo rand(0,1000) ?>">
            <p id="get_in">Get in!</p>
        </div>
    </div>
</body>
</html>