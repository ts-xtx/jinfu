<?php

namespace app\admin\model;

use think\Model;

class Role extends Model {

	public function roleList($where){
		$list=db('role a')
			->join('admin b','a.create_id=b.id','left')
			->where($where)
			->field('a.id,a.name,a.summary,a.update_time,a.create_time,a.status,b.name as create_name')
			->order('a.id asc')
			->paginate();
		return $list;
	}

	public function roleJoin(){
		$filed='a.id,a.name';
		$where=array('a.type'=>1,'a.status'=>1);
		$menu=db('join a')
			->join('admin b','a.create_id=b.id','left')
			->where($where)
			->field($filed)
			->order('a.sort asc')
			->select();
		end($menu);
	    $key_last = key($menu);
		foreach($menu as $k=>$v){
			$where_child=array('a.type'=>2,'a.pid'=>$v['id'],'a.status'=>1);
			if($k!=$key_last){
				$menu[$k]['name']='├─'.$v['name'];
			}else{
				$menu[$k]['name']='└─'.$v['name'];
			}
			$menu[$k]['_child']=db('join a')
				->join('admin b','a.create_id=b.id','left')
				->where($where_child)
				->field($filed)
				->select();
			end($menu[$k]['_child']);
	    	$chlid_last = key($menu[$k]['_child']);
			foreach ($menu[$k]['_child'] as $key => $val) {
				if($key!=$chlid_last){
					$menu[$k]['_child'][$key]['name']='├─'.$val['name'];
				}else{
					$menu[$k]['_child'][$key]['name']='└─'.$val['name'];
				}
			}
		}
		return $menu;
	}

	//角色列表
	public function role(){
		$list=db('role')->where(array('status'=>1))->field('id,name')->select();
		return $list;
	}
}