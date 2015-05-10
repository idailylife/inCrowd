<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 11:08
 */

/**
 * Class General_eval_pic 需要评价的单张图片
 *   id
 *   src
 */
class General_eval_pic extends CI_Model {
    const TABLE_NAME = 'general_eval_pic';

    public $id;
    public $src;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 选择当前最需要被比较的比较对
     * 注意：结果直接以返回值形式给出，为数组形式
     *      不会将$this内的属性更新
     */
    public function get_most_needed() {
        //TODO: 找个快又好的算法
    }

    /**
     * 随机选择一张图，更新对象，返回$this
     * @param except: 需要排除的id值
     * @return $this
     */
    public function get_random($except = null){
        //取表中的所有项目出来
        //$this->db->select('id');
        $query = $this->db->get($this->db->dbprefix(General_eval_pic::TABLE_NAME));  // SELECT id FROM general_eval_pic
        $ary_result = $query->result(); //Query results as array
        if(empty($ary_result)){
            show_error('Error on General_eval_pic->get_random(): Empty result case #0');
            return null;
        }
        //Shuffle results
        shuffle($ary_result);
        foreach($ary_result as $row) {
            if($row->id == $except)
                continue;
            $this->id = $row->id;
            $this->src = $row->src;
            return $this;
        }
        show_error('Error on General_eval_pic->get_random(): Empty result case #1');
        return null;
    }

    /**
     * 从数据库中查询某id的model
     * @param $id
     * @return $this|null 返回$this或null
     */
    public function get_by_id($id){
        $query = $this->db->get_where($this->db->dbprefix(General_eval_pic::TABLE_NAME),
            array('id' => $id));
        if($query->num_rows() < 1){
            return null;
        }
        $row = $query->row();
        $this->id = $row->id;
        $this->src = $row->src;
        return $this;
    }


    //TODO: implement necessary interface
}