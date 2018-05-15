<?php

// 应用公共文件
function return_msg($msg='ok',$code=1,$result=array()){
	ob_clean();
	header('Content-Type:application/json; charset=utf-8');
	if($msg=='ok'){
		$code=0;
	}
	echo json_encode(array('code'=>$code,'msg'=>$msg,'result'=>$result),true);
	exit;
}

// curl post 请求
function post_curl($url,$data){
    if(empty($data)){
        $data = array();
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return array('errcode'=>-2);
    }
    $results = json_decode($result,true);
    curl_close($ch);
    return $results;
}

// curl get 请求
function get_curl($url){
    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $output = curl_exec($ch) ;
    curl_close($ch);
    $results = json_decode($output,true);
    return $results;
}

/**
 * 压缩图片
 */
function compress_img($src,$maxW=960){
    $path = './'.$src;
    if(preg_match('/(.jpg)|(.png)|(.jpeg)|(.gif)/i',$path)){
    	$image=\think\Image::open($path);
        $w = $image->width();
        if($w>$maxW){
            $h = $image->height();
            $image->thumb($maxW,$h,1)->save($path);
        }
    }
}

/**
 * 图片添加水印
 */
function wate_img($src,$text){
	$image = \think\Image::open($src);
	$image->text($text,'public/static/fonts/glyphicons-halflings-regular.ttf',20,'#ffffff')->save($src);
}

/**
 * 生成压缩图片
 */
function thumb_img($src,$width,$height){
	$src = trim($src,'.');
    if(!$src){ return ""; }
    if(preg_match('/(http:\/\/)|(https:\/\/)/i', $src)) {
        return $src;
    }
    $path = '.'.$src;
    if(!file_exists($path)){
        $path = './Public/static/imgs/default.png';
        $src = trim($path,'.');
    }
    ini_set('memory_limit', '1024M');
    ignore_user_abort(TRUE);
    set_time_limit(0);
    $temp = explode('/',$src);
    $count = count($temp);
    $temp[1] = 'thumb';
    $temp[$count-1] = preg_replace('/(.jpg)|(.png)|(.jpeg)|(.gif)/i','/'.$width.'x'.$height.'${0}',$temp[$count-1]);
   	$thumbSrc = join('/',$temp);
    $thumbPath = '.'.$thumbSrc;
    if(file_exists($thumbPath)){
        return $thumbSrc;
    }
    $temp = explode('/',$thumbPath);
    array_pop($temp);
    $dir = join('/',$temp);
    if(!is_dir($dir)){
        mkdir($dir, 0777, true);
    }
    $image=\think\Image::open($src);
    $image->thumb($width,$height)->save($thumbPath);
    return $thumbSrc;
}

/**
 * 检测是否为app
 * @return bool
 */
function is_app(){
    return strpos($_SERVER['HTTP_USER_AGENT'],'app')!==false?1:false;
}

/**
 * 检测是否为json字符串
 * @param $string
 * @return bool
 */
function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}