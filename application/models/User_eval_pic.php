<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:02
 */

/**
 * Class User_eval_pic
 * 用于评估用户质量的单张图片
 * id
 * src:存储位置
 * category: 所属分类（IDEA_GOLD、REDDOT_BOFB这种）
 */
class User_eval_pic extends CI_Model {
    const TABLE_NAME = 'user_eval_pic';
    public $id;
    public $src;
    //public $real_answer;
    public $category;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 随机选择一张图，更新对象，返回$this
     * @param except: 需要排除的id值
     * @return $this
     */
    public function get_random($except = null){
        //取表中的所有项目出来
        //$this->db->select('id');
        $query = $this->db->get(User_eval_pic::TABLE_NAME);  // SELECT id FROM general_eval_pic
        $ary_result = $query->result(); //Query results as array
        if(empty($ary_result)){
            show_error('Error on User_eval_pic->get_random(): Empty result case #0');
            return null;
        }
        //Shuffle results
        shuffle($ary_result);
        foreach($ary_result as $row) {
            if($row->id == $except)
                continue;
            $this->id = $row->id;
            $this->src = $row->src;
            $this->category = $row->category;
            return $this;
        }
        show_error('Error on User_eval_pic->get_random(): Empty result case #1');
        return null;
    }

    /**
     * 随机选择一张图，但不属于某一category
     * @param $category 获奖等级（gold,silver,bronze,...）
     * @return $this|null 找不到则返回null
     */
    public function get_random_except($category){
        $this->db->where('category !=', $category);
        $query = $this->db->get(User_eval_pic::TABLE_NAME);
        if($query->num_rows() > 0){
            $ary_result = $query->result();
            shuffle($ary_result);
            $row = $query->row();
            $this->id = $row->id;
            $this->src = $row->src;
            $this->category = $row->category;
            return $this;
        }
        show_error('Error on User_eval_pic->get_random_except(): Empty result case #1');
        return null;
    }

    /**
     * 从数据库中查询某id的model
     * @param $id
     * @return $this|null 返回$this或null
     */
    public function get_by_id($id){
        $query = $this->db->get_where(User_eval_pic::TABLE_NAME,
            array('id' => $id));
        if($query->num_rows() < 1){
            return null;
        }
        $row = $query->row();
        $this->id = $row->id;
        $this->src = $row->src;
        $this->category = $row->category;
        return $this;
    }

    //TODO: implement necessary interface
}
