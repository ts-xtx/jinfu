<?php
namespace app\admin\controller;
use app\admin\controller\Common;
use think\Paginator;
use app\admin\model\Role;
use app\admin\model\User;

class Power extends Common{

    //权限管理
    public function join(){
    	$list=menuList();
        $this->assign('list',$list);
        return view('join');
    }

    //删除模块
    public function delJoin(){
        $id=input('post.id');
        $ids=input('post.ids');
        if(!empty($id)){
            $cur=db('join')->where(array('id'=>array('in',$id)))->update(array('status'=>2));
        }
        if(!empty($ids)){
            $cur=db('join')->where(array('id'=>array('in',$ids)))->update(array('status'=>2));
        }
        if($cur){
            return_msg('ok');
        }else{
            return_msg('未知错误');
        }
    }

    //编辑 & 修改 - 页面
    public function doneJoin(){
        $id=input('id');
        if(!empty($id)){
            $cur=db('join')->where(array('id'=>$id))->find();
        }
        $cur['subnode']= rtrim($cur['subnode'], ",");
        $cur['subnode']= ltrim($cur['subnode'], ",");
        $menu=db('join')
            ->where(array('type'=>1,'status'=>1,'id'=>array('neq',1)))
            ->field('id,name')
            ->select();
        $this->assign('menu',$menu);
        $this->assign('cur',$cur);
        return view('doneJoin');
    }

    //编辑 & 修改
    public function doJoin(){
        global $adminId;
        $id=input('id');
        $data=input('post.');
        unset($data['id']);
        if(!empty($data['subnode'])){
            $data['subnode']=','.$data['subnode'].',';
        }
        if(empty($id)){
            $data['create_time']=time();
            $data['create_id']=$adminId;
            $sort=db('join')->order('sort desc')->value('sort');
            if($data['type']==1){
                $data['sort']=$sort+1;
            }
            $res=db('join')->insert($data);
            if($res){
                return_msg('添加成功',0);
            }else{
                return_msg('未知错误');
            }
        }else{
            db('join')->where(array('id'=>$id))->update($data);
            return_msg('修改成功',0);
        }
    }

    //模块排序
    public function menuSort(){
        $id = input('post.id');
        $flag = input('post.flag');
        if(!is_numeric($id) || $id<1 || !in_array($flag,array(1,2))){
            return_msg(1000,'信息出错！');
        }else{
            $sort=db('join')->where(array('id'=>$id))->value('sort');
            if($flag==1){
                if($sort==1){
                    return_msg('已经是第一了哦');
                }else{
                    $topSort=db('join')
                        ->where(array('sort'=>array('lt',$sort),'status'=>1,'type'=>1))
                        ->order('sort desc')
                        ->field('id,sort')
                        ->find();
                    if(!empty($topSort)){
                        $res=db('join')->where(array('id'=>$id))->update(array('sort'=>$topSort['sort']));
                        $res2=db('join')->where(array('id'=>$topSort['id']))->update(array('sort'=>$sort));
                    }else{
                        return_msg('ok');
                    }
                }
            }else{
                $topSort=db('join')
                        ->where(array('sort'=>array('gt',$sort),'status'=>1,'type'=>1))
                        ->order('sort asc')
                        ->field('id,sort')
                        ->find();
                if(!empty($topSort)){
                    $res=db('join')->where(array('id'=>$id))->update(array('sort'=>$topSort['sort']));
                    $res2=db('join')->where(array('id'=>$topSort['id']))->update(array('sort'=>$sort));
                }else{
                    return_msg('ok');
                }
            }
            if($res && $res2){
                return_msg('ok');
            }else{
                return_msg(5000,'服务器走丢啦！');
            }
        }
    }

    //角色管理
    public function role(){
        $default=array();
        $config=array('like_field'=>'a.name');
        $where=$this->getWhere($default,$config);
        $roleModel = model('Role');
        $list = $roleModel->roleList($where);
        $this->pageloca($list->lastPage());
        $page = $list->render();
        $this->assign('page',$page);
        $this->assign('list',$list->toArray());
        return view('role');
    }

    //编辑权限页面
    public function doneRoleJoin(){
        $roleModel = model('Role');
        $list = $roleModel->roleJoin();
        $id=input('id');
        if(!empty($id)){
            $join_id=db('role')->where(array('id'=>$id))->value('join_id');
            $join_id=explode(',',$join_id);
            foreach($join_id as $k=>$v){
                foreach($list as $key=>$val){
                    if($v==$val['id']){
                        $list[$key]['is_']="1";
                    }else{
                        foreach($val['_child'] as $keys=>$value){
                            if($v==$value['id']){
                                $list[$key]['_child'][$keys]['is_']="1";
                            }
                        }
                    }
                }
            }
        }
        $this->assign('list',$list);
        return view('doneRoleJoin');
    }

