<?php
namespace app\admin\controller;

use think\Controller;
use think\captcha\Captcha;
use think\Cache;

class Login extends Controller{

	//登陆页面
    public function login(){
        $view_title=\think\Config::get('view_title');
        $this->assign('view_title',$view_title);
        return view('login');
    }

    // 生成生成验证码
    public function verify()
    {
        ob_clean();
        $config = [
            'imageH'   => 40,
            'imageW'   => 120,
            'length'   => 3,
            'fontttf'  => '5.ttf',
            'fontSize' => 16
        ];
        $v = new Captcha($config);
        return $v->entry();
    }

    //登出
    public function exitLogin(){
        session(null);
        $this->redirect('/login');
    }

    //验证登陆
    public function doLogin(){
    	$name=input('username');
    	$pwd=input('password');
    	$code=input('verify_code');
    	if(empty($name) || empty($pwd) || empty($code)){
    	    return_msg('检测到空函数！');
        }
        $captcha = new Captcha();
    	if($captcha->check($code)){
    	    $where=array('name'=>$name,'pwd'=>md5($pwd));
    	    $admin=db('admin')
                ->where($where)
                ->field('id,name,headimg,realname,role_id,status')
                ->find();
            if(empty($admin)){
                $where=array('phone'=>$name,'pwd'=>md5($pwd));
                $admin=db('admin')
                    ->where($where)
                    ->field('id,name,headimg,realname,role_id,status')
                    ->find();
            }
            if(empty($admin)){
                return_msg('用户名或密码错误，请重试！');
            }else if($admin['status']!=1){
                return_msg('角色已被禁用，请联系管理员！');
            }else{
                $role=db('role')
                    ->where(array('id'=>$admin['role_id']))
                    ->field('name,join_id,status')
                    ->find();
                if(empty($role['status'])){
                    return_msg('角色已被删除，请联系管理员！');
                }else if($role['status']!=1){
                    return_msg('角色已被禁用，请联系管理员！');
                }else if(empty($role['join_id'])){
                    return_msg('角色暂无权限，请联系管理员！');
                }else{
                    $admin['role_name']=$role['name'];
                    session('admin',$admin);
                    $this->loginLog();
                    $this->generatingPermissions();
                }
            }
        }else{
    	    return_msg('验证码错误！');
        }
    }

    //登录信息
    public function loginLog(){
        $adminId=session('admin.id');
        $last_time = time();
        $ip=request()->ip();
        $data=array(
            'uid'=>$adminId,
            'create_time'=>$last_time,
            'ip'=>$ip,
            'operation'=>'登录',
        );
        db('logs')->insert($data);
        $data_user=array(
            'last_time'=>$last_time,
            'last_ip'=>$ip
        );
        db('admin')->where('id',$adminId)->update($data_user);
    }

    //权限
    public function generatingPermissions(){
        $adminId=session('admin.id');
        $field='url,subnode';
        if($adminId==1){
            $join=db('join')->where(array('status'=>1))->field($field)->select();
        }else{
            $role_id=session('admin.role_id');
            $role_id=10;
            $join_id=db('role')->where(array('id'=>$role_id))->value('join_id');
            $join=db('join')
                ->where(array('type'=>1,'status'=>1,'id'=>array('in',$join_id)))
                ->field('id,url,subnode')
                ->order('sort asc')
                ->select();
            foreach($join as $k=>$v){
                $join_child=db('join')
                    ->where(array('type'=>2,'pid'=>$v['id'],'status'=>1,'id'=>array('in',$join_id)))
                    ->field($field)
                    ->select();
                $join=array_merge($join,$join_child);
            }
        }
        $roles=array();
        foreach ($join as $k => $v) {
            if(!empty($v['url'])){
                $roles[]=$v['url'];
            }
            if(!empty($v['subnode'])){
                $subnode=explode(',',$v['subnode']);
                unset($subnode[0]);
                unset($subnode[count($subnode)]);
                $roles=array_merge($roles,$subnode);
            }
        }
        $defualt_role=array(
            'index/index',
            'error/index',
            'common/updateImg',
            'login/verify',
            'login/login',
            'login/doLogin',
            'login/exitLogin',
            'index/pwd',
            'index/resetPwd'
        );
        if(empty($roles)){
            return_msg('您还未设置权限，请联系管理员');
        }
        $roles=array_merge($roles,$defualt_role);
        $a_str=implode(',',$roles);
        $a_lowercase=strtolower($a_str);
        $roles=explode(',',$a_lowercase);
        Cache::set('roles',$roles);
        return_msg('ok');
    }

}
