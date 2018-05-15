<?php
namespace app\admin\controller;

use think\Controller;
use think\Log;
use think\Validate;
use think\Cache;

// 公共控制器
class Common extends Controller{
	// 初始化函数
	public function _initialize(){
		$this->tesing();
        $this->_isLogin();
	}

	//检测id
	public function tesing(){
		$id=input('id');
		if(!empty($id) && !is_numeric($id)){
			$this->CommonError('（ID）参数错误');//模块未定义
		}
	}

	//空操作
    public function _empty(){
		$this->redirect('/error');
	}

    // 遍历登陆
    public function _isLogin(){
        global $adminId;
        $id=session('admin.id');
        if(is_numeric($id) && $id>0){
            $adminId=$id;
            $this->detectionPermissions();
        }else{
            $this->redirect('/login');
        }
    }

    //检测权限
	public function detectionPermissions(){
		$path=request()->controller().'/'.request()->action();
		$role=Cache::get('roles');
		$path=strtolower($path);
		if(!in_array($path,$role)){
			$this->CommonError('抱歉，您没有访问权限');//模块未定义
		}
	}

	//错误页面
	public function CommonError($str){
		if(request()->isAjax()){
			return_msg($str);
		}else{
			echo '<div style="width: 48%;height: 100px;margin: 0 25%;text-align: center;border: 1px solid #ccc;border-radius: 15px;line-height: 100px;background: white">
					'.$str.'
				</div>';exit;
		}
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

	//查询条件重组
	public function getWhere($default,$config){
		$where = searchWhere($default,$config);
		$this->assign('search',$where['data']);
		return $where['where'];
	}

	//分页跳转
	public function pageloca($toPage){
		if($_GET['page']>$toPage){
			$this->redirect('/'.request()->path().'?page='.$toPage);
		}
	}

	public function updateImg(){
		if(request()->method()=='POST'){
			$base64=input('post.img');
			$base64_image = str_replace(' ', '+', $base64);
			$date=date('Ymd');
		    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
		        if(in_array($result[2],array('jpg', 'gif', 'png', 'jpeg'))){
		            $image_name = uniqid().'.'.$result[2];
		        }else{
		        	return_msg('请上传图片！');
		        }
		        $image_file = "./upload/web/images/".$date.'/';
		        $image_file_url = "/upload/web/images/".$date.'/';
		        if(!is_dir($image_file)){
			        mkdir($image_file, 0777, true);
			    }
			    $image_file .= $image_name;
		        if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
		            return_msg($image_file_url.$image_name,0);
		        }else{
		            return_msg('保存失败!');
		        }
		    }else{
		        return_msg('文件损坏！');
		    }
		}else{
			return view('updateImg');
		}
	}

}
