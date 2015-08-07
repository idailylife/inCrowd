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
 * pay_status:   报酬支付状态： 0 任务完成,审核中；1 待支付；2 已支付；-1 任务尚未完成； -2 审核失败
 * pay_amount:   报酬支付金额
 * score: 当前积分
 * score_rate: 当前得分折扣 如 0.95
 */
class Hit_record extends CI_Model {
    const TABLE_NAME = 'hit_record';
    //Payment status
    const PS_FAILED = -2;
    const PS_UNFINISHED = -1;
    const PS_PAID = 2;
    const PS_PENDING = 1;
    const PS_FINISHED = 0;

    public $id;
    public $start_time;
    public $end_time;
    public $record_id_array; //存放Compare_record对象的ID (从数据库存取需要json_encode/decode)
    public $progress_count;
    public $user_ip;
    public $payment_info;
    public $expert_info;
    public $token;           //对应cookie的内容
    public $advice;          //用户意见建议
    public $pay_status;     //报酬支付状态： 0 任务完成,审核中；1 待支付；2 已支付；-1 任务尚未完成； -2 审核失败
    public $pay_amount;     //报酬支付金额
    public $score;
    public $score_rate;

    private $model_generated;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->model_generated = false;
        $this->progress_count = 0;
        $this->pay_status = -1;
        $this->score = 0;
        $this->score_rate = 1.0;
    }


    /**
     * 在原有基础上新增比较对，并写入数据库
     * @param int $comparison_size  新增总长度
     * @param int $test_cmp_size    其中的QoE题目个数
     */
    public function create_comparison($comparison_size = COMPARISON_SIZE, $test_cmp_size = TEST_CMP_SIZE){
        //TODO: Create trap questions in case of spammer
        if(is_null($this->record_id_array)){
            //Initialize array of record id when not yet set.
            $this->record_id_array = [];
        }
        $record_ary_size = count($this->record_id_array);
        //Build CMP_TYPE array
        $cmp_type_ary = [];
        for($i=0; $i<$comparison_size; $i++){
            if($i < $test_cmp_size)
                array_push($cmp_type_ary, CMP_TYPE_USERTEST);
            else
                array_push($cmp_type_ary, CMP_TYPE_GENERAL);
        }
        shuffle($cmp_type_ary);

        $tmp_ary = [[],[]];
        for($i=$record_ary_size; $i<$record_ary_size + $comparison_size; $i++){
            $cmp = new Compare_record();
            $cmp->comp_type = $cmp_type_ary[$i - $record_ary_size];

            $q_type = ($i - $record_ary_size) < $comparison_size/2 ? 0:1;
            $cmp->generate_record($q_type);
            $cmp_id = $cmp->push_to_db();
            array_push($tmp_ary[$q_type], $cmp_id);
        }

        shuffle($tmp_ary[0]);
        shuffle($tmp_ary[1]);
        //Randomly create one trap question//////
        $trap_i = rand(0,1); //Choose one trap type (q_type)
        $trap_index = rand(0, count($tmp_ary[$trap_i]) -2);
        $trap_cmp_src = $tmp_ary[$trap_i][$trap_index];
        $cmp = new Compare_record();
        $cmp->get_by_id($trap_cmp_src);
        $cmp->trap_id = $trap_cmp_src;
        $cmp->set_model_generated();
        $cmp_id = $cmp->push_to_db();
        array_push($tmp_ary[$trap_i], $cmp_id);
        ////////////////////////////////////////////////
        $this->record_id_array = array_merge($this->record_id_array,
            $tmp_ary[0], $tmp_ary[1]);
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
            'records'    => json_encode($this->record_id_array),
            'pay_status' => $this->pay_status,
            'score'      => $this->score,
            'score_rate' => $this->score_rate
        );
        if(isset($this->start_time))
            $data['start_time'] = $this->start_time;
        if(isset($this->user_ip))
            $data['user_ip'] = $this->user_ip;
        if(isset($this->token)){
            $data['token'] = $this->token;
        }


        $this->db->insert($this->db->dbprefix(Hit_record::TABLE_NAME), $data);
        //$db_helper = new Model_helper();
        $count = $this->get_max_id();//$db_helper->get_auto_increment_value(Hit_record::TABLE_NAME);
        return $count;
    }

    public function get_max_id(){
        $maxid = 0;
        $row = $this->db->query('SELECT MAX(id) AS `maxid` FROM `'.
            $this->db->dbprefix(Hit_record::TABLE_NAME).'`')->row();
        if ($row) {
            $maxid = $row->maxid;
        }
        return $maxid;
    }

    public function update_db($key_array){
        foreach($key_array as $item){
            if($item == 'start_time')
                $this->db->set('start_time', $this->start_time);
            elseif($item == 'end_time')
                $this->db->set('end_time', $this->end_time);
            elseif($item == 'progress_count')
                $this->db->set('progress_count', $this->progress_count);
            elseif($item == 'user_ip')
                $this->db->set('user_ip', $this->user_ip);
            elseif($item == 'payment_info')
                $this->db->set('payment_info', $this->payment_info);
            elseif($item == 'expert_info')
                $this->db->set('expert_info', $this->expert_info);
            elseif($item == 'records')
                $this->db->set('records', json_encode($this->record_id_array));
            elseif($item == 'token')
                $this->db->set('token', $this->token);
            elseif($item == 'advice')
                $this->db->set('advice', $this->advice);
            elseif($item == 'pay_status')
                $this->db->set('pay_status', $this->pay_status);
            elseif($item == 'pay_amount')
                $this->db->set('pay_amount', $this->pay_amount);
            elseif($item == 'score')
                $this->db->set('score', $this->score);
            elseif($item == 'score_rate')
                $this->db->set('score_rate', $this->score_rate);
            else
                log_message('error', 'Hit_record: Unrecognized key to update:'. $item);
        }
        $this->db->where('id', $this->id);
        return $this->db->update($this->db->dbprefix(Hit_record::TABLE_NAME));
    }


    public function get_comparison_id($index=null){
        if(is_null($index))
            $index = $this->progress_count;
        return $this->record_id_array[$index];
    }

