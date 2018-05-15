<?php
namespace app\admin\controller;

use app\admin\controller\Common;
use think\Paginator;
use app\admin\model\System;
use app\admin\model\Sategory;

class Systemi extends Common{

    public function journal(){
    	$default=array();
    	$config=array('like_field'=>'b.name|b.realname|b.phone','bet_time'=>'a.create_time');
    	$where=$this->getWhere($default,$config);
    	$list=model('System')->journal($where);
    	$this->pageloca($list->lastPage());
        $page=$list->render();
        $this->assign('page',$page);
        $this->assign('list',$list->toArray());
    	return view('journal');
    }

/*
    模块名：分类管理
    模块url：Systemi/category
    模块路由：system/category
    子路由：system/delCategory,system/doneCategory,system/doCategory

    //分类管理
    public function category(){
        $list=model('Category')->index();
        $this->assign('list',$list);
    	return view('category');
    }

    //分类管理
    public function doneCategory(){

    }

    //分类管理
    public function doCategory(){

    }

    //分类管理
    public function delCategory(){
    	
    }
*/

}
