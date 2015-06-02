<?php
/**
 * This class handles data model of invitation code
 * User: Bowei
 * Date: 2015/6/2
 * Time: 11:13
 */
require_once(APPPATH . '../'. SYSDIR. '/core/Model.php');

class Invitation_record extends CI_Model {
    const TABLE_NAME = 'invitation';

    public $code;
    public $count;
    public $limit;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function generate_invitation($limit, $code = null){
        if (null == $code){
            $full_md5 = md5(time() + rand());
            $code = substr($full_md5, rand(0,20), 6);
        }

        $this->code = $code;
        $this->count = 0;
        $this->limit = $limit;
    }

    public function push_to_db(){
        $this->db->insert($this->db->dbprefix(Invitation_record::TABLE_NAME), $this);
    }

    /**
     * Check availability of invitation code
     * Returns: 0:Available; -1:Invalid code; -2:Limitation exceed
     */
    public function check_availability($code = null){
        if(null == $code)
            $code = $this->code;
        $this->db->where('code', $code);
        $query = $this->db->get($this->db->dbprefix(Invitation_record::TABLE_NAME));

        if($query->num_rows() > 0){
            $row = $query->row();
            if($row->count < $row->limit)
                return 0;
            else
                return -2;
        } else {
            return -1;
        }
    }

    public function get_by_code($code){
        $this->db->where('code', $code);
        $query = $this->db->get($this->db->dbprefix(Invitation_record::TABLE_NAME));
        if($query->num_rows() > 0) {
            $row = $query->row();
            $this->code = $code;
            $this->limit = $row->limit;
            $this->count = $row->count;
            return 0;
        } else {
            return -1;
        }
    }

    public function increase_count(){
        $this->count ++;
        $this->db->set('count', $this->count);
        $this->db->where('code', $this->code);
        return $this->db->update($this->db->dbprefix(Invitation_record::TABLE_NAME));
    }

}