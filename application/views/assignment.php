<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/22
 * Time: 16:59
 */
?>

<html lang="zh" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Assignment | Crowd Crowd Crowd | Test website</title>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/assignment.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/jquery.fancybox.css">
    <script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery.mousewheel-3.0.6.pack.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/assignment.js"></script>
    <script type="text/javascript">
        var img_load_count = 0;
        $(document).ready(function(){
            $('.fancybox').fancybox({
                padding : 0
            });
            $('#img_a').load(function() {
                img_load_count = img_load_count + 1;
                resize_image(img_load_count);
            });
            $('#img_b').load(function() {
                img_load_count = img_load_count + 1;
                resize_image(img_load_count);
            });
        });
    </script>


</head>
<body>
    <div id="img_framework">
        <!--Image container-->
        <div  class="comp_image_container">
            <a class="fancybox" rel="group" href="<?php echo $img_src1?>">
                <img id="img_a" class="comp_image" src="<?php echo $img_thumb1?>">
            </a>
        </div>
        <div  class="comp_image_container">
            <a class="fancybox" rel="group" href="<?php echo $img_src2?>">
                <img id="img_a" class="comp_image" src="<?php echo $img_thumb2?>">
            </a>
        </div>
    </div>
    <div id="cmp_choices">
        <!-- Radio buttons here -->
        <p>就<b>创新性</b>而言，您认为两幅作品中，较好的是？</p>
        <input type="radio" name="creativity" value="A"> A
        <input type="radio" name="creativity" value="B"> B
        <p>就<b>实用性</b>而言，您认为两幅作品中，较好的是？</p>
        <input type="radio" name="usability" value="A"> A
        <input type="radio" name="usability" value="B"> B
    </div>
    <div id="progress">
        <!--Progress bar here-->
        <p>进度<?php echo $prog_current.'/'.$prog_total?>.</p>
    </div>
    <div id="div_next">
        <p>继续</p>
    </div>
</body>
</html>