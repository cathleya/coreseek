<?php
error_reporting(0);



if($_POST['info'])
{	
	$info=$_POST['info'] ?? '';
	$key=$_GET['key'] ?? $_POST['key'];
	$html = new article($info,$key);
	echo $html->Content();
}



class article
{
	// 需要解析的源代码
    protected $source = "";
	//关键词
	protected $keyword="";
	
	
	function __construct($source,$keyword)
    {
		$this->source = $source;
		$this->keyword = explode(',' ,$keyword);
		
	}
	public function Content()
    {
        if (!$this->source) return false;
		
		$text=str_replace(array("\r\n","\n","\r"),'[/h/]',$this->source); 
		$text= $this->keyword_lock($this->keyword,$text)['content'];
		
		if(preg_match('/(<p|<h1)/is',$text)){
			//去除标签
			$text=$this->html_filter($text);
			//伪原创
			$wyc= $this->wyc($text['info']);
			 //还原标签
			$html=$this->html_un($text['html'],$wyc);
			
		}else{
			$text=$this->txt_filter($text);
			
			$en_zh=$this->wyc($text['0']);
			
			$label=$text['1'];
			$html='';
			foreach ($en_zh as $age){
				$html.= $label['0'].$age.$label['1'];
			}
			
		}
		
		
		$js= $this->keyword_unlock($this->keyword,$html);


		return str_replace('[/h/]',PHP_EOL,$js); 

		
		
		
	}
	
	public function html_un($html,$info){
		preg_match_all("/\[\d+\]/i", $html, $match);
		for ($x=1; $x<=count($match[0]); $x++) {
			
			$html=&$html;
			
			$html=preg_replace("/\[$x\]/", $info[$x-1],$html ,1);
			
		}

		$a=['/\[div(.*?)\](.*?)\[\/div\]/','/\[\/br\/]/','/\[\/s2\/\](.*?)\[\/s2\/\]/is'];
		$b=['<div${1}>${2}</div>','<br>','<strong>${1}</strong>'];
		return preg_replace($a, $b, $html);
		
	}
	
	
	//txt删除
	public function txt_filter($text)
	{
		$eol_count=substr_count($text,'[/h/]');//换行次数
		
		$jh_count=substr_count($text,'。');//换行次数
		$div_count=substr_count($text,'</div>');//div次数
		$max_count=['eol'=>$eol_count,'jh'=>$jh_count,'div'=>$div_count];
		asort($max_count);//升序
		$key=array_keys($max_count);
			if($key['2']=='eol' and !empty($max_count[$key['2']])){
				$content=explode('[/h/]',$text);
				$label=[" ","\r\n"];
				
			}else if($key['2']=='jh' and !empty($max_count[$key['2']])){
				
				$content=explode('。',$text);
				$label=[' ',' '];
			}else if($key['2']=='div' and !empty($max_count[$key['2']])){
				
				$content=explode('</div>',$text);
				$label=['<div>','</div>'.PHP_EOL];
			}else{
				
				$content['0']=$text;
				$label=['',PHP_EOL];
			}

		return [$content,$label];
		
	}
	
	//HTML清理
	public function html_filter($text)
	{
	//过滤标签
			$a=['|<strong>(.*?)</strong>|','/<br(\/|)>/','/\s{1,}/','/<div(.*?)>(.*?)<\/div>/','/&nbsp;/'];
			$b=['[/s2/]${1}[/s2/]','[/br/]',' ','[div${1}]${2}[/div]',' '];
			$text=preg_replace($a, $b, $text);
			//保留html，剩下的删除
			$text=strip_tags($text,"<p><h1><h2><h3><h4><br><img>");  
			

			$content= preg_replace_callback("/<(.*?)>(.*?)<\/(.*?)>/is",array($this, 'html'),$text);
			//分割为数组
			 $content=explode(PHP_EOL ,$content);
			 $html='';
			 $info=array();
			 foreach ($content as $key=>$age) {
			   $fenge=explode('//分割//' ,$age);
			   
			   $html.=$fenge['0'];
			   $info[$key]=$fenge['1']??'';
			 }
			return ['html'=>$html,'info'=>$info];
			
	}
	
