<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/20
 * Time: 19:55
 */
?>

<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title>Crowd Crowd Crowd | Test website</title>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
<!--    <link rel="stylesheet" href="--><?php //echo base_url()?><!--assets/jquery-ui.css">-->
<!--    <script type="text/javascript" src="--><?php //echo base_url()?><!--assets/jquery-ui.min.js"></script>-->
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
                .click(function(){
                var code_num = $('#veri_code').val();
                $.post("/inCrowd/verifycode", {captcha:code_num}, function(msg){
                    if(msg == '1'){
                        var url = '/inCrowd/assignment';
                        if($('#keep_cookie').prop('checked')){
                            url = url + '?keep_cookie=1';
                        }
                        window.location.href= url;
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
            <?php if($cont_flag):?>
            $('#verification').css('display', 'none');
            $('#get_in').css('display', 'none');
            <?php else:?>
            $('#continue').css('display', 'none');
            <?php endif;?>
        });

    </script>


</head>
<body>
    <div id="headers">
        <!--Logo and headers-->
        <p id="logo">Crowd? Crowd. Crowd!</p>
    </div>
    <div id="container">
        <div id="terms_div" class="div_center">
            <!--Terms and declaration here-->
            <p class="title">XXXXX问卷</p>
            <div id="terms_detail">
                该说点什么好呢。
            </div>

        </div>

        <div id="verification" class="div_center">
            <!--Verification form-->
            验证码
            <input type="text" id="veri_code">
            <div id="captcha_info">
                验证码错误，请重试:(
            </div>
            <div id="verify">
                <p>
                    <img id="captcha_img" src="/inCrowd/verifycode?rnd=<?php echo rand(0,1000) ?>">
                </p>
                <div id="cookie_div">
                    <label for="keep_cookie">存储答题状态，以便下次打开时能恢复</label>
                    <input type="checkbox" id="keep_cookie" name="keep_cookie" value="1">
                </div>

            </div>
        </div>

        <div id="submit_div" class="div_center">
                <div class="submit_btn" id="continue">继续先前任务</div>
                <div class="submit_btn" id="get_in">开始</div>
        </div>
    </div>

</body>
</html>