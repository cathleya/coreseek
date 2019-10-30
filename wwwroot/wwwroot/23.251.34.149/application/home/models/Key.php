<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17
 * Time: 16:47
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Key extends CI_Model
{


    private $db;
    function __construct()
    {
        parent::__construct();

        $this->db=$this->load->database('title', TRUE);
    }

    public function create($cate){
        return $this->db->insert('title',$cate);
    }

    public function find($cate=array()){

        $this->db->order_by('RAND()');
        $this->db->limit(1,0);
        return $this->db->select('total,content')->get_where('title', $cate)->result_array();
    }

    public function delete($cate){
        return $this->db->delete('title', $cate);
    }

    public function update($data,$cate){
        $this->db->update('title', $data, $cate);
        return $this->db->affected_rows();
    }

    public function exist($cate){
        $res = $this->db->get_where('title', $cate)->num_rows();
        return $res>0;
    }

    public function sum($cate=array()){
        return $this->db->get_where('title', $cate)->num_rows();
    }


}