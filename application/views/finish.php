<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/26
 * Time: 20:02
 */
?>
<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title>Information Gathering | Crowd Crowd Crowd</title>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/finish.css">
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery-1.11.2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#btn_submit').click(function(){
                var validity = check_payment_info()
                    && check_form_blank();
                if(validity){
                    $.post("finish", {
                        'payment_info': $('#payment').val(),
                        'expert_info': $("input[name='expertise']:checked").val(),
                        'advice': $('#advice').val()
                    }, submit_callback);
                } else {
                    $('#btn_submit').text('请填写完整后重试');
                }
            });
            $('#payment_re').blur(function(){
                check_payment_info();
            });
            $('#payment').blur(function(){
                if($('#payment').val() != '')
                    check_payment_info();
            });
        });
        function show_submit_failure(){
            $('#submit_info').css('display', 'inline');
        }

        function submit_callback(data, status){
            console.log('post_callback:' + status);
            console.log(data);
            if(status != 'success') {
                console.log('post_callback: Connection failed.');
                show_submit_failure();
            } else {
                var jsonval = jQuery.parseJSON(data);
                switch (jsonval.status){
                    case 2:
                    case 1:
                        console.log('Remote error: ' + jsonval.message);
                        break;
                    case 0:
                        //success
                        $('#info_input').css('display', 'none');
                        $('#info_result').css('display', 'inline');
                        break;
                    default:
                        alert('wtf');
                }
            }
        }
        function check_payment_info(){
            //检查两次输入的内容是否一致
            var info = $('#payment').val();
            var info1= $('#payment_re').val();
            if(info == info1){
                $('#verify_info').css('display', 'none');
                $('#payment_re').css('border', '2px solid #cfe3dc');
                $('#payment').css('border', '2px solid #cfe3dc');
                return true;
            } else {
                $('#verify_info').css('display', 'inline');
                $('#payment_re').css('border', '2px solid #E34B57');
                $('#payment').css('border', '2px solid #E34B57');
                return false;
            }
        }

        function check_form_blank(){
            var info = $('#payment').val();
            if(info == ""){
                return false;
            } else {
                if($("input[name='expertise']:checked").val() == undefined){
                    console.log('undefined value detected');
                    return false;
                } else {
                    return true;
                }
            }

        }
    </script>
</head>
<body>
    <div id="title">
        <h1>YOU MADE IT!</h1>
    </div>
    <div id="info_input" class="container">
        <p>恭喜，任务已完成.</p>
        <p>您的支付宝账号(邮箱或手机号)<br/>
            <input type="text" id="payment" name="payment"/>
        </p>
        <p>烦请再输入一遍以确认<br/>
            <input type="text" id="payment_re" name="payment_re"/>
        </p>
        <p id="verify_info">:(两次输入的内容不一致.</p>
        <p>
            请问您是否从事设计相关工作, 或设计相关专业在读学生?
        <br/>
        <input type="radio" name="expertise" value="1" id="rad_exp1">
        <label for="rad_exp1"><span><span></span></span>是</label>
        <input type="radio" name="expertise" value="0" id="rad_exp2">
        <label for="rad_exp2"><span><span></span></span>否</label>
        </p>
        <p>(可选)烦请留下宝贵意见
            <br/>
            <textarea id="advice" rows="3" placeholder=""></textarea>
        </p>
        <div id="btn_submit">提交</div>
        <p id="submit_info">熬~连接失败，烦请再试一次。</p>
    </div>
    <div id="info_result" class="container">
        <h4>提交成功！再次感谢您的参与！</h4>
    </div>
</body>
</html>