<?php
namespace app\admin\controller;

use app\admin\controller\Common;


class Index extends Common{

    public function index(){
    	$admin=session('admin');
    	$this->assign('admin',$admin);
        return view('index');
    }

    //修改用户密码
    public function pwd(){
    	if(request()->method()=='POST'){
    		global $adminId;
    		$admin_pwd=db('admin')->where('id',$adminId)->value('pwd');
    		$data=input('post.');
    		if($admin_pwd!=md5($data['ypwd'])){
    			return_msg('原始密码错误！');
    		}else if($admin_pwd==md5($data['pwd'])){
    			return_msg('新密码与原密码一致！');
    		}else{
    			$res=db('admin')->where('id',$adminId)->update(array('pwd'=>md5($data['pwd'])));
    			if($res){
    				session(null);
    				return_msg('修改成功，请重新登录！',0);
    			}else{
    				return_msg('未知错误，修改失败！');
    			}
    		}
    	}else{
    		return view('pwd');
    	}
    }

	public function welcome(){
        return view('welcome');
    }

}
