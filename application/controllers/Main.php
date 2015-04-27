<?php
/**
 * Main Controller of this project, `/` will be redirected here.
 * Created by PhpStorm.
 * User: bowei
 * Date: 2015/4/20
 * Time: 19:30
 */
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
        $continue_flag = false;
        if(isset($_SESSION['pass'])){
            $continue_flag = true;
        }
        $data = array(
            'cont_flag' => $continue_flag
        );
        $this->load->view('main', $data);
    }
}