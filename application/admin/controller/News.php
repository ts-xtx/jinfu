<?php
namespace app\admin\controller;
use app\admin\controller\Common;
//代理商管理
class News extends Common{

	//代理商列表
	public function list(){
		$default=array();
        $config=array('like_field'=>'title','status'=>'type','bet_time'=>'create_time');
        $where=$this->getWhere($default,$config);
        $list=model('NewsModel')->list($where);
        $this->pageloca($list->lastPage());
        $page = $list->render();
        $this->assign('page',$page);
        $this->assign('list',$list->toArray());
        return view('list');
	}

	//编辑页面
	public function doneNews(){
		$id=input('id');
        if(!empty($id)){
            $cur=db('news')->where(array('id'=>$id))->find();
        }
        $type=array(
            array('value'=>3,'defualt'=>1,'str'=>'所有','name'=>'type'),
            array('value'=>2,'str'=>'二级代理','name'=>'type'),
            array('value'=>1,'str'=>'一级代理','name'=>'type')
        );
        $this->assign('type',$type);
        $this->assign('cur',$cur);
        return view('doneNews');
	}

	//编辑方法
	public function doNews(){
		global $adminId;
		$id=input('id');
		$data=input('post.');
		unset($data['id']);
        if(empty($id)){
           	$data['uid']=$adminId;
           	$data['create_time']=time();
           	$res=db('news')->insert($data);
           	if($res){
           		return_msg('添加成功',0);
           	}else{
           		return_msg('添加错误');
           	}
        }else{
        	$res=db('news')->where('id',$id)->update($data);
        	return_msg('修改成功',0);
        }
	}

	//删除方法
	public function delNews(){
		$id=input('id');
		$ids=input('ids');
        if(!empty($id)){
            $res=db('news')->where('id',$id)->delete();
        }
        if(!empty($ids)){
        	$res=db('news')->where(array('id'=>array('in',$ids)))->delete();
        }
		if($res){
			return_msg('ok');
		}else{
			return_msg('未知错误！');
		}
	}

}