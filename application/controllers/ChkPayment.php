<?php
/**
 * Created by PhpStorm.
 * User: ioran_000
 * Date: 2015/5/9
 * Time: 21:28
 */
require_once(APPPATH . 'models/Hit_record.php');


class Chkpayment extends CI_Controller{
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

    function index_get(){
        $this->load->view('checkpayment');
    }

    function index_post(){
        $ret_data = array();
        if(!$this->check_authority()){
            $ret_data['status'] = -1;
            $ret_data['reason'] = 'Auth failed.';
        } elseif(!isset($_POST['payment_info'])){
            $ret_data['status'] = -2;
            $ret_data['reason'] = 'Broken POST submit';
        } else {
            $pay_info = $this->input->post('payment_info', true);
            if(empty($pay_info)){
                $ret_data['status'] = -2;
                $ret_data['reason'] = "Empty payment_info";
            } else {
                $hit_record = new Hit_record();
                $p_info_ary = $hit_record->getPayStatusByPayInfo($pay_info);
                $ret_data['status'] = 0;
                $ret_data['data'] = json_encode($p_info_ary);
            }
        }
        echo json_encode($ret_data);
    }

    function check_authority(){
        if(!isset($_SESSION[KEY_PASS]))
            return false;
        if($_SESSION[KEY_PASS] == '1')
            return true;
        return false;
    }
}