    //编辑权限
    public function doRoleJoin(){
        $id=input('post.id');
        if($id==1){
            return_msg('初始化超级管理员禁止配置！');
        }
        $join_id=input('post.join_id');
        if(empty($id) || empty($join_id)){
            return_msg('参数错误！');
        }else{
            db('role')->where(array('id'=>$id))->update(array('join_id'=>$join_id));
            return_msg('配置成功',0);
        }
    }

    //编辑角色页面
    public function doneRole(){
        $id=input('id');
        if(!empty($id)){
            $cur=db('role')->where(array('id'=>$id))->find();
        }
        $this->assign('cur',$cur);
        return view('doneRole');
    }

    //编辑角色
    public function doRole(){
        global $adminId;
        $id=input('post.id');
        $data=input('post.');
        unset($data['id']);
        if(empty($id)){
            $data['create_time']=time();
            $data['create_id']=$adminId;
            $res=db('role')->insert($data);
            $str='添加';
        }else{
            if($id==1){
                return_msg('初始化超级管理员禁止配置！');
            }
            $data['update_time']=time();
            $res=db('role')->where('id',$id)->update($data);
            $str='编辑';
        }
        if($res){
            return_msg($str.'成功',0);
        }else{
            return_msg($str.'失败,未知错误',0);
        }
    }

    //删除角色
    public function delRole(){
        $id=input('post.id');
        if(empty($id)){
            $this->CommonError('参数错误（id）');
        }else{
            $cur=db('role')->where('id',$id)->delete();
            if($cur){
                return_msg('ok');
            }else{
                return_msg('未知错误');
            }
        }
    }

    //管理员管理
    public function user(){
        $default=array('b.status'=>'1');
        $config=array('like_field'=>'a.name|a.phone|a.realname','bet_time'=>'a.create_time');
        $where=$this->getWhere($default,$config);
        $model=model('User');
        $list=$model->list($where);
        $this->pageloca($list->lastPage());
        $page=$list->render();
        $this->assign('page',$page);
        $this->assign('list',$list->toArray());
        return view('user');
    }

    //管理员详情页
    public function showUser(){
        $id=input('id');
        if(empty($id)){
            $this->CommonError('参数错误（id）');
        }else{
            $model=model('User');
            $cur=$model->single($id);
            if(empty($cur)){
                $this->CommonError('管理员未找到');
            }else{
                $this->assign('cur',$cur);
                return view('showUser');
            }
        }
    }

    //编辑管理员页面
    public function doneUser(){
        $id=input('id');
        if(!empty($id)){
            $cur=model('User')->single($id);
            $this->assign('cur',$cur);
        }
        $role_list=model('Role')->role();
        $this->assign('role_list',$role_list);
        $status=array(
            array('value'=>1,'default'=>1,'str'=>'显示','name'=>'status'),
            array('value'=>2,'str'=>'隐藏','name'=>'status')
        );
        $sex=array(
            array('value'=>1,'defualt'=>1,'str'=>'男','name'=>'sex'),
            array('value'=>2,'str'=>'女','name'=>'sex')
        );
        $this->assign('sex',$sex);
        $this->assign('status',$status);
        return view('doneUser');
    }

    //编辑管理员
    public function doUser(){
        global $adminId;
        $id=input('id');
        $data=input('post.');
        unset($data['id']);
        if(empty($id)){
            $is_phone=db('admin')->where(array('phone'=>$data['phone']))->value('id');
            if(!empty($is_phone)){
                return_msg('手机号已经注册过了哦');
            }
            $data['create_time']=time();
            $data['create_id']=$adminId;
            $data['pwd']=md5($data['pwd']);
            $res=db('admin')->insert($data);
            if($res){
                return_msg('添加成功',0);
            }else{
                return_msg('添加失败,未知错误',0);
            }
        }else{
            $is_phone=db('admin')->where(array('phone'=>$data['phone'],'id'=>array('neq',$id)))->value('id');
            if(!empty($is_phone)){
                return_msg('手机号已经注册过了哦');
            }
            $res=db('admin')->where('id',$id)->update($data);
            return_msg('编辑成功',0);
        }
    }

    //删除管理员
    public function delUser(){
        $id=input('id');
        if(empty($id)){
            return_msg('检查到空参数（ID）');
        }else{
            if($id==1){
                return_msg('初始化管理员静止删除');
            }
            $res=db('admin')->where('id',$id)->delete();
            if($res){
                return_msg('删除成功',0);
            }else{
                return_msg('删除失败');
            }
        }
    }

    //重置密码
    public function resetPwdUser(){
        $id=input('id');
        if(empty($id)){
            $this->CommonError('参数错误（id）');
        }else{
            $data=array(
                'pwd'=>md5('123456')
            );
            $res=db('admin')->where('id',$id)->update($data);
            return_msg('ok');
        }
    }

}
