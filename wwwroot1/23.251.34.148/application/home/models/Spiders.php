<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/18
 * Time: 19:10
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Spiders extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function create($cate){
        return $this->db->insert('spider',$cate);
    }

    public function find($cate=array(),$page=0){
        $this->db->limit(10,$page*10);
        return $this->db->get_where('spider', $cate)->result_array();
    }

    public function delete($cate){
        return $this->db->delete('spider', $cate);
    }

    public function update($data,$cate){
        return $this->db->update('spider', $data, $cate);
    }

    public function exist($cate){
        $res = $this->db->get_where('spider', $cate)->num_rows();
        return $res>0;
    }

    public function sum($cate){
        return $this->db->get_where('spider', $cate)->num_rows();
    }


}