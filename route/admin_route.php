<?php
	use think\Route;
	Route::domain('admin.jinfu.cn','admin');
	Route::domain('192.168.0.118','admin');
	// 批量添加参数规则
	Route::pattern([
	    
	]);
	$join=db('join')->where(array('status'=>1,'url'=>array('neq','')))->field('url,role_url,subnode')->select();
	$routes_my=array();
	foreach ($join as $v) {
		$routes_my[$v['role_url']]=$v['url'];
		$role_url=explode(',', $v['subnode']);
		if(count($role_url)>1){
			foreach($role_url as $k=>$v){
				if(!empty($v)){
					$routes_my[$v]=$v;
				}
			}
		}
	}
	// 批量注册路由
	$routes=array(
		'/'=>'index/index',
		'error'=>'error/index',
		'updateImg'=>'common/updateImg',
		'verify'=>'login/verify',
		'login'=>'login/login',
		'doLogin'=>'login/doLogin',
		'exitLogin'=>'login/exitLogin',
		'index/pwd'=>'index/pwd',
		'index/resetPwd'=>'index/resetPwd',
		'system/journal'
	);
	$routes=array_merge($routes,$routes_my);
	Route::rule($routes);