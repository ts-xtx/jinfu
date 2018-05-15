<?php

namespace app\admin\model;

use think\Model;

class System extends Model {

	//系统日志
	public function journal($where){
		$list=db('logs a')
			->join('admin b','a.uid=b.id')
			->where($where)
			->field('a.create_time,a.ip,a.operation,b.name,b.realname')
			->order('a.id desc')
			->paginate();
		return $list;
	}

}