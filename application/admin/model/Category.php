<?php

namespace app\admin\model;

use think\Model;

class Category extends Model {

	//列表
	public function index(){
		$list=db('category')
			->where('type',1)
			->field('id,code,value,summary')
			->select();
		foreach($list as $k=>$v){
			$list[$k]['_child']=db('category')
				->where(array('type'=>2,'pid'=>$v['id']))
				->field('id,value,summary')
				->select();
		}
		foreach($list as $k=>$v){
			if(!empty($v['_child'])){
				end($v['_child']);
    			$key_last = key($v['_child']);
				foreach($v['_child'] as $key=>$val){
					if($key_last==$key){
						$list[$k]['_child'][$key]['value']='└─'.$val['value'];
					}else{
						$list[$k]['_child'][$key]['value']='├─'.$val['value'];
					}
				}
			}
		}
		return $list;
	}

}