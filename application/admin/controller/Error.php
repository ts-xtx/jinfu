<?php
namespace app\admin\controller;

class Error{

	// 空操作
	public function _empty(){
		$this->index();
	}

    public function index(){
    	echo '/error';
    	return view();
    }

}