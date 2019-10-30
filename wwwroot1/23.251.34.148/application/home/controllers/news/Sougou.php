<?php
/**
 * Created by PhpStorm.
 * User: herry
 * Date: 2019/4/30
 * Time: 18:59
 */

class Sougou extends Ancestor
{
    function  __construct()
    {
        parent::__construct();

    }

    function index(){
        echo 123;
    }

    function list1(){
        $this->load->view("news/sougou/list.html");
    }

    function show(){
        $this->load->view("news/sougou/show.html");
    }


}