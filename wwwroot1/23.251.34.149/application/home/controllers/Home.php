<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

    function  __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->model('sentence');
        $this->load->model('key');
        $this->load->driver('cache');

    }
	public function index()
	{

            require ( APPPATH.DIRECTORY_SEPARATOR."libraries".DIRECTORY_SEPARATOR."sphinxapi.php");
            $data=$this->key->find();
            $text = $data[0]['content'];
            $key=preg_replace('# #','',$text);
            $sum=$data[0]['total'];
            $klen=mb_strlen($key);
            $klen2=$klen/2;
            $sum2=$sum/$klen2;

        
        $array=array();
        $r=0;
        for ($i=0;$i<$klen2;$i++){
            $keyword= mb_substr($key,$r,2);

            $r=$r+2;
            $sphinx = new SphinxClient();
            $sphinx->SetServer('localhost',9312);
            $sphinx->setMatchMode(SPH_MATCH_ANY);//匹配模式 SPH_MATCH_ALL：完全匹配
            $result = $sphinx->query($keyword,'*');//*表示在所有索引里面进行搜索
            if(!$result['total']==0){
                $ids = implode(',',array_keys($result['matches']));
                $id2=explode(',',$ids);
                // var_dump($id2);exit();
                $idlen=count($id2)-1;
                // var_dump($idlen);exit;
                shuffle($id2);
                for($n=0; $n<$sum2; $n++){
                    $array[]= $id2[mt_rand(0, $idlen)];

                }
            }

        }
        // $array=array_filter($array);
        $array = array_flip($array);
        $array = array_flip($array);

        $res= $this->sentence->find($array);

        $res = array_column($res, NULL, 'content');
        $res = array_values($res);
            shuffle($res);
            $con=array();
            $con['title']=$data[0]['content'];
            $con['essay']='';
            foreach ($res as $item){
                $con['essay'].=$item['content'];
            }

           echo json_encode($con);



	}

}
