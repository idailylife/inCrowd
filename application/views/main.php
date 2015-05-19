<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<!--[if lt IE 10]>
<p style="color: #ffffff; background: red; font-size: 23px; text-align: center">抱歉，请使用IE10以上或其他非IE浏览器完成本次实验.</p>
<![endif]-->
<head>
    <meta charset="utf-8">
    <title>Crowd Crowd Crowd | Test website</title>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
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
            $('#chk_payment').click(function(){
                window.open('/inCrowd/chkpayment', '_blank');
            });
            <?php if($cont_flag):?>
            $('#verification').css('display', 'none');
            $('#get_in').css('display', 'none');
            <?php else:?>
            $('#continue').css('display', 'none');
            <?php endif;?>

            $(document).keypress(function(e){
                if(e.which == 13){
                    $('#get_in').trigger('click'); //Trigger click function when Enter key pressed
                }
            })

            $('#detail_btn').click(function () {
                $('#pay_desc_detail').toggle();
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
        <div id="terms_div" class="div_center">
            <!--Terms and declaration here-->

            <div id="terms_detail">
                <p>真诚地感想您的参与!即将开始的实验类似于调查问卷,我们将会列出一些设计竞赛的作品(图),<b>您需要对作品进行两两比较,
                评价出在创新性或可用性上较为优秀的一个</b>. 大部分问题的答案我们自己也不明确,所以尽力选择就好. </p>

                <p><b>实验时间约5-15分钟/组，每组15个单选题</b>，我们会记录您的答题信息(包括您的点击信息及操作时间等数据), 任务完成后将会向您索取支付宝账号, 以便将报酬支付. </p>

                <p style="color: deeppink">请注意:<br/>1.实验中出现的设计作品, 其版权归原作者所有, 请勿下载或挪作商用. <br/>
                    2.请勿使用自动化方法或随意点击完成实验或违反其他实验约定,一经发现将<b>取消或降低支付报酬</b>.<br/>
                    3.题目中将随机出现检测参与质量的问题，请认真作答.
                </p>

                <div id="payment_description">
                    实验酬劳根据实验获得的总得分支付，100分=1元人民币.<br/>
                    完成一组(15题)实验后，系统根据参与度判断是否可进入下一组，分值比前一组多3%-100%!<br/>
                    <b>预实验数据表明，通常情况下15分钟内可以获得10元报酬.</b>
                    <div id="detail_btn">...</div>
                    <div id="pay_desc_detail">
                        <b>积分获得：</b>回答问题获得积分，每道题的得分=等级基数×倍率，级别越高等级基数越大,初始倍率为100%<br/>
                        <b>倍率改变：</b>我们采用经过专家评判过有预判答案的问题估计您的评价能力，这将轻微影响您每题的得分(5%左右).
                        系统将实时判断您的参与度，参与度低下(如:胡乱填写)的用户倍率将减至原有的60%/次.<br/>
                        <b>晋级条件：</b>倍率>0.5，最高可回答105题.</br>
                        <b>等级基数：</b>1-7级分别为20,40,50,55,60,63,65.
                    </div>
                </div>

                <p>使用相同的浏览器,实验进度在24小时内都可以恢复.
                您在实验过程中填写的一切信息将完全保密,仅供研究使用.如有问题请邮件联系boweihe[at]zju.edu.cn</p>
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
                <div id="cookie_div" style="display: none">
                    <label for="keep_cookie">存储答题状态，以便意外关闭时能恢复</label>
                    <input type="checkbox" id="keep_cookie" name="keep_cookie" value="1" checked="checked">
                </div>

            </div>
        </div>

        <div id="submit_div" class="div_center">
                <div class="submit_btn" id="continue">继续先前任务</div>
                <div class="submit_btn" id="get_in">开始</div>
        </div>
        <div class="div_center">
            <div class="submit_btn" id="chk_payment">报酬支付查询</div>
        </div>
    </div>
    <script src="http://s95.cnzz.com/z_stat.php?id=1254983938&web_id=1254983938" language="JavaScript"></script>
</body>
</html>