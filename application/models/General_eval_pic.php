<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:08
 */

/**
 * Class General_eval_pic 需要评价的单张图片
 *   id
 *   src
 */
class General_eval_pic extends CI_Model {
    public $id;
    public $src;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //TODO: implement of necessary interface
}