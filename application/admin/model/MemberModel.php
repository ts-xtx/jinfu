<?php

namespace app\admin\model;

use think\Model;

class MemberModel extends Model {

	//用户列表
	public function list($where){
		$list=db('member a')
			->join('member_info b','a.id=b.uid','left')
			->where($where)
			->field('a.id,a.nickname,a.headimg,a.sex,a.create_time,a.status')
			->order('a.id desc')
			->paginate();
		return $list;
	}

	//单个用户详情
	public function one($id){
		$cur=db('member a')
			->join('member_info b','a.id=b.uid')
			->where('id',$id)
			->find();
		return $cur;
	}

	//代理商列表
	public function agentList($where){
		$list=db('member a')
			->join('member_info b','a.id=b.uid','left')
			->where($where)
			->field('a.id,a.nickname,a.headimg,a.sex,a.create_time,a.totals,a.pid,a.grade,b.phone,a.status')
			->order('a.id desc')
			->paginate();
		return $list;
	}

	//代理商子级
	public function agentChild($id){
		$where=array(
			'grade'=>2,
			'pid'=>$id
		);
		$list=db('member a')
			->join('member_info b','a.id=b.uid','left')
			->where($where)
			->field('a.id,a.nickname,a.headimg,a.sex,a.create_time,a.totals,a.pid,a.grade,b.phone,a.status')
			->order('a.id desc')
			->paginate();
		return $list;
	}
}