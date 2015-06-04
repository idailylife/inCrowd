<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 15:16
 */

/**
 * Class VerifyCode
 * Handles verify code in the main page
 */
class Verifycode extends CI_Controller {
    public $font;

    public function __construct(){
        parent::__construct();
        $this->font = FONT_PATH;//dirname(__FILE__).'/../../fonts/courbd.ttf';
    }

    public function test_dir(){
        echo FONT_PATH;
        //echo dirname(__FILE__).'/../../fonts/courbd.ttf';
    }

    public function index(){
        if(!isset($_SERVER['REQUEST_METHOD'])){
            show_error("REQUEST_METHOD not set.");
        }

        $this->load->library('session'); //Load session library

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->index_post();
        } else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->index_get();
        } else {
            show_error("REQUEST_METHOD not legal");
        }
    }

    /**
     * 判断验证码是否正确，如果正确则存储在session中一个变量'pass'，并返回1
     * 否则返回0, 并且删除session中的'captcha'项，使之需要强制刷新
     */
    public function index_post(){
        unset($_SESSION[KEY_PASS]);

        if(!isset($_POST['captcha']) ||
            !isset($_SESSION['captcha'])){
            echo '0';
            return;
        }
        $code_submit = $_POST['captcha'];
        $code_session = $_SESSION['captcha'];
        if($code_session == $code_submit){
            echo '1';
            $_SESSION[KEY_PASS] = '1';
            $_SESSION['captcha'] = '0';
        } else {
            echo '0';
            unset($_SESSION['captcha']);
        }
    }

    public function test(){
        header("Content-type:image/png");
        $img_width = 100;
        $img_height = 36;
        $image = imagecreate($img_width, $img_height);
        $background = imagecolorallocate($image, 248, 248, 248); //#f8f8f8
        imagefill($image, 0, 0, $background);
        imagepng($image);
    }

    public function index_get() {
        if(!file_exists(FONT_PATH)){
            show_error('Error generating verifycode: font file does not exist\n'. FONT_PATH);
            return;
        }

        //Session过期问题?

        $img_width = 100;
        $img_height = 36;
        $code = "";
        for($i = 0; $i< 5; $i++) {
            $code .= rand(0,9);
        }

        $this->session->set_userdata('captcha', $code); //Write to session

        header("Content-type:image/png");
        $image = imagecreate($img_width, $img_height);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 200, 200, 200);
        $background = imagecolorallocate($image, 248, 248, 248); //#f8f8f8


        //Fill background to gray
        imagefill($image, 0, 0, $background);
        //Frame
        imagerectangle($image, 0, 0, $img_width-1, $img_height-1, $gray);

        //随机绘制两条虚线，起干扰作用
        $style = array($black, $black, $black, $black, $black,
            $gray, $gray, $gray, $gray, $gray);
        imagesetstyle($image, $style);

        $y1 = rand(0, $img_height);
        $y2 = rand(0, $img_height);
        $y3 = rand(0, $img_height);
        $y4 = rand(0, $img_height);
        imageline($image, 0, $y1, $img_width, $y3, IMG_COLOR_STYLED);
        imageline($image, 0, $y2, $img_width, $y4, IMG_COLOR_STYLED);

        //在画布上随机生成大量黑点，起干扰作用;
        for($i=0; $i<200; $i++) {
            $randcolor = imagecolorallocate($image, rand(0,100), rand(0,100), rand(0,100));
            imagesetpixel($image, rand(0,$img_width),
                rand(0, $img_height), $randcolor);
        }

        //将数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成
        $strx = rand(5, 10);
        for($i=0; $i< 5; $i++) {
            $strpos = rand(20, 35);
            $fontcolor = imagecolorallocate($image, rand(0, 128), rand(0, 128), rand(0, 128));
            imagettftext($image, rand(18, 24), rand(360,390), $strx, $strpos, $fontcolor, $this->font,
                substr($code, $i, 1));
            $strx += rand(16, 22);
        }
        imagepng($image);
        imagedestroy($image);
    }
}