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
                var validity = check_payment_info();
                if(validity){
                    $.post("finish", {
                        'payment_info': $('#payment').val(),
                        'expert_info': $("input[name='expertise']:checked").val()
                    }, submit_callback);
                }
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
    <div id="info_input">
        <p>向您支付的报酬有一个最低标准且参照您先前答题的能力向上浮动.</p>
        <p>麻烦提供您的支付宝账号(邮箱或手机号)
            <input type="text" id="payment" name="payment"/>
        </p>
        <p>烦请再输入一遍以确认
            <input type="text" id="payment_re" name="payment_re"/>
        </p>
        <p id="verify_info">:(两次输入的内容不一致.</p>
        <p>
            请问您是否从事设计相关工作, 或设计相关专业在读学生?(此项仅用于算法验证)
            <input type="radio" name="expertise" value="1">是
            <input type="radio" name="expertise" value="0">否
        </p>
        <input type="button" id="btn_submit" value="提交"/>
        <p id="submit_info">熬~连接失败，烦请再试一次。</p>
    </div>
    <div id="info_result">
        <h4>提交成功.</h4>
    </div>
</body>
</html>