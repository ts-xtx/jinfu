<?php

namespace app\admin\model;

use think\Model;

class NewsModel extends Model {
	//消息列表
	public function list($where){
		$list=db('news')
			->where($where)
			->order('id desc')
			->paginate();
		return $list;
	}
}