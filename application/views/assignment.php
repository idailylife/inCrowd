<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/22
 * Time: 16:59
 */
?>

<html lang="zh" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <title>Assignment | Crowd Crowd Crowd | Test website</title>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/assignment.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/square/green.css">              <!--Radio button-->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery.elevateZoom-3.0.8.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/assignment.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/icheck.min.js"></script> <!--Radio button-->

    <script type="text/javascript">
        var start_time = null;
        var timer = null;
        var progress = '<?php echo $prog_current/$prog_total*100 ?>%';
        var last_q_type = <?php echo $q_type;?>;
        var resize_timer_id;
        var preload_counter = 0;

        $(document).ready(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '10%' // optional
            });


            $('#hint_mask').hide();
            $('#img_a').on('load', function(){
                switchLoadImg('a', false);
            });
            $('#img_b').on('load', function(){
                switchLoadImg('b', false);
            });
            $('#div_next').click(function () {
                post_to_server('#div_next', false);
            });
            $('#div_next_1').click(function () {
                post_to_server('#div_next_1', false);
            });
            $('#div_next_2').click(function () {
                post_to_server('#div_next_2', true);
            });
            $('#hint_button').click(function(){
                start_time = new Date().getTime();
                $('#hint_mask').hide();
            });
            //Set progress bar
            $('#meter_span').css('width', progress);
            //Set start time
            start_time = new Date().getTime();
            //Hide unavailable thing
            <?php if($q_type == 0):?>
            $('#cmp_usability').css('display', 'none');
            show_hint(0);
            <?php elseif($q_type == 1): ?>
            $('#cmp_creativity').css('display', 'none');
            show_hint(1);
            <?php else:?>
            alert('WTF');
            <?php endif;?>
            init_zoom();
            //Set timer
            resetTimer();
            timer = setInterval("tick_and_show();", 1000);
            //Set button style
            <?php if(($prog_current == $prog_total)
                && ($can_expand)):?>
            switch_double_button(true);
            <?php endif;?>

            $('#img_a').one('load', function(){
                console.log('img1 loaded.');
                switchLoadImg('a', false);
                set_image_margin();
                setZoomImage($(this), 1);
                preload_counter++;
                if(preload_counter < 2)
                    return;
                on_img_load(<?php echo '"'.$next_img_src1 . '","'. $next_img_src2 . '"'?>);

            }).each(function() {
                if(this.complete) $(this).load();
            });

            $('#img_b').one('load', function(){
                console.log('img2 loaded.');
                switchLoadImg('b', false);
                set_image_margin();
                setZoomImage($(this), 11);
                preload_counter++;
                if(preload_counter < 2)
                    return;
                on_img_load(<?php echo '"'.$next_img_src1 . '","'. $next_img_src2 . '"'?>);

            }).each(function() {
                if(this.complete) $(this).load();
            });
        });
        $(window).on('resize', function(){
            //Skip quick movement and wait till resize settles
            clearTimeout(resize_timer_id);
            resize_timer_id = setTimeout(set_image_margin, 500);
        });
        $(window).on('load', set_image_margin);
    </script>
</head>

<body>
<div id="hint_mask">
    <div id="hint_container" >
        <p style="font-size: 18px">即将开始对<span id="q_type_span">`创新性`</span>的评价</p>
        <p style="font-size: 12px; color: #d3d3d3">Tips: 鼠标移到图片上可以放大，缩放比例用鼠标滚轮调节.</p>
        <div id="hint_button">
            好的
        </div>
    </div>
    <div id="finish_hint_container">
        <p></p>
    </div>
</div>
<div id="timer" >
    <span id="time"></span>
    <!--Show time here-->
</div>
<div id="meter_top" class="meter container">
    <!--Progress bar here-->
    <span id="meter_span" style="width: 25%"></span>
</div>
<div id="progress" class="container">
        当前进度
        <span id="curr_index"><?php echo $prog_current ?></span>
        /
        <span id="total_index"><?php echo $prog_total; ?></span>
    <!--Time estimation-->
</div>
<div id="img_framework" class="container">
    <!--Image container-->
    <div id="img_container_a" class="comp_image_container">
        <div class="spinner" id="load_img_a">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
        <img id="img_a" class="comp_image" src="<?php echo $img_src1 ?>" data-zoom-image="<?php echo $img_src1 ?>">
    </div>
    <div id="img_container_b" class="comp_image_container">
        <div class="spinner" id="load_img_b">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
        <img id="img_b" class="comp_image" src="<?php echo $img_src2 ?>" data-zoom-image="<?php echo $img_src2 ?>">
    </div>
    </br>
    <span id="hint_p">点击图片可放大</span>
</div>

<div id="cmp_choices" class="container">
    <!-- Radio buttons here -->
    <div id="cmp_creativity" class="cmp_container">
        两幅作品中，
            <a class="tooltips" href="#" data-tooltip="以现有的思维模式提出有别于常规或常人思路的见解为导向，利用现有的知识和物质，
                在特定的环境中，本着理想化需要或为满足社会需求，而改进或创造新的事物、方法、元素、路径、
                环境，并能获得一定有益效果的行为。">
                创新性
            </a>
            较强的是？
        <input type="radio" name="creativity" class="radio" value="A" id="cr_a">
        <label for="cr_a" class="cmp_label"><b>左图</b></label>
        <input type="radio" name="creativity" class="radio" value="B" id="cr_b">
        <label for="cr_b" class="cmp_label"><b>右图</b></label>
        <input type="radio" name="creativity" class="radio" value="X" id="cr_x">
        <label for="cr_x" class="cmp_label">难以判断</label>
    </div>
    <div id="cmp_usability" class="cmp_container">
        两幅作品中，
            <a class="tooltips" href="#" data-tooltip="该产品能够制造或者使用，并且能够产生积极效果。">
                实用性
            </a>
            较强的是？
        <input type="radio" name="usability" class="radio" value="A" id="us_a">
        <label for="us_a" class="cmp_label"><b>左图</b></label>
        <input type="radio" name="usability" class="radio" value="B" id="us_b">
        <label for="us_b" class="cmp_label"><b>右图</b></label>
        <input type="radio" name="usability" class="radio" value="X" id="us_x">
        <label for="us_x" class="cmp_label">难以判断</label>
    </div>
</div>

<div id="div_next" class="container meter">
    继续
</div>
<div id="button_set" class="container">
    <div id="div_next_1" class="meter double_btn">完成</div>
    <div id="div_next_2" class="meter double_btn">再来一组</div>
</div>

<script src="http://s95.cnzz.com/z_stat.php?id=1254983938&web_id=1254983938" language="JavaScript"></script>
</body>
</html>