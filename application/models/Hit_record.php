<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:23
 */
require_once('Compare_record.php');
require_once('public_defination.php');
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

    /**
     * 初始化模型，初始化空的问题对
     * @param $comparison_size  总的比较对的个数
     * @param $test_cmp_size    用户质量控制用比较对的个数
     */
    public function init($comparison_size, $test_cmp_size) {
        $this->records = [];
        for($i=0; $i<$comparison_size; $i++) {
            $cmp = new Compare_record(); //需要用Load方法加载已有的图片

            if($i < $test_cmp_size) {
                //设定为用户测试对
                $cmp->comp_type = CMP_TYPE_USERTEST;
            } else {
                $cmp->comp_type = CMP_TYPE_GENERAL;
            }
            array_push($this->records, $cmp);
        }
        //两类问题随机一下
        shuffle($this->records);
    }



}