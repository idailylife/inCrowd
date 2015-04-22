<?php
/**
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/21
 * Time: 19:04
 */
define('DEBUG_MODE', false);

class Assignment extends CI_Controller {

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

        $this->load->view('assignment');
    }

    function index_post(){

    }
}