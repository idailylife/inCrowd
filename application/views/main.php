<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<!--[if lt IE 10]>
<p style="color: #ffffff; background: red; font-size: 23px; text-align: center">抱歉，请使用IE10以上或其他非IE浏览器完成本次实验.</p>
<![endif]-->
<head>
    <meta charset="utf-8">
    <title>Crowd Crowd Crowd | Powered by inLab@ZJU</title>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/main.css">
    <script type="text/javascript">
        function reset_captcha(){
            $('#captcha_img').attr("src", "/inCrowd/VerifyCode?" + Math.random());
        }

        $(document).ready(function(){

            $('#captcha_img').click(function(){
                reset_captcha();
            });
            $('#get_in')
                .click(function(){
                var code_num = $('#veri_code').val();
                $.post("/inCrowd/VerifyCode", {captcha:code_num}, function(msg){
                    if(msg == '1'){
                        var url = '/inCrowd/assignment';
                        if($('#keep_cookie').prop('checked')){
                            url = url + '?keep_cookie=1';
                        }
                        <?php if (isset($invite_code)):?>
                        var inv_code = '<?php echo $invite_code?>';
                        var post_data = {invite_code:inv_code, use_invite:true};
                        $.post('/inCrowd/invitation', post_data, function(status){
                            window.console.log(status);
                            if(status != '0'){
                                alert("邀请码已失效. #" + status);
                                location.reload();
                            }
                        });
                        <?php endif;?>
                        if(navigator.userAgent.indexOf("Firefox") > 0){
                            alert('Firefox: 邀请码验证成功.');
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
            <?php if($cont_flag > 0):?>
            $('#verification').hide();
            $('#get_in').hide();
            $('#continue').show();
            <?php else:?>
            $('#continue').hide();
            <?php endif;?>

            $(document).keypress(function(e){
                if(e.which == 13){
                    $('#get_in').trigger('click'); //Trigger click function when Enter key pressed
                }
            })

            $('#detail_btn').click(function () {
                $('#pay_desc_detail').toggle("slow");
            });
        });

    </script>


</head>
<body>
    <div id="headers">
        <!--Logo and headers-->
        <p id="logo">inCrowd:众包设计作品评价平台（Public）</p>
    </div>
    <div id="container">
        <div id="terms_div" class="div_center">
            <!--Terms and declaration here-->

            <div id="terms_detail">
                <p>我们将会列出一些产品概念设计的作品(图),<b>您需要对作品进行两两比较,
                评价出在创新性或实用性上较为优秀的一个</b>. 大部分问题的答案我们自己也不明确,所以尽力选择就好. </p>

                <p><b>实验分组完成，约8-15分钟/组，每组15个单选题，每组完成后可选择是否继续</b>，我们会记录您的答题信息(包括您的点击信息、操作时间等).</p>
                <p style="color: deeppink">请注意:<br/>1.实验中出现的设计作品, 其版权归原作者所有, 请勿下载或挪作商用. <br/>
                    2.请勿使用自动化方法、随意点击完成实验或违反其他实验约定.<br/>
                    3.题目中将随机出现检测参与质量的问题，<span style="font-weight: 900">重复出现的问题请务必保证您的回答一致</span>.<br/>
                </p>

                <div id="payment_description">
                    此为公共测试，评价成绩以积分记录，不提供酬金.<br/>
                    完成一组(15题)实验后，系统根据参与度判断是否可进入下一组，分值比前一组多3%-100%!<br/>
                    <div id="detail_btn">详细说明</div>
                    <div id="pay_desc_detail">
                        <b>积分获得：</b>回答问题获得积分，每道题的得分=等级基础分×参与度，级别越高等级基数越大,初始参与度为100%<br/>
                        <b>参与度：</b><br/>
                            &nbsp;&nbsp;1.我们将使用陷阱题对您的参与认真程度进行检测，参与度低下将大幅影响您的得分(>50%)<br/>
                            &nbsp;&nbsp;2.我们采用经过专家评判过有预判答案的问题估计您的评价能力，这将轻微影响您每题的得分(+/-5%).
                        <br/>
                        <b>晋级条件：</b>倍率>0.75，最高可回答105题.<br/>
                        <b>等级基础分：</b>1-7级分别为25,35,43,50,55,58,60.
                    </div>
                </div>
                <p>真诚地感谢您的参与! 建议使用Chrome, Safari浏览器完成实验，IE10以下浏览器暂不支持</p>
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
                    <img id="captcha_img" src="/inCrowd/VerifyCode?rnd=<?php echo rand(0,1000) ?>">
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
            <div class="submit_btn" id="chk_payment">酬金支付查询</div>
        </div>
        <div class="div_center" style="font-size: 14px;color: #d3d3d3">
            Product of <a href="http://in.zju.edu.cn" target="_blank">inLab@ZJU</a>
            
        </div>

    </div>
</body>
</html>