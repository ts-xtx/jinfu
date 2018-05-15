<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use app\index\model;
use think\Log;
use think\Validate;
use think\captcha\Captcha;

// 公共控制器
class Common extends Controller{
	// 初始化函数
	public function _initialize(){
		echo 'index';
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
