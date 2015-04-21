<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:22
 */

/**
 * Class Compare_record : 成对比较的记录
 * id
 * comp_id1
 * comp_id2
 * answer:比较结果
 * duration:完成比较的时间
 *
 */
class Compare_record extends CI_Model {
    public $id;
    public $comp_id1;
    public $comp_id2;
    public $answer;
    public $duration;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //TODO: implement of necessary interface
}