// /*Removed because of duplication*/
//    public function get_comparison_size(){
//        if(isset($this->record_id_array))
//            return count($this->record_id_array);
//        return 0;
//    }

    public function get_by_id($id){
        $this->db->where('id', $id);
        $query = $this->db->get($this->db->dbprefix(Hit_record::TABLE_NAME));
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
            $this->score = $row->score;
            $this->score_rate = $row->score_rate;
            return $this;
        }
        else
            return null;
    }

    /**
     * 根据token返回id，无法查找则返回-1
     * @param $token
     * @return int
     */
    public function get_id_by_token($token){
        $this->db->where('token', $token);
        $query = $this->db->get($this->db->dbprefix(Hit_record::TABLE_NAME));
        if($query->num_rows() >0) {
            $row = $query->row();
            return $row->id;
        }
        return -1;
    }

    /**
     * 得到比较对的总长度（总问题数量）
     */
    public function getCmpLength(){
        if(is_null($this->record_id_array)){
            return -1;
        }
        return count($this->record_id_array);
    }

    public function can_expand(){
        if($this->score_rate < EXPAND_RATE_MIN){
            return false;       // Penalty over limit
        }

        return $this->getCmpLength() < MAX_COMPARISON_SIZE - 1;
    }

    /**
     * 用支付方式查询支付信息
     * @param $p_info
     * @return array 结果array[[info1],[info2],...];
     *                  info = [start_time, pay_status, pay_amount];
     */
    public function getPayStatusByPayInfo($p_info){
        $this->db->where('payment_info', $p_info);
        $result_ary = [];
        $query = $this->db->get($this->db->dbprefix(Hit_record::TABLE_NAME));
        foreach($query->result() as $row){
            $ary = [];
            $ary[0] = $row->start_time;
            $ary[1] = $row->pay_status;
            $ary[2] = $row->pay_amount;
            $ary[3] = $row->score;
            array_push($result_ary, $ary);
        }
        return $result_ary;
    }

    /**
     * 获得当前表长度(HIT任务个数)
     * @return mixed
     */
    public function getHitRecordTotalSize(){
        return $this->db->count_all($this->db->dbprefix(Hit_record::TABLE_NAME));
    }

    /**
     * @return int 当前等级
     */
    public function getHitLevel(){
        return intval($this->progress_count /(COMPARISON_SIZE+1)) + 1;
    }

    public function getScorePerCmp($level){
        switch($level){
            case 0:
                $score = 0;
                break;
            case 1:
                $score = 25; //20
                break;
            case 2:
                $score = 35; //40
                break;
            case 3:
                $score = 43; //50
                break;
            case 4:
                $score = 50; //55
                break;
            case 5:
                $score = 55; //60
                break;
            case 6:
                $score = 58; //63
                break;
            case 7:
                $score = 60; //65
                break;
            default:
                $score = 65;
        }
        return $score;
    }

    /**
     * @param bool $with_penalty 是否算入罚分
     * @return int 当前的等级分
     */
    public function getCurrLevelScore($with_penalty = true){
        $score = $this->getScorePerCmp($this->getHitLevel());
        if($with_penalty)
            $score = $score * $this->score_rate;
        return $score;
    }

    /**
     * @return int 当前等级内的进度
     */
    public function getLevelProgress(){
        return $this->progress_count % (COMPARISON_SIZE+1);
    }
}