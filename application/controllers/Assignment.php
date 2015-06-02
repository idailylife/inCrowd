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



class Assignment extends CI_Controller {

    public function index(){
        if(!isset($_SERVER['REQUEST_METHOD'])){
            show_error("REQUEST_METHOD not set.");
        }

        $this->load->library('session'); //Load session library
        if(!$this->check_authority() && !DEBUG_MODE){
            //Authentication failed
            header("Location: ". base_url());
            return;
        }


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
        if(!isset($_SESSION[KEY_PASS])){
            if($this->have_unfinished_hit())
                return true;
            else
                return false;
        }
            return false;
        if($_SESSION[KEY_PASS] >= 1)
            return true;
        return false;
    }


    function have_unfinished_hit(){
        if (isset($_SESSION[KEY_HIT_RECORD])){
            return true;
        } elseif (isset($_COOKIE[KEY_HIT_COOKIE])){
            $this->load->helper('cookie');
            $hit_record = new Hit_record();
            $hit_id = $hit_record->get_id_by_token(get_cookie(KEY_HIT_COOKIE, true));
            if(-1 != $hit_id){
                $_SESSION[KEY_HIT_RECORD] = $hit_id;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function get_current_hit_id(){
        if(!$this->have_unfinished_hit())
            return -1;
        return $_SESSION[KEY_HIT_RECORD];
    }

    function get_image_thumb_path($origin_file_path){
        $extension = pathinfo($origin_file_path, PATHINFO_EXTENSION);
        $wo_extension = preg_replace('/\\.[^.\\s]{3,4}$/', '', $origin_file_path);
        return $wo_extension . '_thumb.'. $extension;
    }

    function get_image_thumb_url($origin_url){
        $origin_url = str_replace("\r", "", $origin_url);
        return $this->get_image_thumb_path($origin_url);
    }

    /**
     * @param $origin_file_path Must be the server path, NOT a url
     * @return string $thumb_path
     */
    function create_thumbnail($origin_file_path){
        //If image exists, there is no need to regenerate one
        $origin_file_path = str_replace("\r", "", $origin_file_path); //Remove unwanted control character
        $thumb_path = $this->get_image_thumb_path($origin_file_path);
        if(file_exists($thumb_path))
            return $thumb_path;

        //Configurations
        $config['image_library'] = 'gd2';
        $config['source_image'] = $origin_file_path;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']         = 720;
        $config['height']       = 720;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
        return $thumb_path;
    }

    /**
     * 得到比较数据，用于view呈现
     *
     * @param $hit_record HIT记录，用来获取当前进度
     * @return array
     */
    private function get_comp_data($hit_record){
        $cmp_record = new Compare_record();
        $cmp_record->get_by_id($hit_record->get_comparison_id());
        //Get image info
        $data = array(
            'img_src1'      => IMAGE_BASE_URL,
            'img_src2'      => IMAGE_BASE_URL,
            'prog_current'  => $hit_record->getLevelProgress() +1,//progress_count + 1,
            'prog_total'    => COMPARISON_SIZE +1,//$hit_record->getCmpLength(),
            'level'         => $hit_record->getHitLevel(),
            'q_type'        => $cmp_record->q_type,
            //'max_size'      => MAX_COMPARISON_SIZE,
            'next_img_src1' => IMAGE_BASE_URL,
            'next_img_src2' => IMAGE_BASE_URL,
            'total_score'   => round($hit_record->score),
            'next_score'    => round($hit_record->getCurrLevelScore())
        );  //Array for view variables
//        $temp_path = array(
//            'img1' => PATH_TO_RESOURCES,
//            'img2' => PATH_TO_RESOURCES
//        );  //Array for resource image path on the disk
        switch($cmp_record->comp_type){
            case CMP_TYPE_GENERAL:
                $model = new General_eval_pic();
                break;
            case CMP_TYPE_USERTEST:
                $model = new User_eval_pic();
                break;
        }
        $model->get_by_id($cmp_record->comp_id1);
        $data['img_src1'] .= $model->src;
        $model->get_by_id($cmp_record->comp_id2);
        $data['img_src2'] .= $model->src;

        //Fetch next image urls
        if($hit_record->progress_count <
            $hit_record->getCmpLength() -1 ){
            $next_cmp_id = $hit_record->get_comparison_id($hit_record->progress_count+1);
            $cmp_record->get_by_id($next_cmp_id);
            switch($cmp_record->comp_type){
                case CMP_TYPE_GENERAL:
                    $model = new General_eval_pic();
                    break;
                case CMP_TYPE_USERTEST:
                    $model = new User_eval_pic();
                    break;
            }
            $model->get_by_id($cmp_record->comp_id1);
            $data['next_img_src1'] .= $model->src;
            $model->get_by_id($cmp_record->comp_id2);
            $data['next_img_src2'] .= $model->src;
        } else {
            $data['next_img_src1'] = null;
            $data['next_img_src2'] = null;
            $data['can_expand']    = $hit_record->can_expand();
        }


        //Generate image thumbnail
//        $i = 1;
//        foreach($temp_path as $key => $value){
//            $this->create_thumbnail($value);    //Create image thumbnail if needed
//            $data['img_thumb'.$i] = $this->get_image_thumb_url(
//                $data['img_src'.$i]
//            );  //Set image thumbnail url(Not local path)
//            $i++;
//        }
        return $data;
    }

    /**
     * Get IP address of client
     * ref: http://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php
     */
    private function get_client_ip(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    private function index_get(){
        $hit_record = new Hit_record();
        //See if we have an unfinished HIT assignment..
        if($this->have_unfinished_hit()){
            $hit_id = $_SESSION[KEY_HIT_RECORD];
            $hit_record->get_by_id($hit_id);
        } else {
            //Create a new HITRecord
            $hit_record->create_comparison();

            $hit_record->mark_time(true);
            $hit_record->user_ip = $this->get_client_ip();
            //If the user allow cookie storage, create a token
            if(isset($_GET['keep_cookie']) &&
                $_GET['keep_cookie'] == '1') {
                $token = md5(''+time());
                $this->load->helper('cookie');
                $cookie = array(
                    'name'  => KEY_HIT_COOKIE,
                    'value' => $token,
                    'expire'=> '173000' //one day
                );
                $this->input->set_cookie($cookie);
                $hit_record->token = $token;
            }

            $hit_id = $hit_record->push_to_db();
            //Write to session
            $_SESSION[KEY_HIT_RECORD] = $hit_id;
        }


        //If this hit is already finished, jump to /finish
        if($hit_record->progress_count >= $hit_record->getCmpLength()){
            if(!empty($hit_record->end_time)){
                header("Location: ". base_url("finish"));
                return;
            } else {
                $hit_record->progress_count --;  // DO NOT WRITE TO DATABASE
                $data = $this->get_comp_data($hit_record);
                $data['semi_finish'] = true;
                $data['total_score'] = round($hit_record->score);
                $data['next_score'] = round($hit_record->getCurrLevelScore());

                //echo json_encode($data);
            }
        } else {
            $data = $this->get_comp_data($hit_record);
            $data['semi_finish'] = false;
        }
        $this->load->view('assignment', $data);

    }

    /**
     * 使用POST方法提交当前比较对的结果:
     *  creativity
     *  usability
     *  duration
     * 并获取下一个比较对的数据（JSON）
     *
     */
    private function index_post(){

        $ret_data = array();
        $hit_id = $this->get_current_hit_id();
        if(-1 == $hit_id){
            $ret_data['status'] = 2; //Error
            $ret_data['reason'] = 'HIT record not in the session';
        } elseif(isset($_POST['expand'])) {
            //Expand hit comparison array and return the info of next cmp
            $hit_record = new Hit_record();
            $hit_record->get_by_id($hit_id);
            if ($hit_record->can_expand()) {
                $hit_record->create_comparison(); //TODO: Set a proper value
                $hit_record->update_db(array('records'));

                $ret_data = $this->get_comp_data($hit_record);
                $ret_data['status'] = 0;
            } else {
                $ret_data['status'] = 2;
                $ret_data['reason'] = 'Cannot expand comparison array: limitation reached.';
            }
        } elseif(!isset($_POST['creativity'])
                && !isset($_POST['usability'])){
                $ret_data['status'] = 2; //Error
                $ret_data['reason'] = 'Insufficient POST data ';
        } else {
            $hit_record = new Hit_record();
            $hit_record->get_by_id($hit_id);
            $progress = $hit_record->progress_count;
            $current_comp_id = $hit_record->record_id_array[$progress];
            $cmp_record = new Compare_record();
            $cmp_record->get_by_id($current_comp_id);

            $answer = 0;    //'B':0; 'A':1, 'not sure':2
            if($cmp_record->q_type == Compare_record::QTYPE_CREATIVITY){ //Question for creativity
                if(strcmp($_POST['creativity'],'A') == 0)
                    $answer = 1;
                elseif(strcmp($_POST['creativity'], 'X') == 0)
                    $answer = 2;
            } elseif($cmp_record->q_type == Compare_record::QTYPE_USABILITY){
                if(strcmp($_POST['usability'], 'A') == 0)
                    $answer = 1;
                elseif(strcmp($_POST['usability'], 'X') == 0)
                    $answer = 2;
            }
            $cmp_record->answer = $answer;

            $hit_key_ary = ['progress_count', 'score'];
            //Add score
            $curr_score = $hit_record->getCurrLevelScore();
            $hit_record->score += $curr_score;

            /* Re-calculate penalty if it's a QoE question */
            if($cmp_record->comp_type == CMP_TYPE_USERTEST){
                $ground_truth = $cmp_record->get_ground_truth();
                if($ground_truth != $answer){
                    $hit_record->score_rate *= PENALTY_RATE_QOE;
                } else {
                    $hit_record->score_rate *= BONUS_RATE_QOE;
                }
                array_push($hit_key_ary, 'score_rate');
            }
            /* End of re-calculation */



            $cmp_key_ary = array('answer');
            if(isset($_POST['duration'])){
                $cmp_record->duration = $this->input->post('duration', true);
                array_push($cmp_key_ary, 'duration');
            }

            //Update database
            $cmp_record->update_db($cmp_key_ary);

            //Move to next comparison
            $hit_record->progress_count = ++$progress;
            $hit_record->update_db($hit_key_ary);

            if($hit_record->progress_count < $hit_record->getCmpLength()){
                //$current_comp_id = $hit_record->record_id_array[$progress];
                $ret_data = $this->get_comp_data($hit_record);
                //Return json array
                $ret_data['status'] = 0;
            } else {
                //End of comparison stage
                $ret_data['status'] = 1; //End of comparison
                $ret_data['can_expand'] = $hit_record->can_expand();
                $ret_data['total_score'] = round($hit_record->score);
                $ret_data['next_score'] = round($hit_record->getCurrLevelScore());

            }
        }
        echo json_encode($ret_data);
    }

    /**
     * 判断当前的HIT任务是否可以增长
     * 返回状态：
     *  -1 => 鉴权失败
     *  -2 => 没有未完成的HIT
     *  0  => 无法增长
     *  1  => 可以增长
     * @return string
     */
    public function can_expand(){
        $retData = [];
        if(!$this->check_authority() && !DEBUG_MODE){
            //Authentication failed
            $retData['status'] = -1;
            $retData['msg'] = "Permission denied. Log in first.";

        } else {
            $hit_record = new Hit_record();
            if($this->have_unfinished_hit()){
                $hit_id = $_SESSION[KEY_HIT_RECORD];
                $hit_record->get_by_id($hit_id);
                $retData['status'] = 0;
                if($hit_record->can_expand())
                    $retData['status'] = 1;

            } else {
                $retData['status'] = -2;
                $retData['msg'] = "No unfinished HIT record.";
            }
        }
        echo json_encode($retData);
    }
}