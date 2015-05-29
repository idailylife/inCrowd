<?php
/**
 * Main Controller of this project, `/` will be redirected here.
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/20
 * Time: 19:30
 */
require_once(APPPATH . 'models/Hit_record.php');

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
        if(isset($_SESSION[KEY_PASS])){
            $continue_flag = 1;
        }
        if(isset($_COOKIE[KEY_HIT_COOKIE])){
            $continue_flag = 2;
        }
        if($hitSize <= MAX_HIT_SIZE
            || $continue_flag > 0){

            $data = array(
                'cont_flag' => $continue_flag
            );
            $this->load->view('main', $data);
        } else {
            $data = [
                'heading' => '抱歉抱歉',
                'message' => '当前任务数量超过系统限制，请过几天再访问啦'
            ];
            $this->load->view('traffic_control', $data);
        }
    }

}