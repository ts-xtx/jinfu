//ajax 提示提交
function subAjax(data,url,method,msg,type){
	type=type||'post';
	if(msg){
		layer.confirm(msg , {
			icon: 3,
			title:'消息提示',
		  	btn: ["确定","取消"] //按钮
		}, function(){
		  	$.ajax({
		  		url:url,
		  		data:data,
		  		type:type,
		  		dataType:"json",
		  		success:function(cus){
		  			method(cus);
		  		}
		  	})
		});
	}else{
		$.ajax({
	  		url:url,
	  		data:data,
	  		type:type,
	  		dataType:"json",
	  		success:function(cus){
	  			method(cus);
	  		}
	  	})
	}
}

//删除方法
function getDel(data,url,msg){
	msg=msg||'确定删除吗？';
	subAjax(data,url,function(){
		location.reload() 
	},msg);
}

//调用页面
function showLayerFim(title,url){
	layer.open({
	  	type: 2,
	  	title: title,
	  	shadeClose: true,
	  	shade: 0.5,
	  	area: ['90%', '90%'],
	  	content: url //iframe的url
	});
}

//显示一张图片，参数图片路径，标题
function layer_photo(src,title){
	src = src ? src : '';
	layer.photos({
	    photos: {
	    	  "title": title,
	    	  "id": 1,
	    	  "start": 0, 
	    	  "data": [ 
	    	    {
	    	      "alt": title,
	    	      "pid": 1,
	    	      "src": src,
	    	      "thumb": src 
	    	    }
	    	  ]
	    	},
	    shift: 5,
	    shade: [0.5,'#000'],
	    closeBtn: 0, //不显示关闭按钮
	});
}

//显示多张图片,参数父元素，img元素，标题
function layer_photos(parent,title){
	var src = [];
	$(parent).find('img').each(function(){
		var value = $(this).attr('src');
		if(src){
			var one = {
		        	 "alt": title,
		        	 "pid": 1,
		        	 "src": value,
		        	 "thumb": value 
	         	};
			src.push(one);
		}
	});
	if(src.length > 0){
		layer.photos({
			photos: {
				"title": title,
				"id": 1,
				"start": 0, 
				"data": src
			},
			shift: 5,
			shade: [0.5,'#000'],
			closeBtn: 1, //不显示关闭按钮
		});
	}else{
		layer.msg('图片获取错误');
	}
}

//图片上传
function uploadCropImage(title,method){
    var server = '/updateImg';
    layer.open({
        type: 2,
        title: title,
        closeBtn: 1, //不显示关闭按钮
        shade: [0],
        area: ['90%','90%'],
        offset: '50px',
        time: 0,
        anim: 2,
        scrollbar :false,
        maxmin : true,
        content: [server,'yes'],
        btn : ['确定','取消'],
        yes : function(index,layero){
            var url = $(layero).find('iframe')[0].contentWindow.cur_url;
            if(url == ''){
				layer.msg('您还未上传图片',{icon:2});
				return false;
			}
			var load='';
			$.ajax({
		  		url:"/updateImg",
		  		data:{img:url},
		  		type:"post",
		  		dataType:"json",
		  		beforeSend: function () {
	               	load = layer.load(0, {
			            shade: [0.5,'#000'],
			            time: 10*1000
			        });
	            },
		  		success:function(cus){
		  			if(cus.code==0){
		  				layer.close(load);
		  				method(cus.msg);
		  				layer.close(index);
		  			}else{
		  				layer.msg(cur.msg,{icon:5,time:1000});
		  			}
		  		}
		  	})
        }
    });
}

