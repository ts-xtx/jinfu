<?php
namespace app\module\controller;

class Error{

	// 空操作
	public function _empty(){
		$this->index();
	}

    public function index(){
    	echo '/error';
    }

}
