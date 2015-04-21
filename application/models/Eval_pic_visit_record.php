<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:15
 */

/**
 * Class Eval_pic_visit_record : 评价图片访问计数
 * id: 外键，连接至GeneralEvalPic的主键
 * count: 评价计数
 */
class Eval_pic_visit_record extends CI_Model {
    public $id;
    public $count;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //TODO: implement of necessary interface
}