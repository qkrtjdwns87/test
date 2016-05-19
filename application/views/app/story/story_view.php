<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$storyCnt = 0;
	$stoNum = $recordSet['NUM'];
	$title = $recordSet['TITLE'];
	$name = $recordSet['USER_NAME'];
	$email = $recordSet['USER_EMAIL_DEC'];
	$storyContent = $recordSet['STORY_CONTENT'];
	$orgWriteUserNum = 0;
	
	//SNS에 공유할 내용
	$snsImgUrl = '';
	$fullUrl = $siteDomain.$currentUrl;
	$snsShortUrl = $this->common->getShortURL($fullUrl);
	$snsSet = array(
		'facebook_appId' => $this->config->item('facebook_appid'),
		'twitter_key' => $this->config->item('twitter_consumer_key'),
		'kakao_Key' => $this->config->item('kakao_javascript_key'),
		'insta_clientId' => $this->config->item('insta_client_id'),
		'snsTitle' => $title,
		'snsMsg' => $this->common->cutStr($this->common->stripHtmlTags($storyContent), 80, '...'),
		'snsLink' => $this->common->getShortURL($fullUrl),
		'snsDomain' => $snsShortUrl	//$fullUrl
	);
	if (!empty($fileSet[0]['FILE_NAME']))
	{
		$snsImgUrl = $siteDomain.$fileSet[0]['FILE_PATH'].$fileSet[0]['FILE_TEMPNAME'];
		$snsSet['snsImgUrl'] = $snsImgUrl;
	}	
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$isLoginJs = ($isLogin) ? 'true' : 'false';
	$loginUserNum = ($isLogin) ? get_cookie('usernum') : 0;	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/main.css">
	<link rel="stylesheet" type="text/css" href="/css/app/swiper.min.css">
	<script type="text/javascript" src="/js/app/swiper.min.js"></script>
	<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>	
	<script src='http://connect.facebook.net/en_US/all.js'></script>		
	<script src="https://www.youtube.com/iframe_api"></script>	
	<script type="text/javascript">
		var isLogin = <?=$isLoginJs?>;
	    //sns 공유될 내용
        var snsTitle = "<?=$snsSet['snsTitle']?>";
        var snsMsg = "<?=$snsSet['snsMsg']?>";
        var snsImgUrl = "<?=$snsSet['snsImgUrl']?>";
        var snsLink = "<?=$snsSet['snsLink']?>";
        var snsDomain = "<?=$snsSet['snsDomain']?>";
		var fbAppId = "<?=$snsSet['facebook_appId']?>";
		var kakaoKey = "<?=$snsSet['kakao_Key']?>";		
	    $(document).ready(function () {
	    });	
	</script>
