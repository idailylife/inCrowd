<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 19:04
 */
require_once(APPPATH . 'models/Hit_record.php');
require_once(APPPATH . 'models/Compare_record.php');
require_once(APPPATH . '/models/General_eval_pic.php');
require_once(APPPATH . '/models/User_eval_pic.php');

define('DEBUG_MODE', false);

class Assignment extends CI_Controller {
    const COMPARISON_SIZE = 30;     //每个用户需要比较的总图片·对·数
    const TEST_CMP_SIZE = 5;        //其中每个用户的用户能力测试用的图片·对·数

    public function index(){
        if(!isset($_SERVER['REQUEST_METHOD'])){
            show_error("REQUEST_METHOD not set.");
        }

        $this->load->library('session'); //Load session library

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->index_post();
        } else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->index_get();
        } else {
            show_error("REQUEST_METHOD not legal");
        }
    }

    /**
     * 检查用户是否有权限进入
     * 返回值 true / false
     */
    function check_authority(){
        if(!isset($_SESSION['pass']))
            return false;
        if($_SESSION['pass'] == '1')
            return true;
        return false;
    }

    function index_get(){
        if(!$this->check_authority() && !DEBUG_MODE){
            //Authentication failed
            header("Location: http://localhost/inCrowd");
            return;
        }
        //See if we have an unfinished HIT assignment..
        //If so, load the old job. (status stored in cookie)
        //TODO: implementation
        //////////////////////////////////////

        //Create a new HITRecord
        $hit_record = new Hit_record();
        $hit_record->init(Assignment::COMPARISON_SIZE,
            Assignment::TEST_CMP_SIZE);
        //Generate comparisons for current user
        $hit_record->generate_comparison();
        $hit_id = $hit_record->push_to_db();

        //Get next comparison id
        $next_cmp_id = $hit_record->get_comparison_id();
        //Fetch next Compare_record by id
        $cmp_record = new Compare_record();
        $cmp_record->get_by_id($next_cmp_id);
        //Get image info
        $data = array(
            'img_src1' => null,
            'img_src2' => null
        );
        switch($cmp_record->comp_type){
            case CMP_TYPE_GENERAL:
                $gen_eval_model = new General_eval_pic();
                $gen_eval_model->get_by_id($cmp_record->comp_id1);
                $data['img_src1'] = $gen_eval_model->src;
                $gen_eval_model->get_by_id($cmp_record->comp_id2);
                $data['img_src2'] = $gen_eval_model->src;
                break;
            case CMP_TYPE_USERTEST:
                $user_eval_model = new User_eval_pic();
                $user_eval_model->get_by_id($cmp_record->comp_id1);
                $data['img_src1'] = $user_eval_model->src;
                $user_eval_model->get_by_id($cmp_record->comp_id2);
                $data['img_src2'] = $user_eval_model->src;
                break;
        }


        //Things need to be load:
        //2 image sources
        //
        $this->load->view('assignment', $data);
    }

    function index_post(){

    }
}