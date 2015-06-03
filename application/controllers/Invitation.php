<?php
/**
 * Created by PhpStorm.
 * User: Bowei
 * Date: 2015/6/2
 * Time: 15:37
 */
require_once(APPPATH . 'models/Invitation_record.php');

class Invitation extends CI_Controller{

    public function index(){
        if(!isset($_SERVER['REQUEST_METHOD'])){
            show_error("REQUEST_METHOD not set.");
        }
        $this->load->library('session'); //Load session library
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->index_post();
        } else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            show_404();
        } else {
            show_error("REQUEST_METHOD not legal");
        }
    }

    /**
     * Check on invitation code
     * If succeed, make a session item
     */
    public function index_post(){
        unset($_SESSION[KEY_INVITE_PASS]);

        $code_post = $this->input->post_get('invite_code', true);
        $use_code = $this->input->post_get('use_invite', true);  //If we should use this code

        $inv_record = new Invitation_record();

        $status = $inv_record->check_availability($code_post);

        if (strcmp($use_code, 'true') == 0){
            $inv_record->get_by_code($code_post);
            if($inv_record->increase_count() != 0){
                $status = -2; //Limit exceeded
            }
        }

        if($status == 0){
            $_SESSION[KEY_INVITE_PASS] = 1;
        }
        echo $status;
    }

    public function generate(){
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            show_404();
        }
        $config_path = APPPATH . 'controllers/inv_pw.txt';
        $config_file = fopen($config_path, 'r') or die("Unable to open invitation config file");
        $pwd = fread($config_file, filesize($config_path));

        if(!isset($_POST['pw'])){
            show_404(); //password not set
        } elseif ($_POST['pw'] != $pwd){
            //TODO: UNSAFE METHOD, REPLACE IT WITH DATABASE ACCESS
            echo 'authentication error';
            return;
        }
        $size = $this->input->post_get('size', true);
        $limit = $this->input->post_get('limit', true);

        $inv_record = new Invitation_record();
        for($i=0; $i<$size; $i++){
            $inv_record->generate_invitation($limit);
            $inv_record->push_to_db();
        }
        echo $size;
    }
}