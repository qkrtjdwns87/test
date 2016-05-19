<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/main.css">
<link rel="stylesheet" type="text/css" href="/css/app/swiper.min.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/app/swiper.min.js"></script>
<script src="https://www.youtube.com/iframe_api"></script>

</head>
<body style="overflow:hidden;">
<div id="wrap">
	<section id="story_section">
		<div class="swiper-container">
			<ul class="swiper-wrapper">
				<!-- 이미지일때 -->
				<li class="swiper-slide"><p class="img"><img src="/images/app/story/sample1.jpg" alt="" /></p></li>
				<li class="swiper-slide"><p class="img"><img src="/images/app/story/sample2.jpg" alt="" /></p></li>

				<!-- 영상 -->
				<li class="swiper-slide movie_box" data-vid="u2l6nk7pMQ0">
					<a class="youtube_cover" href="javascript:;"><img src="/images/app/story/btn_movie.png" alt="" /></a>
					<div class="movie_player"></div>
				</li>
				
				<!-- craftshop -->
				<li class="swiper-slide">
					<div class="craftshop">
						<div class="craftshop_store">
							<dl>
								<dt><img class="craft_thumbnail" src="/images/app/main/bestitem/sample3.jpg" alt="" /></dt>
								<dd class="title">POFF FATION </dd>
								<dd class="author">
									<span class="name">작가 문성훈</span>
									<span class="popularity">인기</span>
									<span class="today_author">오늘의 작가</span>
								</dd>
							</dl>
						</div>

						<ul class="product_type1">
							<li>
								<span class="sale"></span>
								<span class="flag on" onclick="alert('flag');"></span>
								<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
								<p class="soldout on"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name">Winter Holiday Clutch</p>
								<p class="shop">Lovely Shop</p>
							</li>
							<li>
								<span class="sale"></span>
								<span class="flag" onclick="alert('flag');"></span>
								<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
								<p class="soldout"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name">Winter Holiday Clutch</p>
								<p class="shop">Lovely Shop</p>
							</li>
							<li>
								<span class="sale"></span>
								<span class="flag on" onclick="alert('flag');"></span>
								<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
								<p class="soldout on"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name">Winter Holiday Clutch</p>
								<p class="shop">Lovely Shop</p>
							</li>
							<li>
								<span class="sale"></span>
								<span class="flag" onclick="alert('flag');"></span>
								<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
								<p class="soldout"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name">Winter Holiday Clutch</p>
								<p class="shop">Lovely Shop</p>
							</li>
							<li>
								<span class="sale"></span>
								<span class="flag on" onclick="alert('flag');"></span>
								<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
								<p class="soldout on"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name">Winter Holiday Clutch</p>
								<p class="shop">Lovely Shop</p>
							</li>
							<li>
								<span class="sale"></span>
								<span class="flag" onclick="alert('flag');"></span>
								<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
								<p class="soldout"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name">Winter Holiday Clutch</p>
								<p class="shop">Lovely Shop</p>
							</li>
						</ul>
					</div>
				</li>

			</ul>
		</div>
		
		<!-- 터치영역 -->
		<div class="graph_comm">
			<div class="bg_graph"></div>
			<span class="graph_g">
				<span class="bar_g"></span>
				<a href="#" class="inner_g"><span  class="num_g">1</span></a>
			</span>
		</div>
		<!-- //터치영역 -->

	</section>
</div>

<script type="text/javascript">
	var len = $('.swiper-slide').size();
	var aniObj = {p:0};
	
	var moveX;
	var downX;
	var elX;
	var gutterWidth = $('.graph_g').width()-21;
	var percent;
	var currentIndex = 0;
	var isDrag;
	
	$(window).on('resize', resizeHandler);
	$('.inner_g').on('mousedown touchstart', downHandler);
	
	//swiper
	var swiper = new Swiper('.swiper-container', {
		loop: false,
		onSlideChangeStart:function(swiper){			
			var index = swiper.activeIndex;
			var percent = index/(len-1);
			if(!isDrag) animate(percent)
			$('.num_g').text(index+1);
			if(player){
				player.stopVideo();
			}
		}
	});
	
	function resizeHandler(e){
		gutterWidth = $('.graph_g').width()-21;
	}
	
	function downHandler(e){
		$(window).on('mousemove touchmove', moveHandler);
		$(window).on('mouseup touchend', upHandler);
		downX = e.originalEvent.touches ? e.originalEvent.touches[0].pageX : e.pageX;
		elX = parseInt($(this).css('left'));
	}
	
	function moveHandler(e){
		isDrag = true;
		var dx = e.originalEvent.touches ? e.originalEvent.touches[0].pageX : e.pageX;
		moveX = Math.max(0, Math.min(gutterWidth, (dx - downX+elX) ));
		percent = moveX / gutterWidth;
		aniObj.p = parseInt($('.inner_g').css('left')) / gutterWidth;
		if(currentIndex != Math.round(percent*(len-1))){
			currentIndex = Math.round(percent*(len-1));
			swiper.slideTo(currentIndex);
		}
		$('.inner_g').css('left', moveX);
		$('.graph_g .bar_g').css({
			width: moveX+21
		});
	}
	
	function upHandler(e){
		$(window).off('mousemove touchmove');
		$(window).off('mouseup touchend');
		isDrag = false;
	}
	
	function animate(target){
		$(aniObj).stop().animate({p:target}, {duration:500, step:function(n){
			$('.graph_g .bar_g').css({
				width: n*(gutterWidth+21)
			});
			$('.graph_g .inner_g').css({
				left: n*gutterWidth
			});
		}});
	}
	
	// youtube
	var player;
	var iframe;
	var isFullScreen;
	var currentVideoId;
	var players = [];
	$(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange', function(e){
			isFullScreen = !isFullScreen;
			if(isFullScreen){
				$('.movie_player').show();
			}else{
				player.pauseVideo();
				$('.movie_player').hide();
				$('.youtube_cover').show();
			}
	});
	
	$(document).on('click', '.youtube_cover', function(){
		$(this).hide();
		iframe = $(this).parent().find('iframe')[0];
		player = players[$(this).parent().data('num')];
		playFullscreen();
		return false;
	});

	function onYouTubeIframeAPIReady() {		
		var w = $(window).width();
		
		$('.movie_box').each(function(i){
			$(this).data('num', i);
			player = new YT.Player($(this).find('.movie_player')[0], {
			    height: 0.5625*w,
			    width: w,
			    videoId: $(this).data('vid'),
			    events: {
			    	'onReady': onPlayerReady
			    }
			});
			players.push(player);
		});
		
		$('.movie_player').css({
    		marginTop:(($(window).height()-80)-(0.5625*w))*.5,
    		display:'none'
    	});
	}
	
	function onPlayerReady(event) {
		var player = event.target;
		iframe = $(player)[0];
		
	}
	function playFullscreen(){
		player.playVideo();
	    var requestFullScreen = iframe.requestFullScreen || iframe.mozRequestFullScreen || iframe.webkitRequestFullScreen;
		if (requestFullScreen) {
	    	requestFullScreen.bind(iframe)();
		}
	}
	
	
</script>
</body>
</html>