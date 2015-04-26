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
//    const COMPARISON_SIZE = 5;     //每个用户需要比较的总图片·对·数
//    const TEST_CMP_SIZE = 1;        //其中每个用户的用户能力测试用的图片·对·数
    const KEY_HIT_RECORD = 'current_hit_record';

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


    function have_unfinished_hit(){
        return isset($_SESSION[Assignment::KEY_HIT_RECORD]);
        //TODO: 检查cookie
    }

    function get_current_hit_id(){
        if(!$this->have_unfinished_hit())
            return -1;
        return $_SESSION[Assignment::KEY_HIT_RECORD];
    }

    function get_image_thumb_path($origin_file_path){
        $extension = pathinfo($origin_file_path, PATHINFO_EXTENSION);
        $wo_extension = preg_replace('/\\.[^.\\s]{3,4}$/', '', $origin_file_path);
        return $wo_extension . '_thumb.'. $extension;
    }

    function get_image_thumb_url($origin_url){
        return $this->get_image_thumb_path($origin_url);
    }

    /**
     * @param $origin_file_path Must be the server path, NOT a URL
     * @return string $thumb_path
     */
    function create_thumbnail($origin_file_path){
        //If image exists, there is no need to regenerate one
        $thumb_path = $this->get_image_thumb_path($origin_file_path);
        if(file_exists($thumb_path))
            return $thumb_path;

        //Configurations
        $config['image_library'] = 'gd2';
        $config['source_image'] = $origin_file_path;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 360;
        $config['height']       = 360;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
        return $thumb_path;
    }

    /**
     * @param $comp_id
     * @param $hit_record
     * @return array
     */
    private function get_comp_data($comp_id, $hit_record){
        $cmp_record = new Compare_record();
        $cmp_record->get_by_id($comp_id);
        //Get image info
        $data = array(
            'img_src1'      => IMAGE_BASE_URL,
            'img_src2'      => IMAGE_BASE_URL,
            'prog_current'  => $hit_record->progress_count + 1,
            'prog_total'    => $hit_record->get_comparison_size()
        );
        $temp_path = array(
            'img1' => PATH_TO_RESOURCES,
            'img2' => PATH_TO_RESOURCES
        );
        switch($cmp_record->comp_type){
            case CMP_TYPE_GENERAL:
                $gen_eval_model = new General_eval_pic();
                $gen_eval_model->get_by_id($cmp_record->comp_id1);
                $data['img_src1'] .= $gen_eval_model->src;
                $temp_path['img1'] .= $gen_eval_model->src;
                $gen_eval_model->get_by_id($cmp_record->comp_id2);
                $data['img_src2'] .= $gen_eval_model->src;
                $temp_path['img2'] .= $gen_eval_model->src;
                break;
            case CMP_TYPE_USERTEST:
                $user_eval_model = new User_eval_pic();
                $user_eval_model->get_by_id($cmp_record->comp_id1);
                $data['img_src1'] .= $user_eval_model->src;
                $temp_path['img1'] .= $user_eval_model->src;
                $user_eval_model->get_by_id($cmp_record->comp_id2);
                $data['img_src2'] .= $user_eval_model->src;
                $temp_path['img2'] .= $user_eval_model->src;
                break;
        }
        //Generate image thumbnail
        $i = 1;
        foreach($temp_path as $key => $value){
            $this->create_thumbnail($value);
            $data['img_thumb'.$i] = $this->get_image_thumb_url(
                $data['img_src'.$i]
            );
            $i++;
        }
        return $data;
    }

    private function index_get(){
        if(!$this->check_authority() && !DEBUG_MODE){
            //Authentication failed
            header("Location: http://localhost/inCrowd");
            return;
        }
        //See if we have an unfinished HIT assignment..
        //If so, load the old job. (status stored in cookie)
        //TODO: implementation
        //////////////////////////////////////
        $hit_record = new Hit_record();
        if($this->have_unfinished_hit()){
            $hit_id = $_SESSION[Assignment::KEY_HIT_RECORD];
            $hit_record->get_by_id($hit_id);
        } else {
            //Create a new HITRecord

            $hit_record->init(COMPARISON_SIZE,
                TEST_CMP_SIZE);
            //Generate comparisons for current user
            $hit_record->generate_comparison();
            $hit_id = $hit_record->push_to_db();
            //Write to session
            $_SESSION[Assignment::KEY_HIT_RECORD] = $hit_id;
        }


        //Get next comparison id
        $next_cmp_id = $hit_record->get_comparison_id();
        $data = $this->get_comp_data($next_cmp_id, $hit_record);
        //Things need to be load:
        //2 image sources
        //
        $this->load->view('assignment', $data);
    }

    /**
     * 使用POST方法提交当前比较对的结果:
     *  creativity
     *  usability
     * 并获取下一个比较对的数据（JSON）
     *
     */
    private function index_post(){
        if(!$this->check_authority() && !DEBUG_MODE){
            //Authentication failed
            header("Location: /");
            return;
        }
        $ret_data = array();
        $hit_id = $this->get_current_hit_id();
        if(-1 == $hit_id){
            $ret_data['status'] = 2; //Error
            $ret_data['reason'] = 'HIT record not in the session';
        } elseif(!isset($_POST['creativity'])
            || !isset($_POST['usability'])){
            $ret_data['status'] = 2; //Error
            $ret_data['reason'] = 'POST data incomplete';
        } else {
            $hit_record = new Hit_record();
            $hit_record->get_by_id($hit_id);
            $progress = $hit_record->progress_count;
            $current_comp_id = $hit_record->record_id_array[$progress];
            $cmp_record = new Compare_record();
            $cmp_record->get_by_id($current_comp_id);
            $answer = 0;
            if(strcmp($_POST['creativity'],'A') == 0)
                $answer = 1;
            $answer = $answer << 1;
            if(strcmp($_POST['usability'], 'A') == 0)
                $answer = $answer | 1;
            $cmp_record->answer = $answer;
            $key = array('answer');
            //Update database
            $cmp_record->update_db($key);

            //Move to next comparison
            $hit_record->progress_count = ++$progress;
            $hit_record->update_db(array('progress_count'));

            if($hit_record->progress_count < COMPARISON_SIZE){
                $current_comp_id = $hit_record->record_id_array[$progress];
                $ret_data = $this->get_comp_data($current_comp_id, $hit_record);
                //Return json array
                $ret_data['status'] = 0;
            } else {
                //End of comparison stage
                $ret_data['status'] = 1; //End of comparison
            }
        }
        echo json_encode($ret_data);
    }
}