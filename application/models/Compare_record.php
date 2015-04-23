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
 * duration:完成比较的时间
 *
 */
require_once('public_defination.php');
require_once('General_eval_pic.php');
require_once('User_eval_pic.php');

class Compare_record extends CI_Model {

    public $id;
    public $comp_id1;
    public $comp_id2;
    public $comp_type;
    public $answer;
    public $duration;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 从图片库中随机选择图片作为比较对
     * 返回值：是否生成成功
     */
    public function generate_record() {
        if(!isset($this->comp_type)) {
            show_error('Failed to generate comparison: type not set.');
            return null;
        }
        $cmp_obj1 = null;
        $cmp_obj2 = null;
        switch($this->comp_type){
            case CMP_TYPE_GENERAL:
                //TODO: UNIT TEST
                $cmp_obj1 = new General_eval_pic();
                $cmp_obj1->get_random();
                $cmp_obj2 = new General_eval_pic();
                $cmp_obj2->get_random($cmp_obj1->id);
                break;
            case CMP_TYPE_USERTEST:
                //TODO: UNIT TEST
                $cmp_obj1 = new User_eval_pic();
                $cmp_obj1->get_random();
                $cmp_obj2 = new User_eval_pic();
                $cmp_obj2->get_random($cmp_obj1->id);

                break;
            default:
                show_error('Failed to generate comparison: unknown type.');
                return null;
        }
        $this->comp_id1 = $cmp_obj1->id;
        $this->comp_id2 = $cmp_obj2->id;
        return $this;
    }


    //TODO: implement necessary interface
}