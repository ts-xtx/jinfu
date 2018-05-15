<?php
return [
    // 模板
    'view_replace_str'       => array(
        '__CSS__'	=> 'public/'.request()->module().'/css',
        '__JS__'	=> 'public/'.request()->module().'/js',
        '__IMG__' 	=> 'public/'.request()->module().'/images',
        '__STATIC__'=> 'public/static'
    ),

    'template'               => array(
    	'view_path'	=> 	'./template/'.request()->module().'/',
        'view_depr'	=>	'_',
        'taglib_begin' => '<',
        'taglib_end'   => '>',
    )
    
];
