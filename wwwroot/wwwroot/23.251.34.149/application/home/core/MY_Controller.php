<?php
/**
 * Created by PhpStorm.
 * User: herry
 * Date: 2019/4/26
 * Time: 20:39
 */

class MY_Controller extends CI_Controller
{
    function  __construct()
    {
        parent::__construct();
    }

    function renderJson($data= array(), $status=200, $msg="ok"){
        $res = array("data"=>$data,"status"=>$status,"msg"=>$msg);
        echo json_encode($res);
    }

}

class Ancestor extends MY_Controller
{
    function  __construct()
    {
        parent::__construct();

    }

}