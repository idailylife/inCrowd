<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title>Crowd Crowd Crowd | Check Payment </title>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
    <link rel="stylesheet" href="<?php echo base_url()?>assets/checkpayment.css">
    <script type="text/javascript">
        function reset_captcha(){
            $('#captcha_img').attr("src", "/inCrowd/verifycode?" + Math.random());
            $('#veri_code').val("");
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
            var html = '';
            var query_data = jQuery.parseJSON(data);


            switch (query_data.status){
                case -1:
                    //Auth failed
                    alert("验证码错误，请重试");
                    break;
                case -2:
                    alert("意外错误");
                    break;
                case 0:
                    var dataAry = jQuery.parseJSON(query_data.data);
                    if(dataAry.length == 0){
                        html = '<span>无查询结果.</span>'
                    } else {
                        html = '<table>\n' +
                            '<tr><th>任务时间</th><th>支付状态</th><th>实际支付</th></tr>';
                        var row;
                        for(var i=0; i<dataAry.length; i++){
                            html += '<tr><td>';
                            row = dataAry[i];
                            var d = new Date();
                            d.setTime(new Number(row[0]).valueOf() * 1000);
                            html += d.toLocaleDateString() + '</td><td>';
                            var ps='意外错误';
                            switch (new Number(row[1]).valueOf()){
                                case -2:
                                    ps = '审核失败';
                                    break;
                                case -1:
                                    ps = '任务未完成';
                                    break;
                                case 0:
                                    ps = '任务完成-审核中';
                                    break;
                                case 1:
                                    ps = '待支付';
                                    break;
                                case 2:
                                    ps = '支付完成';
                                    break;
                            }
                            html += ps + '</td><td>';
                            html += row[2] + '</td></tr>';
                        }
                        html += '</table>';
                    }
                    console.log(html);
                    $('#query_result').html(html);
                    break;
                default :
                    alert("网络连接中断，请重试");
            }

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
                            $('#chk_payment').text('查询');
                        } else {
                            reset_captcha();
                            $('#veri_code').val("");
                            $('#chk_payment').text('重试');
                        }
                    });
                });
            $('#go_back').click(function () {
               window.location.href = '/inCrowd';
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
        <div class="submit_btn" id="go_back">返回</div>
    </div>
    <div id="query_result" class="div_center">

    </div>
</div>
<script src="http://s95.cnzz.com/z_stat.php?id=1254983938&web_id=1254983938" language="JavaScript"></script>
</body>
</html>