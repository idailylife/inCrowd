<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:23
 */
require_once('Compare_record.php');
require_once('Model_helper.php');
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
    const TABLE_NAME = 'hit_record';

    public $id;
    public $start_time;
    public $end_time;
    public $records;         //存放Compare_record对象
    public $record_id_array; //存放Compare_record对象的ID (从数据库存取需要json_encode/decode)
    public $progress_count;
    public $user_ip;
    public $payment_info;
    public $expert_info;

    private $model_generated;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->model_generated = false;
        $this->progress_count = 0;
    }

    /**
     * 初始化模型，初始化【空】的问题对
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

    /**
     * 填充HIT任务中具体的比较对数据
     * 并且会更新$record_id_array
     */
    public function generate_comparison() {
        $this->record_id_array = [];
        foreach($this->records as $record) {
            //遍历数组，填充数据
            $record->generate_record();
            $record_id = $record->push_to_db();
            array_push($this->record_id_array, $record_id);
        }
        $this->model_generated = true;
    }

    /**
     * 将当前时间记录为起始时间
     * 【不会】更新数据库
     * @param: $is_starttime true:起始时间，false:结束时间
     */
    public function mark_time($is_starttime) {
        $timestamp = time();
        if($is_starttime)
            $this->start_time = $timestamp;
        else
            $this->end_time = $timestamp;
    }

    /**
     * 将当前数据插入数据库（新增条目）
     * @return int|void
     */
    public function push_to_db(){
        if(!$this->model_generated){
            //Model not generated yet.
            show_error('Hit_record: Unable to insert db record, generate comparison models first.');
            return;
        }
        $data = array(
            'records' => json_encode($this->record_id_array)
        );
        if(isset($this->start_time))
            $data['start_time'] = $this->start_time;
        if(isset($this->user_ip))
            $data['user_ip'] = $this->user_ip;

        $this->db->insert(Hit_record::TABLE_NAME, $data);
        //$db_helper = new Model_helper();
        $count = $this->get_max_id();//$db_helper->get_auto_increment_value(Hit_record::TABLE_NAME);
        return $count;
    }

    public function get_max_id(){
        $maxid = 0;
        $row = $this->db->query('SELECT MAX(id) AS `maxid` FROM `'. Hit_record::TABLE_NAME.'`')->row();
        if ($row) {
            $maxid = $row->maxid;
        }
        return $maxid;
    }

    public function update_db($key_array){
        foreach($key_array as $item){
            if($item == 'start_time')
                $this->db->set('start_time', $this->start_time);
            else if($item == 'end_time')
                $this->db->set('end_time', $this->end_time);
            else if($item == 'progress_count')
                $this->db->set('progress_count', $this->progress_count);
            else if($item == 'user_ip')
                $this->db->set('user_ip', $this->user_ip);
            else if($item == 'payment_info')
                $this->db->set('payment_info', $this->payment_info);
            else if($item == 'expert_info')
                $this->db->set('expert_info', $this->expert_info);
            else if($item == 'records')
                $this->db->set('records', $this->record_id_array);
            else
                log_message('error', 'Hit_record: Unrecognized key to update:'. $item);
        }
        $this->db->where('id', $this->id);
        return $this->db->update(Hit_record::TABLE_NAME);
    }


    public function get_comparison_id($index=null){
        if(is_null($index))
            $index = $this->progress_count;
        return $this->record_id_array[$index];
    }

    public function get_comparison_size(){
        if(isset($this->record_id_array))
            return count($this->record_id_array);
        return 0;
    }

    public function get_by_id($id){
        $this->db->where('id', $id);
        $query = $this->db->get(Hit_record::TABLE_NAME);
        if($query->num_rows() > 0){
            $row = $query->row();
            $this->id = $row->id;
            $this->start_time = $row->start_time;
            $this->end_time = $row->end_time;
            $this->record_id_array = json_decode($row->records);
            $this->progress_count = $row->progress_count;
            $this->expert_info = $row->expert_info;
            $this->user_ip = $row->user_ip;
            $this->payment_info = $row->payment_info;
            return $this;
        }
        else
            return null;
    }
}