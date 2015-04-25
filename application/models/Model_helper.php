<?php
/**
 * 共有定义以及辅助函数
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/23
 * Time: 17:01
 */

define('CMP_TYPE_GENERAL', 0);
define('CMP_TYPE_USERTEST', 1);
define('IMAGE_BASE_URL', '../');       //Base url of comparison images
define('PATH_TO_RESOURCES', 'd:/wamp/www/'); //Base path of resources

class Model_helper extends CI_Model {

    /**
     * 获取某一数据表的auto_increment值
     * @param $table_name
     * @return int
     */
    public function get_auto_increment_value($table_name){
        $this->load->database();
        $this->db->select_max('id');
        $query = $this->db->get($table_name);
        $count = $query->row_array()['MAX(id)'];
        return $count;
    }
}