	private function html($matches){
			   static $i;
			   $i=$i+1;
			   if(strpos($matches[2],'img')){
				   $img=preg_replace('/<img(.*)(\/|)>/', '', $matches[2]);
				   $img1=preg_replace('/<img(.*)(\/|)>/', '<img${1}>', $matches[2]);
			   }else{
				   $img=$matches[2];
				   $img1='';
			   }
				//return '['.$matches[3].']  '.$matches[2].$i.'  [/'.$matches[3].']  '."\r\n";
				return '<'.$matches[1].'>'.$img1.'['.$i.']</'.$matches[3].'>//分割//'.$img."\r\n";
			}
	
	//关键词锁定
	public function keyword_lock($key='',$content='')
	{

		 foreach ($key as $id=>$age) {
			 $content=&$content;
			 $content=str_replace($age,"/[/k$id/]",$content);
             
				
		 }
		return ['key'=>$key,'content'=>$content];
	}
	
	//关键词解锁
	public function keyword_unlock($key='',$content='')
	{
	
		foreach ($key as $id=>$age) {
			
			 $content=&$content;
			 $content=str_ireplace("/[/k$id/]",$age,$content);
				
		 }
		return $content;
		
	}

	
	//二次翻译
	public function wyc($content)
	{
 
		$zh_en=$this->translate($content,'zh-CHS','en');
        
		if($zh_en){
			
           
			$wyc=$this->translate($zh_en,'en','zh-CHS');
			return $wyc;
		}else{
			
			//错误，重新执行
			sleep(2);//延迟三秒
			
			return $this->wyc($content);
		}
		
	}
	
	
	//翻译接口，必须 $info数组
	public function translate($info,$from,$to)
	{
		//数据分类
		foreach ($info as $age){
			$arr_info[]=[
			'id'=>'2-0','sendback'=>'text','text'=>strip_tags($age)
			];
			
		}
		
		//提交数据
		$data=[
			'appid'=>'front_translatepc',
			'uuid'=>'a54b2bec-'. $this->getRandomString('4') .'-4fe0-8s7x-'. $this->getRandomString('12'),
			'from_lang'=>$from,
			'to_lang'=>$to,
			'sendback'=>'1',
			'trans_frag'=>$arr_info
		];
		
		
		//数据json
		$data=json_encode($data,JSON_UNESCAPED_UNICODE);
		//伪装头部
		$ip="114.241.".rand(15,230).".".rand(15,230);
		$ext=[
		'referer'=>'http://translate.sogoucdn.com/pcvtsnapshotinne',
		'headers'=>[
			"CLIENT-IP"=>$ip,//伪造客户端IP
	         "X-FORWARDED-FOR"=>$ip,//伪造转发IP
			]
		];
		//发送数据
		$send=json_decode($this->send('http://translate.sogoucdn.com/commontranslate',$data,$ext),1);

		 if ($send['trans_result']['0']['success'])
		{
			//正确，返回信息
			//数据分类
			foreach ($send['trans_result'] as $age){
				$return_info[]=$age['trans_text'];
			}
			return $return_info;
			
		} else {
			//错误，重新执行
			sleep(2);//延迟三秒
			return $this->translate($info,$from,$to);
		} 
	}
	
	
	
	
	
	
	
	
	//随机字母+数字
	public function getRandomString($len, $chars=null)
	{
		if (is_null($chars))
		{
			$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		}  
		mt_srand(10000000*(double)microtime());
		for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++)
		{
			$str .= $chars[mt_rand(0, $lc)];  
		}
    return $str;
	}
	//curl发送器
	public function send($url,$req_data,$ext_data=[]){ 


	    $browser_agent=isset($ext_data['agent'])?$ext_data['agent']:$_SERVER['HTTP_USER_AGENT'];
	   
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 跳过证书检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
	  
	    curl_setopt($curl, CURLOPT_USERAGENT, $browser_agent);
	    if (isset($ext_data['referer'])) {
	       curl_setopt($curl, CURLOPT_REFERER, $ext_data['referer']);
	    }
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	    curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $req_data);
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //timeout on connect
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30);//timeout on response
	    if (isset($ext_data['headers'])) {
	       curl_setopt($curl, CURLOPT_HTTPHEADER, $ext_data['headers']); 
	    }else{
	       curl_setopt($curl, CURLOPT_HEADER, 0); 
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($curl);
	    //如果有错误，则返回错误信息
	    if($result === FALSE ){
	       return "CURL Error:".curl_error($curl);
	     }
	    curl_close($curl);
	    return $result;
	}



}