</head>
<body>
<div id="wrap">

	<section id="story_section">
		<div class="swiper-container">
			<ul class="swiper-wrapper">
			<?
				//$i = 0;
				//foreach ($recSubSet as $rs):
				//0번은 배너 이미지
				for($i=1; $i<count($recSubSet); $i++)
				{
					if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1810)
					{
						//파일정보
						$fileNum = $fileSet[$i]['NUM'];
						$fileName = $fileSet[$i]['FILE_NAME'];
						$imgUrl = $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME']; //원본이미지가 나오도록
			?>
 				<!-- 이미지일때 -->
				<li class="swiper-slide"><p class="img"><img src="<?=$imgUrl?>" alt="" /></p></li>
			<?
					}
					else if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1820)
					{
			?>
				<!-- 영상 -->
				<li class="swiper-slide movie_box" data-vid="<?=$recSubSet[$i]['CONTENT']?>">
					<div class="cover"></div>
					<a class="youtube_cover" href="javascript:;"><img src="/images/app/story/btn_movie.png" alt="" /></a>
					<div class="movie_player"></div>
				</li>			
			<?
					}
					else if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1830)
					{		
						$arrShopInfo = explode('|', $recSubSet[$i]['SHOP_INFO']);
						$shopCode = $arrShopInfo[0];
						$shopName = $arrShopInfo[1];
						$shopUserName = $arrShopInfo[2];
						$todayAuthorYn = $arrShopInfo[3];
						$popAuthorYn = $arrShopInfo[4];
						$arrFile = explode('|', $recSubSet[$i]['PROFILE_FILE_INFO']);
						$defaultImg = '/images/app/main/bestitem/sample3.jpg';
						if (!empty($arrFile[0]))
						{
							if ($arrFile[4] == 'Y')	//썸네일생성 여부
							{
								$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
							}
							else
							{
								$img = $arrFile[2].$arrFile[3];
							}
						}	
						$fileName = (!empty($img)) ? $img : $defaultImg;						
			?>		
				<!-- craftshop -->
				<li class="swiper-slide">
					<div class="craftshop">
						<div class="craftshop_store">
							<dl>
								<dt><img class="craft_thumbnail" src="<?=$fileName?>" alt="" /></dt>
								<dd class="title"><?=$shopCode?></dd>
								<dd class="author">
									<span class="name"><?=$shopName?></span>
									<?if ($popAuthorYn == 'Y'){?><span class="popularity">인기</span><?}?>
									<?if ($todayAuthorYn == 'Y'){?><span class="today_author">오늘의 작가</span><?}?>
								</dd>
							</dl>
						</div>

						<ul class="product_type1">
						<?
							if (!empty($recSubSet[$i]['SHOPITEM_INFO']))
							{
								$defaultImg = '';
								$arrItemInfo = explode('-', $recSubSet[$i]['SHOPITEM_INFO']); //최대8건의 아이템정보
								foreach ($arrItemInfo as $irs):
									$arrItem = explode('#', $irs); //1건씩의 아이템정보
									$shopNum = $arrItem[0];
									$itemNum = $arrItem[1];
									$itemFlag = $arrItem[2];
									$arrItemDetail = explode('|', $arrItem[3]); //아이템 세부 항목
									$arrFile = explode('|', $arrItem[5]); //1건에 대한 파일정보									
									$url = '/app/item_a/view/sno/'.$shopNum.'/sino/'.$itemNum;
									$img = '';
									if (!empty($arrFile[0]))
									{
										if ($arrFile[4] == 'Y')	//썸네일생성 여부
										{
											$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
										}
										else
										{
											$img = $arrFile[2].$arrFile[3];
										}
									}
									$fileName = (!empty($img)) ? $img : $defaultImg;
									$flagCss = ($itemFlag) ? ' on' : '';
									$itemName = $this->common->cutStr($arrItemDetail[4], 30, '..');
									$shopName = $arrItemDetail[2];
									$stockFreeYn = $arrItemDetail[11];
									$stockCount = $arrItemDetail[12];
									$itemStateCodeNum = $arrItemDetail[8];
									$soldOutCss = (($stockFreeYn == 'N' && $stockCount == 0) || $itemStateCodeNum == 8070) ? ' on' : '';
						?>
							<li>
								<span class="sale"></span>
								<span id="item_<?=$itemNum?>" class="flag<?=$flagCss?>" onclick="webFlaging('item', '<?=$itemNum?>', '<?=$shopNum?>');"></span>
								<a href="javascript:app_moveToItemDetail('<?=$shopNum?>', '<?=$itemNum?>');"><img src="<?=$fileName?>" class="img_box" /></a>
								<p class="soldout<?=$soldOutCss?>"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name"><?=$itemName?></p>
								<p class="shop"><?=$shopName?></p>
							</li>
						<?
								endforeach;
							}
						?>								
						</ul>
					</div>
				</li>				
			<?
					}
					else if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1840)
					{			
			?>
				<!-- html -->
				<li class="swiper-slide">"<?=$recSubSet[$i]['HTML_CONTENT']?>"</li>			
			<?			
				
						$storyShopName = $recSubSet[$i]['SHOP_NAME'].'('.$recSubSet[$i]['SHOP_CODE'].')';
						$storyShopNum = $recSubSet[$i]['CONTENT'];
					}
				}
				//	$i++;
				//endforeach;						
			?>
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

