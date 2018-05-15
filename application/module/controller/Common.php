<?php
namespace app\module\controller;

use think\Controller;
use think\Db;
use app\module\model;
use think\Log;
use think\captcha\Captcha;

// 公共控制器
class Common extends Controller{
	protected $app_id;
	protected $appsecret;
	// 初始化函数
	public function _initialize(){
		global $loginId;
		$this->$app_id=config('weixin.app_id');
		$this->$appsecret=config('weixin.appsecret');
		$userid = session('user.id');
        $open_id = session('user.open_id');
    	if(!empty($userid)){
    		$loginId = $userid;
    	}else if(empty($open_id)){
    		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$url_back = urlencode($url);
	        self::checkAuth($url_back);
    	}else{
    		//有open-id，但是没有userid;
    	}
	}

	//微信Oauth 2.0鉴权
    protected function checkAuth($url_back){
        $redirect_uri='http://'.$_SERVER['SERVER_NAME'].'/Open/checkLogin.html';
        $redirect_uri=urlencode($redirect_uri);
        $appid = $this->app_id;
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state='.$url_back.'#wechat_redirect';
        redirect($url);
    }

	// 空操作
	public function _empty(){
		$this->redirect('/error');
	}

	// 文档验证函数
	public function validates($rule,$msg,$data){
		$validate = new Validate($rule, $msg);
		$result   = $validate->check($data);
		if($result){
			return 'ok';
		}else{
			return_msg($validate->getError());
		}
	}

}
