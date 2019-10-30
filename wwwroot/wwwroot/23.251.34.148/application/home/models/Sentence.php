<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17
 * Time: 16:47
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Sentence extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create($cate){
        return $this->db->insert('sentence',$cate);
    }

    public function find($ids){

        $this->db->select('content');
        $this->db->from('sentence');
        $this->db->where_in('id', $ids);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete($cate){
        return $this->db->delete('sentence', $cate);
    }

    public function update($data,$cate){
        $this->db->update('sentence', $data, $cate);
        return $this->db->affected_rows();
    }

    public function exist($cate){
        $res = $this->db->get_where('sentence', $cate)->num_rows();
        return $res>0;
    }

    public function sum($cate=array()){
        return $this->db->get_where('sentence', $cate)->num_rows();
    }


}