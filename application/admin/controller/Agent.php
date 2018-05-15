<?php
namespace app\admin\controller;
use app\admin\controller\Common;
//代理商管理
class Agent extends Common{

	//代理商列表
	public function list(){
		$default=array('a.grade'=>array('in','1,2'));
        $config=array('like_field'=>'a.nickname|b.phone','bet_time'=>'a.create_time','status'=>'a.status','grade'=>'a.grade');
        $where=$this->getWhere($default,$config);
        $list=model('MemberModel')->agentList($where);
        $this->pageloca($list->lastPage());
        $page=$list->render();
        $this->assign('page',$page);
		$this->assign('list',$list->toArray());
		return view('list');
	}

	//父级 & 自己
	public function showUser(){
		$id=input('id');
		if(empty($id)){
			$this->CommonError('（ID）参数错误');
		}else{
			$cur=model('MemberModel')->one($id);
			$this->assign('cur',$cur);
			return view('showUser');
		}
	}

	//禁用用户
    public function delUser(){
        $data=input('post.');
        if(empty($data['id']) && !in_array($data['flag'],array('1','2'))){
            return_msg('参数错误');
        }else{
            if($data['flag']==2){
                $yes_str='禁用成功';
                $no_str='禁用失败，未知错误';
            }else{
                $yes_str='开启成功';
                $no_str='开启失败，未知错误';
            }
            $res=db('member')->where('id',$data['id'])->update(array('status'=>$data['flag']));
            if($res){
                return_msg($yes_str,0);
            }else{
                return_msg($no_str);
            }
        }
    }

	//查看子级
	public function childUser(){
		$id=input('id');
		if(empty($id)){
			$this->CommonError('（ID）参数错误');
		}else{
			$list=model('MemberModel')->agentChild($id);
	        $this->pageloca($list->lastPage());
	        $page=$list->render();
	        $this->assign('page',$page);
	        $this->assign('list',$list->toArray());
			return view('childUser');
		}
	}

	//申请列表
	public function applyList(){
		return view('applyList');
	}
	
	//等级管理
	public function grade(){
		return view('grade');
	}

}