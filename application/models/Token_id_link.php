<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 13:49
 */

/**
 * Class Token_id_link : 用于存储token与id的连接关系
 * token_id
 * link_type: 链接的类型
 * link_id
 */
class Token_id_link extends CI_Model {
    public $token_id;
    public $link_type;
    public $link_id;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //TODO: implement of necessary interface
}