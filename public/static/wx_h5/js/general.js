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