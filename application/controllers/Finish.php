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
            header("Location: /");
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
        if(!isset($_SESSION['pass']))
            return false;
        if($_SESSION['pass'] == '1' |
            $_SESSION['pass'] == '2') {
            return true;
        }
        return false;
    }

    function have_unfinished_hit(){
        return isset($_SESSION[Finish::KEY_HIT_RECORD]);
        //TODO: 检查cookie
    }

    function get_current_hit_id(){
        if(!$this->have_unfinished_hit())
            return -1;
        return $_SESSION[Finish::KEY_HIT_RECORD];
    }

    private function index_post() {
        //TODO: Remove hit id

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
            header("Location: /assignment");
            return;
        }
        $_SESSION['pass'] = 2;
        $this->load->view('finish');
    }
}