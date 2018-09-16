document.documentElement.style.fontSize = document.documentElement.clientWidth*20/750 + 'px';
window.onresize = function(){
    document.documentElement.style.fontSize = document.documentElement.clientWidth*20/750 + 'px';
}
function remToPx(value){
	return value*document.documentElement.clientWidth*20/750;
}
$(function($){
	FastClick.attach(document.body);
});
function toDou(num){
	return num > 9 ? num + '' : '0' + num;
}
function getFormData(ele){
	var d = {};
	var data = $(ele).serializeArray();
	$.each(data, function() {
	  	d[this.name] = this.value;
	  });
	return d;
}
function checkPhone(phone){
	return /^[1][3,4,5,6,7,8][0-9]{9}$/i.test(phone);
}
function showAlert(msg, callback){
	if(msg instanceof Array){
		if(msg.length == 2){
			msg = msg[0] + '<br/>' + msg[1];
		}else{
			msg = msg[0];
		}
	}
	if($('#alert-wrap').length > 0){
		$('#alert-wrap').remove();
	}
	var html = '<div id="toast-wrap"><div class="toast"><i></i><span>'+msg+'</span></div></div>';
	$('body').append(html);
}
function showToast(msg, time, delay){
	if(msg instanceof Array){
		if(msg.length == 2){
			msg = msg[0] + '<br/>' + msg[1];
		}else{
			msg = msg[0];
		}
	}
	if(!time) time=1000;
	if(!delay) delay = 500;
	if($('#toast-wrap').length > 0){
		$('#toast-wrap').remove();
	}
	var html = '<div id="toast-wrap"><div class="toast"><i></i><span>'+msg+'</span></div></div>';
	$('body').append(html);
	var tranS = time + 'ms all ease '+delay+'ms';
	$('#toast-wrap .toast').css({'opacity': 1, 'transition':tranS, '-webkit-transition':tranS});
	setTimeout(function(){
		$('#toast-wrap .toast').css('opacity', 0);
	}, 50);
	$('#toast-wrap .toast')[0].addEventListener("webkitTransitionEnd", function(){
		$('#toast-wrap').remove();
	});
	$('#toast-wrap .toast')[0].addEventListener("transitionEnd", function(){
		$('#toast-wrap').remove();
	});
}
function showConfirm(msg, yesCallback, cancelCallback){
	if(msg instanceof Array){
		if(msg.length == 2){
			msg = msg[0] + '<br/>' + msg[1];
		}else{
			msg = msg[0];
		}
	}
	if($('#confirm-wrap').length > 0){
		$('#confirm-wrap').remove();
	}
	var html = '<div id="confirm-wrap"><i></i><div class="confirm"><span>'+msg+'</span><div class="btns clearFix"><div class="yes">确认</div><div class="cancel">取消</div></div></div></div>';
	$('body').append(html);
	$('#confirm-wrap .confirm .btns .yes').on('click', function(){
		if(yesCallback)yesCallback();
		$('#confirm-wrap').remove();
	});
	$('#confirm-wrap .confirm .btns .cancel').on('click', function(){
		if(cancelCallback)cancelCallback();
		$('#confirm-wrap').remove();
	});
}
var imgdefereds;
function loadedImg(ele, callback){
	if($(ele).find('img').length > 0){
		imgdefereds=[];                     //定义一个操作数组  
		$(ele).find('img').each(function(){   //遍历所有图片，将图片  
		    var dfd=$.Deferred();               //定义一个将要完成某个操作的对象  
		    $(this).bind('load',function(){    
		        dfd.resolve();              //图片加载完成后，表示操作成功  
		    });  
		    if(this.complete){              //如果图片加载状态为完成，那么也标识操作成功  
		        setTimeout(function(){    
		            dfd.resolve();    
		        },1000);   
		    }  
		    imgdefereds.push(dfd);            //将所有操作对象放入数组中  
		}) ;
	    $.when.apply(null,imgdefereds).done(callback); 
	}
}
function initSwitch(onCallback, offCallback){
	$('.xjp-switch .handle .on').on('click', function(){
		$('.xjp-switch .handle').css({left:'-3rem'});
		$(this).css({zIndex:5});
		$('.xjp-switch .handle .off').css({zIndex:10});
		offCallback();
	});
	$('.xjp-switch .handle .off').on('click', function(){
		$(this).parent().css({left:0});
		$(this).css({zIndex:5});
		$('.xjp-switch .handle .on').css({zIndex:10});
		onCallback();
	});
}
