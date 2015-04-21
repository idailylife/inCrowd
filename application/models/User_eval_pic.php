<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:02
 */

/**
 * Class User_eval_pic
 * 用于评估用户质量的单张图片
 * id
 * src:存储位置
 * real_answer:真实判定值，好或坏
 */
class User_eval_pic extends CI_Model {
    public $id;
    public $src;
    public $real_answer;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //TODO: implement of necessary interface
}
