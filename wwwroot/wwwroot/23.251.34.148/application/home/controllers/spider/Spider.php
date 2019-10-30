<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23
 * Time: 14:50
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Spider extends Ancestor {
    function __construct()
    {
        parent::__construct();
        $this->load->model('spiders');
    }

    public function index(){
        $page=intval($this->input->post('page'));
        $data= $this->spiders->find(array(),$page);
        return $this->renderJson($data);
    }

}