<?php
/**
 * Created by PhpStorm.
 * User: herry
 * Date: 2019/4/30
 * Time: 21:01
 */


defined('BASEPATH') OR exit('No direct script access allowed');


$config['socket_type'] = 'tcp'; //`tcp` or `unix`
$config['socket'] = '/var/run/redis.sock'; // in case of `unix` socket type
$config['host'] = '127.0.0.1';
$config['password'] = NULL;
$config['port'] = 6379;
$config['timeout'] = 3600;

