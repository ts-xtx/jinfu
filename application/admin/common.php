<?php
// 模块
function configMenu(){
	$id=session('admin.id');
	if($id==1){
		$menu=db('join')->where(array('type'=>1,'status'=>1))->field('id,name,ico,url,role_url')->order('sort asc')->select();
		foreach($menu as $k=>$v){
			$menu[$k]['sub']=db('join')->where(array('type'=>2,'pid'=>$v['id'],'status'=>1))->field('id,name,ico,url,role_url')->select();
		}
	}else{
		$role_id=session('admin.role_id');
		$join_id=db('role')->where(array('id'=>$role_id))->value('join_id');
		$menu=db('join')->where(array('type'=>1,'status'=>1,'id'=>array('in',$join_id)))->field('id,name,ico,url,role_url')->order('sort asc')->select();
		foreach($menu as $k=>$v){
			$menu[$k]['sub']=db('join')->where(array('type'=>2,'pid'=>$v['id'],'status'=>1,'id'=>array('in',$join_id)))->field('id,name,ico,url,role_url')->select();
		}
	}
	$menuStr='';
	foreach($menu as $k=>$v){
		if(empty($v['sub'])){
			$menuStr_ico='<span class="menu-arrow"></span>';
		}else{
			$menuStr_ico='<span class="menu-arrow"><i class="fa fa-chevron-right"></i></span>';
		}
		$menuStr.='<li class="has_sub">';
		if(empty($v['sub'])){
			if(!empty($v['role_url'])){
				$menuStr.='<a href="'.$v['role_url'].'" target="iframe" class="waves-effect">';
			}else{
				$menuStr.='<a href="javascript:;" target="iframe" class="waves-effect">';
			}
		}else{
			$menuStr.='<a href="javascript:;" class="waves-effect">';
		}
        $menuStr.='<i class="fa '.$v['ico'].'"></i>';
        $menuStr.=$menuStr_ico;
        $menuStr.='<span> '.$v['name'].' </span>';
        $menuStr.='</a>';
        if(!empty($v['sub'])){
        	$menuStr.='<ul class="list-unstyled">';
            foreach($v['sub'] as $val){
            	$menuStr.='<li><a target="iframe" href="'.$val['role_url'].'">'.$val['name'].'</a></li>';
            }
            $menuStr.='</ul>';
        }
        $menuStr.='</li>';
	}
	return $menuStr;
}

//模块管理列表
function menuList(){
	$id=session('admin.id');
	$filed='a.id,a.name,a.type,a.ico,a.url,a.role_url,b.realname as create_name,a.create_time,a.status';
	// if($id==1){
	$where=array('a.type'=>1,'a.status'=>1);
	// }else{
	// 	$role_id=session('admin.role_id');
	// 	$join_id=db('role')->where(array('id'=>$role_id))->value('join_id');
	// 	$where=array('a.type'=>1,'a.status'=>1,'a.id'=>array('in',$join_id));
	// }
	$menu=db('join a')
		->join('admin b','a.create_id=b.id','left')
		->where($where)
		->field($filed)
		->order('a.sort asc')
		->select();
	end($menu);
    $key_last = key($menu);
	foreach($menu as $k=>$v){
		// if($id==1){
		$where_child=array('a.type'=>2,'a.pid'=>$v['id'],'a.status'=>1);	
		// }else{
			// $where_child=array('a.type'=>2,'a.pid'=>$v['id'],'a.status'=>1,'a.id'=>array('in',$join_id));
		// }
		if($k!=$key_last){
			$menu[$k]['name']='├─'.$v['name'];
		}else{
			$menu[$k]['name']='└─'.$v['name'];
		}
		$menu[$k]['create_time']=date('Y-m-d H:i',$v['create_time']);
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
			$menu[$k]['_child'][$key]['create_time']=date('Y-m-d H:i',$val['create_time']);
		}
	}
	return $menu;
}

//查询条件重组
function searchWhere($default,$config){
	$cur=array();
	if($_POST && !empty($_POST)){
		$where=$default;
		$data=$_POST;
		if(!empty($config['like_field']) && $data['like_str']){
			$where[$config['like_field']]=array('like','%'.$data['like_str'].'%');
		}
		if(!empty($config['bet_time']) && $data['start-time'] && $data['end-time']){
			$like_field=explode('|',$config['bet_time']);
			$start_time = strtotime($data['start-time']);
            $end_time = strtotime($data['end-time']);
			foreach ($like_field as $v) {
				$where[$v]=array('between',array($start_time,$end_time));
			}
		}
		if(!empty($data['status']) && is_numeric($data['status']) && $data['status']>0){
			$where[$config['status']]=$data['status'];
		}
		if(!empty($data['grade']) && is_numeric($data['grade']) && $data['grade']>0){
			$where[$config['grade']]=$data['grade'];
		}
        session('where',serialize($where));
        session('data',serialize($data));
	}else{
		if(empty($_GET['page'])){
            session('where',serialize($default));
            session('data',serialize(array()));
        }
	}
	$cur['where'] = unserialize(session('where'));
    $cur['data'] = unserialize(session('data'));
	return $cur;
}

//radio 选择
function radioHtml($value,$status){
	$string='';
	if($value){
		foreach($status as $k=>$v){
			$string.='<label class="label_radio">';
			if($v['value']==$value){
				$string.='<input type="radio" checked class="'.$v['name'].'_radio" onclick="checkRadio(this);" value="'.$v['value'].'">';
				$string.='<span class="article_radio"></span>';
			}else{
				$string.='<input type="radio" class="'.$v['name'].'_radio" onclick="checkRadio(this);" value="'.$v['value'].'">';
				$string.='<span></span>';
			}
	        $string.=' <em>'.$v['str'].'</em>';
	        $string.='</label>';
		}
	}else{
		foreach($status as $k=>$v){
			$string.='<label class="label_radio">';
			if($v['default']){
				$string.='<input type="radio" checked class="'.$v['name'].'_radio" onclick="checkRadio(this);" value="'.$v['value'].'">';
				$string.='<span class="article_radio"></span>';
			}else{
				$string.='<input type="radio" class="'.$v['name'].'_radio" onclick="checkRadio(this);" value="'.$v['value'].'">';
				$string.='<span></span>';
			}
	        $string.=' <em>'.$v['str'].'</em>';
	        $string.='</label>';
		}
	}
	return $string;
}








