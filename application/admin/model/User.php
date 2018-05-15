<?php

namespace app\admin\model;

use think\Model;

class User extends Model {

	//角色列表
	public function list($where){
		$list=db('admin a')
			->join('role b','a.role_id=b.id')
			->where($where)
			->field('a.id,a.name,a.realname,a.phone,a.sex,a.headimg,b.name role_name,a.last_time,a.status')
			->order('id asc')
			->paginate();
		return $list;
	}

	//单个详情
	public function single($id){
		$cur=db('admin a')
			->join('role b','a.role_id=b.id','left')
			->where('a.id',$id)
			->field('a.id,a.name,a.headimg,a.realname,a.phone,a.sex,a.summary,a.last_time,a.last_ip,a.create_time,a.status,b.name as role_name,b.id as role_id')
			->find();
		return $cur;
	}

}