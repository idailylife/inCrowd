<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/26
 * Time: 19:21
 */
require_once(APPPATH . 'models/Hit_record.php');

class Finish extends CI_Controller {
    const KEY_HIT_RECORD = 'current_hit_record';

    public function index(){
        if(!isset($_SERVER['REQUEST_METHOD'])){
            show_error("REQUEST_METHOD not set.");
        }

        $this->load->library('session'); //Load session library

        if(!$this->check_authority()){
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

    function check_authority(){
        if(DEBUG_MODE)
            return true;
        if(!isset($_SESSION[KEY_PASS]))
            return false;
        if($_SESSION[KEY_PASS] == '1' |
            $_SESSION[KEY_PASS] == '2') {
            return true;
        }
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
        return $_SESSION[Finish::KEY_HIT_RECORD];
    }

    private function index_post() {
        //TODO: Remove hit id
        //Fetch post data
        $ret_data = array();
        $hit_id = $this->get_current_hit_id();
        if(-1 == $hit_id){
            $ret_data['status'] = 1;
            $ret_data['message'] = 'Illegal request. Hit_id not found';
        } elseif(!isset($_POST['payment_info']) ||
            !isset($_POST['expert_info'])){
            $ret_data['status'] = 1;
            $ret_data['message'] = 'Illegal request. POST variable not set';
        } else {
            $hit_record = new Hit_record();
            $hit_record->get_by_id($hit_id);
            $hit_record->mark_time(false);
            $hit_record->payment_info = $this->input->post('payment_info', true);//$_POST['payment_info'];
            $hit_record->expert_info = $this->input->post('expert_info', true);//$_POST['expert_info'];
            $hit_record->pay_status = Hit_record::PS_FINISHED;
            $key_array = array('end_time','payment_info',
                'expert_info', 'pay_status');

            if(!empty($_POST['advice'])){
                array_push($key_array, 'advice');
                $advice = substr($this->input->post('advice', true), 0, 10240); //最大字数限制
                $hit_record->advice = $advice;
            }

            $db_ret = $hit_record->update_db($key_array);

            if($db_ret){
                $ret_data['status'] = 0;
                $ret_data['message'] = 'Succeed';
                //Clear hit information in session & cookie
                unset($_SESSION[KEY_HIT_RECORD]);
                unset($_SESSION[KEY_PASS]);
                //unset($_COOKIE[KEY_HIT_COOKIE]);
                $this->load->helper('cookie');
                delete_cookie(KEY_HIT_COOKIE);
            } else {
                $ret_data['status'] = 2;
                $ret_data['message'] = 'Unable to update database';
            }
        }
        echo json_encode($ret_data);
    }

    private function index_get() {
        $hit_id = $this->get_current_hit_id();
        if(-1 == $hit_id){
            //Illegal request
            show_error('Error: illegal request. Unknown hit_id');
            return;
        }
        $hit_record = new Hit_record();
        $hit_record->get_by_id($hit_id);
        if($hit_record->progress_count < COMPARISON_SIZE){
            //Another error, unfinished assignment
            header("Location: ". base_url("assignment"));
            return;
        }
        $_SESSION[KEY_PASS] = 2;
        $this->load->view('finish');
    }
}