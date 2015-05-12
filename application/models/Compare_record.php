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
 * comp_type
 * answer:比较结果
 * q_type:提问种类 0-创新性，1-实用性
 * duration:完成比较的时间
 * trap_id: 如果这个题目是陷阱题，此项填写原题的id号，否则为-1
 */
require_once(APPPATH . '../'. SYSDIR. '/core/Model.php');
require_once('Model_helper.php');
require_once('General_eval_pic.php');
require_once('User_eval_pic.php');

class Compare_record extends CI_Model {
    const TABLE_NAME = 'compare_record';
    const QTYPE_CREATIVITY = 0;
    const QTYPE_USABILITY  = 1;

    public $id;
    public $comp_id1;
    public $comp_id2;
    public $comp_type;
    public $q_type;
    public $answer;
    public $duration;
    public $trap_id;

    private $model_generated;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->model_generated = false;
        $this->trap_id = -1;
    }

    public function set_model_generated(){
        $this->model_generated = true;
    }

    /**
     * 从图片库中随机选择图片作为比较对
     * @param $q_type
     * @return $this|null 返回值：是否生成成功
     * 返回值：是否生成成功
     */
    public function generate_record($q_type) {
        if(!isset($this->comp_type)) {
            show_error('Failed to generate comparison: type not set.');
            return null;
        }
        $cmp_obj1 = null;
        $cmp_obj2 = null;
        switch($this->comp_type){
            case CMP_TYPE_GENERAL:
                //For general comparisons
                $cmp_obj1 = new General_eval_pic();
                $cmp_obj1->get_random();
                $cmp_obj2 = new General_eval_pic();
                $cmp_obj2->get_random($cmp_obj1->id);
                break;
            case CMP_TYPE_USERTEST:
                //For user quality test comparisons
                $cmp_obj1 = new User_eval_pic();
                $cmp_obj1->get_random();
                $cmp_obj2 = new User_eval_pic();
                //$cmp_obj2->get_random($cmp_obj1->id);
                $cmp_obj2->get_random_except($cmp_obj1->category);
                break;
            default:
                show_error('Failed to generate comparison: unknown type.');
                return null;
        }
        $this->comp_id1 = $cmp_obj1->id;
        $this->comp_id2 = $cmp_obj2->id;
        $this->q_type = $q_type;//rand(0,1);
        //TODO: 可能需要采用聪明点儿的算法
        $this->model_generated = true;
        return $this;
    }

    /**
     * 将数据写入数据库，并返回新纪录的ID
     * 必须先执行generate_record()
     */
    public function push_to_db() {
        if(!$this->model_generated){
            show_error('Compare_record: Failed to insert db record, model should be generated first');
            return -1;
        }
        $data = array(
            'comp_id1' => $this->comp_id1,
            'comp_id2' => $this->comp_id2,
            'comp_type'=> $this->comp_type,
            'q_type' => $this->q_type,
            'trap_id' => $this->trap_id
        );
        $this->db->insert($this->db->dbprefix(Compare_record::TABLE_NAME), $data);

        $count = $this->get_max_id();
        return $count;
    }

    public function get_max_id(){
        $maxid = 0;
        $row = $this->db->query('SELECT MAX(id) AS `maxid` FROM `'. $this->db->dbprefix(Compare_record::TABLE_NAME).'`')->row();
        if ($row) {
            $maxid = $row->maxid;
        }
        return $maxid;
    }

    /**
     * 更新指定key字段的数据到数据库
     * @param $key_array 键数列
     */
    public function update_db($key_array){
        foreach($key_array as $item){
            if($item == 'comp_id1')
                $this->db->set('comp_id1', $this->comp_id1);
            elseif ($item == 'comp_id2')
                $this->db->set('comp_id2', $this->comp_id2);
            elseif ($item == 'comp_type')
                $this->db->set('comp_type', $this->comp_type);
            elseif ($item == 'answer')
                $this->db->set('answer', $this->answer);
            elseif ($item == 'duration')
                $this->db->set('duration', $this->duration);
            elseif ($item == 'q_type')
                $this->db->set('q_type', $this->q_type);
            elseif ($item == 'trap_id')
                $this->db->set('trap_id', $this->trap_id);
            else
                log_message('error', 'Hit_record: Unrecognized key to update:'. $item);
        }
        $this->db->where('id', $this->id);
        return $this->db->update($this->db->dbprefix(Compare_record::TABLE_NAME));
    }

    public function get_by_id($id){
        $query = $this->db->get_where($this->db->dbprefix(Compare_record::TABLE_NAME),
            array('id'=>$id));
        $row = $query->row();
        $this->id = $row->id;
        $this->answer = $row->answer;
        $this->comp_id1 = $row->comp_id1;
        $this->comp_id2 = $row->comp_id2;
        $this->comp_type = $row->comp_type;
        $this->duration = $row->duration;
        $this->q_type = $row->q_type;
        $this->trap_id = $row->trap_id;
        return $this;
    }

}