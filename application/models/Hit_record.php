<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:23
 */

/**
 * Class Hit_record : 一次HIT任务的记录
 * id
 * start_time: 开始时间
 * end_time: 结束时间
 * records: {比较记录的id}
 * progress_count: 当前答题进度
 * user_ip: 用户提交时的ip地址
 * payment_info: 支付信息
 * expert_info: 预实验用的专业程度信息
 */
class Hit_record extends CI_Model {
    public $id;
    public $start_time;
    public $end_time;
    public $records;
    public $progress_count;
    public $user_ip;
    public $payment_info;
    public $expert_info;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    //TODO: implement of necessary interface
}