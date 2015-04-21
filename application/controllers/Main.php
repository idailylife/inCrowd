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
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            show_404();
        } else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->index_get();
        } else {
            show_error("REQUEST_METHOD not legal");
        }

    }

    function index_get() {
        $this->load->view('main');
    }
}