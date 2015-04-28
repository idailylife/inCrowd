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
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/assignment.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/jquery.fancybox.css">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/assignment.js"></script>
    <script type="text/javascript">
        var start_time = null;

        $(document).ready(function () {
            $('.fancybox').fancybox({
                padding: 0
            });
            $('#div_next').click(function () {
                if(!check_validity()){
                    $('#div_next').text('请填写完整后重试');
                    return;
                } else {
                    $('#div_next').text('继续');
                }
                var val_c = $("input[name='creativity']:checked").val();
                var val_u = $("input[name='usability']:checked").val();
                var duration = new Date().getTime() - start_time;
                $.post("assignment", {
                        'creativity': val_c,
                        'usability': val_u,
                        'duration' : duration
                    },
                    post_callback
                );
            });
            //Set progress bar
            var progress = '<?php echo $prog_current/$prog_total*100 ?>%';
            $('#meter_span').css('width', progress);
            //Set start time
            start_time = new Date().getTime();
        });

    </script>
</head>

<body>
<div class="meter container">
    <!--Progress bar here-->
    <span id="meter_span" style="width: 25%"></span>
</div>
<div id="progress" class="container">
    <p>
        当前进度
        <span id="curr_index"><?php echo $prog_current ?></span>
        /
        <span id="total_index"><?php echo $prog_total; ?></span>
        .
    </p>
    <!--Time estimation-->
</div>
<div id="img_framework" class="container">
    <!--Image container-->
    <div class="comp_image_container">
        <a id="img_a_a" class="fancybox" rel="group" href="<?php echo $img_src1 ?>">
            <img id="img_a" class="comp_image" src="<?php echo $img_thumb1 ?>">
        </a>
    </div>
    <div class="comp_image_container">
        <a id="img_b_a" class="fancybox" rel="group" href="<?php echo $img_src2 ?>">
            <img id="img_b" class="comp_image" src="<?php echo $img_thumb2 ?>">
        </a>
    </div>
</div>

<div id="cmp_choices" class="container">
    <!-- Radio buttons here -->
    <p>您认为两幅作品中，
        <a class="tooltips" href="#">
            创新性
            <span>创新性是指...</span>
        </a>
        较好的是？
    </p>
    <input type="radio" name="creativity" class="radio" value="A" id="cr_a">
    <label for="cr_a"><span><span></span></span><b>左图</b></label>
    <input type="radio" name="creativity" class="radio" value="B" id="cr_b">
    <label for="cr_b"><span><span></span></span><b>右图</b></label>
    <p>您认为两幅作品中，
        <a class="tooltips" href="#">
            实用性
            <span>实用性是指...</span>
        </a>
        较好的是？
    </p>
    <input type="radio" name="usability" class="radio" value="A" id="us_a">
    <label for="us_a"><span><span></span></span><b>左图</b></label>
    <input type="radio" name="usability" class="radio" value="B" id="us_b">
    <label for="us_b"><span><span></span></span><b>右图</b></label>
</div>

<div id="div_next" class="container meter">
    继续
</div>

</body>
</html>