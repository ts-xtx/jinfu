<?php
	use think\Route;
	Route::domain('jinfu.cn','index');

	// 批量添加参数规则
	Route::pattern([
	    
	]);

	// 批量注册路由
	$routes=array(
		'/'=>'Index/index',
		'error'=>'error/index',
	);
	Route::rule($routes);