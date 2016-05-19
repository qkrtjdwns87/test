/**
 * @author chai
 */


var Loading = function(source){
	if(!source){
		source = [];
		for(var i=1; i<=10; i++){
			var num = i < 10 ? "0"+i : i +"";
			source.push('/images/app/common/loading/loading_'+num+'.png');
		}
	}
	var loading;
	var interval;
	var currentIndex = 0;
	var $img;
	init();
	function init(){
		createLoading();
		interval = setInterval(loop, 200);
	}
	
	function createLoading(){
		loading = document.createElement('div');
		loading.id = 'circus_loading';
		var html = '';
		for(var i=0; i<source.length; i++){
			html += '<img src="'+source[i]+'">';			
		}
		$(loading).html(html);
		$img = $(loading).children();
		$img.hide();
		$('body').append(loading);
		$(loading).hide();
	}
	
	function show(){
		currentIndex = 0;		
		$(loading).fadeIn();
	}
	
	function hide(){
		$(loading).fadeOut();
	}
	
	function loop(){
		currentIndex++;
		if(currentIndex == source.length){
			currentIndex = 0;
		}
		$img.each(function(i){
			if(currentIndex == i){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	}
	
	return {
		show: show,
		hide: hide
	}
};
