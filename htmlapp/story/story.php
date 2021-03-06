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
<body>

<!-- <p><a href="javascript:;" onclick="$('#layer_sns').show();">sns 공유하기 레이어</a></p> -->
<!-- SNS 공유하기 레이어 -->
<div id="layer_sns" class="pop">
	<div class="bg"></div>
	<div class="popup_box">
		<div class="top">
			<p>공유하기</p>
			<a href="javascript:;" onclick="$('.pop').hide();" class="btn_close"><img src="/images/app/main/bestitem/layer_sns_btn_close.png" alt="close" /></a>
		</div>
		<ul>
			<li><a href=""><img src="/images/app/main/bestitem/sns_ka.png" alt="카카오톡" /><span>카카오톡</span></a></li>
			<li><a href=""><img src="/images/app/main/bestitem/sns_fb.png" alt="페이스북" /><span>페이스북</span></a></li>
			<li><a href=""><img src="/images/app/main/bestitem/sns_tw.png" alt="트위터" /><span>트위터</span></a></li>
			<li><a href=""><img src="/images/app/main/bestitem/sns_insta.png" alt="인스타그램" /><span>인스타그램</span></a></li>
			<li><a href=""><img src="/images/app/main/bestitem/sns_ks.png" alt="카카오스토리" /><span>카카오스토리</span></a></li>
			<li><a href=""><img src="/images/app/main/bestitem/sns_line.png" alt="라인" /><span>라인</span></a></li>
		</ul>
	</div>
</div>
<!-- //SNS 공유하기 레이어 -->

<div id="wrap">
	<section id="story_section">
		<div class="swiper-container">
			<ul class="swiper-wrapper">
				
				<!-- 영상 -->
				<li class="swiper-slide movie_box" data-vid="7gVJghmjSIc">
					<div class="player"></div>	
				</li>
				
				<!-- 이미지일때 -->
				<li class="swiper-slide"><p class="img"><img src="/images/app/story/sample1.jpg" alt="" /></p></li>
				<li class="swiper-slide"><p class="img"><img src="/images/app/story/sample2.jpg" alt="" /></p></li>

				<!-- 영상 -->
				<li class="swiper-slide movie_box" data-vid="7gVJghmjSIc">
					<div class="player"></div>	
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
				<a href="#" class="inner_g"></a>
			</span>
			<!--<input id="debug" type="text" style="position: absolute; top:0;" value="aaa">-->
			<p class="num"><span class="num_g">1</span><span class="num_g_total"> / 25</span></p>
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
	
	var player;
	var players = [];
	
	$('.num_g_total').text(' / '+ len);
	
	$(window).on('resize', resizeHandler).trigger('resize');
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
				for(var i in players){
					players[i].stopVideo();
				}
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
			width: moveX
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
				width: n*(gutterWidth)
			});
			$('.graph_g .inner_g').css({
				left: n*gutterWidth
			});
		}});
	}
	
	//youtube
	function onYouTubeIframeAPIReady() {		
		var w = $(window).width();
		var h = $(window).height();
		
		var scale = w/560;
		var vh = 315*scale;
		
		$('.movie_box').each(function(i){
			$(this).data('num', i);
			$(this).css('height', h);
			player = new YT.Player($(this).find('.player')[0], {				
			    height: vh,
			    width: w,
			    videoId: $(this).data('vid')
			});
			
			$(this).find('.player').css({
				marginTop:((h-32)-vh)*.5
			});
			players.push(player);
		});
	}
	
</script>
</body>
</html>