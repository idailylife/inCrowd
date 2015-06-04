<?php
/**
 * Main Controller of this project, `/` will be redirected here.
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/20
 * Time: 19:30
 */
require_once(APPPATH . 'models/Hit_record.php');
require_once(APPPATH . 'models/Invitation_record.php');

class Main extends CI_Controller {

    public function index(){

        if(!isset($_SERVER['REQUEST_METHOD'])){
            show_error("REQUEST_METHOD not set.");
        }
        $this->load->library('session'); //Load session library
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            show_404();
        } else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->index_get();
        } else {
            show_error("REQUEST_METHOD not legal");
        }

    }

    function index_get() {
        $hit_record = new Hit_record();
        $hitSize = $hit_record->getHitRecordTotalSize();
        $continue_flag = 0;
//        if(isset($_SESSION[KEY_PASS])){
//            $continue_flag = 1;
//        }
        if(isset($_COOKIE[KEY_HIT_COOKIE])){
            $hit_id = $hit_record->get_id_by_token($_COOKIE[KEY_HIT_COOKIE]);
            if($hit_id != -1){
                $hit_record->get_by_id($hit_id);
                if(!is_null($hit_record) && empty($hit_record->payment_info))
                    $continue_flag = 2;
            }
        }
        if( ($hitSize <= MAX_HIT_SIZE & !NEED_INVITE)
            || $continue_flag > 0 ){

            $data = array(
                'cont_flag' => $continue_flag
            );

            $this->load->view('main', $data);
            return;
        } elseif(isset($_GET['invite_code'])){
            //Check invite code
            $inv_record = new Invitation_record();
            $inv_code = $_GET['invite_code'];
            $status = $inv_record->check_availability($inv_code);
            if($status == 0){
                $data = array(
                    'cont_flag' => $continue_flag,
                    'invite_code' => $inv_code
                );
                $this->load->view('main', $data);
                return;
            } else {
                $data = [
                    'heading' => '邀请链接失效',
                    'message' => '当前的邀请链接不存在或已超过使用限制，请重试或等待下一批实验'
                ];
                $this->load->view('traffic_control', $data);
            }
        } else {
            $data = [
                'heading' => '抱歉抱歉',
                'message' => '当前任务数量超过系统限制，请过几天再访问, 或通过邀请链接访问.'
            ];
            $this->load->view('traffic_control', $data);
        }
    }

}