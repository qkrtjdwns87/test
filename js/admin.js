$(document).ready(function(){
	$('.gnb li a').on('click', function(e){
		if($(this).attr('href').length){
			return;
		}
		$('.gnb li ul').hide();
		$('.gnb li a').removeClass('on');
		$(this).parent().find('ul').show();
		$(this).addClass('on');
		return false;
	});
});