<!-- <p><a href="javascript:;" onclick="snsShareOpen();">sns 공유하기 레이어</a></p> -->
<!-- SNS 공유하기 레이어 -->
<div id="layer_sns" class="pop">
	<div class="bg"></div>
	<div class="popup_box">
		<div class="top">
			<p>공유하기</p>
			<a href="javascript:;" onclick="$('.pop').hide();" class="btn_close"><img src="/images/app/main/bestitem/layer_sns_btn_close.png" alt="close" /></a>
		</div>
		<ul>
			<li><a href="javascript:snsShare('kakao');"><img src="/images/app/main/bestitem/sns_ka.png" alt="카카오톡" /><span>카카오톡</span></a></li>
			<li><a href="javascript:snsShare('facebook');"><img src="/images/app/main/bestitem/sns_fb.png" alt="페이스북" /><span>페이스북</span></a></li>
			<li><a href="javascript:snsShare('twitter');"><img src="/images/app/main/bestitem/sns_tw.png" alt="트위터" /><span>트위터</span></a></li>
			<!-- <li><a href="javascript:snsShare('insta');"><img src="/images/app/main/bestitem/sns_insta.png" alt="인스타그램" /><span>인스타그램</span></a></li> -->
			<li><a href="javascript:snsShare('kakaostory');"><img src="/images/app/main/bestitem/sns_ks.png" alt="카카오스토리" /><span>카카오스토리</span></a></li>
			<li><a href="javascript:snsShare('line');"><img src="/images/app/main/bestitem/sns_line.png" alt="라인" /><span>라인</span></a></li>
		</ul>
	</div>
</div>
<!-- //SNS 공유하기 레이어 -->

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
	
	$('.num_g_total').text(' / '+ len);
	
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
		var h = $(window).height();
		
		$('.youtube_cover').css({
			top:(($(window).height()-80)-$('.youtube_cover').height())*.5
		});
		
		$('.movie_box').each(function(i){
			$(this).data('num', i);
			$(this).css('height', h);
			player = new YT.Player($(this).find('.movie_player')[0], {				
			    height: 0.5625*w,
			    width: w,
			    videoId: $(this).data('vid'),
			    events: {
			    	'onReady': onPlayerReady
			    }
			});
			var img = '<img class="youtube_img" src="'+getScreen($(this).data('vid'))+'" >';
			$(this).find('.cover').html(img);
			var $self = $(this);
			var interval = setInterval(function(){
				if($('.youtube_img').height()){
					$self.find('.cover').css({
						marginTop:(($(window).height()-80)-$('.youtube_img').height())*.5
					});
				}
			}, 100);
			
			
			players.push(player);
		});
		$('.movie_player').css({
    		marginTop:(($(window).height()-80)-(0.5625*w))*.5,
    		display:'none'
    	});
		
		$('.movie_player').css({
    		marginTop:(($(window).height()-80)-(0.5625*w))*.5,
    		display:'none'
    	});
	}
	
	function onPlayerReady(event) {
		//var player = event.target;
		//iframe = $(player)[0];
		
	}
	function playFullscreen(){
		player.playVideo();
	   var requestFullScreen = iframe.requestFullScreen || iframe.mozRequestFullScreen || iframe.webkitRequestFullScreen;
	   if (requestFullScreen) {
	    	requestFullScreen.bind(iframe)();
		}
	}
	
	function getScreen( vid, size )
	{	
	  size = (size === null) ? "big" : size;
	
	  if(size == "small"){
	    return "http://img.youtube.com/vi/"+vid+"/2.jpg";
	  }else {
	    return "http://img.youtube.com/vi/"+vid+"/0.jpg";
	  }
	}
</script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		