(function(window){
	var Tab = function(option){
		var $wrap = option.wrap;
		var $list = $wrap.find('.cate .list a');
		var $content = $wrap.find('.content');
		
		var currentIndex = option.index || 0;
		var transition = option.transition || 'none';
		
		if(transition == 'slide' || transition == 'fade') $content.css('position','absolute');
		
		var oldIndex;
		var contentWidth = parseInt($content.width());
		var isAnimate;
		
		init();
		
		function init(){
			$list.bind('click', clickHandler);
			firstRender();
		}
		
		function firstRender(){
			active();
			$content.each(function(i){
				if(i == currentIndex){
					$(this).show();
				}else{
					$(this).hide();
				}
			});
		}
		
		function clickHandler(e){
			e.preventDefault();
			if( isAnimate ) return false;
			if( $(this).parent().index() == currentIndex ) return false;
			
			oldIndex = currentIndex;			
			currentIndex = $(this).parent().index();			
			viewContent();
		}
		
		function active(){
			$list.each(function(i){
				if(i == currentIndex){
					$(this).addClass('on');
				}else{
					$(this).removeClass('on');
				}
			});
		}
		
		function viewContent(){	
			active();
			if(transition == 'none'){
				$content.each(function(i){
					if(i == currentIndex){
						$(this).show();
					}else{
						$(this).hide();
					}
				});
			}
			
			if(transition == 'slide'){
				isAnimate = true;
				$content.eq(currentIndex).css({left:contentWidth, display:'block'}).stop().animate({left:0},500);
				$content.eq(oldIndex).css('left', 0).stop().animate({left:-contentWidth},{duration:500, complete:function(){
					$content.eq(oldIndex).css('display', 'none');
					isAnimate = false;
				}});
			}
			if(transition == 'fade'){
				isAnimate = true;
				$content.eq(currentIndex).css({opacity:0, display:'block'}).stop().animate({opacity:1},500);
				$content.eq(oldIndex).stop().animate({opacity:0},{duration:500, complete:function(){
					$content.eq(oldIndex).css('display', 'none');
					isAnimate = false;
				}});
			}			
		}
		
		this.moveContent = function(index){
			oldIndex = currentIndex;			
			currentIndex = index;
			viewContent();
		}		
	}
	window.Tab = Tab;
})(window);