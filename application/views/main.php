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
            <p class="title">各位亲</p>
            <div id="terms_detail">
                <p>即将开始的实验类似于调查问卷,我们将会列出一些设计竞赛的作品(图),<b>您需要对作品进行两两比较,
                评价出在创新性或可用性上较为优秀的一个</b>. 大部分问题的答案我们自己也不明确,所以尽力选择就好. </p>

                <p><b>实验时间约5-15分钟/组，每组15个单选题</b>，一组实验结束后有机会<b>'再来一组'</b>。题目内容不定,由系统分配. 我们会记录您的答题信息(包括您的点击信息及操作时间等数据),
                所有评价任务结束后将会向您索取支付宝账号, 以便将报酬支付给您. </p>
                <p style="color: deeppink">实验中出现的设计作品, 其版权归原作者所有, 请勿下载或挪作商用. <br/>
                    请勿使用自动化方法或随意点击完成实验或违反其他实验约定,一经发现将<b>取消或降低支付报酬</b>.<br/>
                    <span style="color: #1b7e5a; font-weight: 700">题目中将随机出现检测参与质量的问题，请认真作答.</span>
                </p>
                <p style="font-weight: 700; color: red">报酬按人民币(5元/组×组数)支付,单次最大可做题量由系统根据参与度判定.每个支付宝账号只能参与最多2次实验,
                超过限定部分不计.</p>
                <p>使用相同的浏览器,<b>如需中途休息,直接关闭页面即可</b>,实验进度在关闭24小时内都可以恢复.</p>
                <p>真诚地感想您的参与!您在实验过程中填写的一切信息将完全保密,仅供研究使用.如有问题请邮件联系boweihe[at]zju.edu.cn</p>
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