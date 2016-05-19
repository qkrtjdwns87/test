<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	if ($shopSet)
	{
		$defaultImg = $bgimg = '/images/gray_bg.png';
		if ($shopSet['MAIN_M_FILE_INFO'])
		{
			$arrFile = explode('|', $shopSet['MAIN_M_FILE_INFO']);
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
			$bgimg = (!empty($img)) ? $img : $defaultImg;
		}
		if ($shopSet['PROFILE_FILE_INFO'])
		{
			$arrFile = explode('|', $shopSet['PROFILE_FILE_INFO']);
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
			$profile_img = (!empty($img)) ? $img :"/images/ic_no_user.png";
		}

		//echo $profile_img;
		//print_r($shopSet);
		
		//SNS에 공유할 내용
		$snsImgUrl = '';
		$fullUrl = $siteDomain.$currentUrl;
		$snsShortUrl = $fullUrl;
		$snsSet = array(
			'facebook_appId' => $this->config->item('facebook_appid'),
			'twitter_key' => $this->config->item('twitter_consumer_key'),
			'kakao_Key' => $this->config->item('kakao_javascript_key'),
			'insta_clientId' => $this->config->item('insta_client_id'),
			'snsTitle' => $shopSet['SHOP_NAME'],
			'snsMsg' => $shopSet['SHOPUSER_NAME'],
			'snsImgUrl' => $siteDomain.$bgimg,
			'snsLink' =>$fullUrl,
			'snsDomain' => $snsShortUrl	//$fullUrl
		);
	}
	
	$isLoginJs = ($isLogin) ? 'true' : 'false';
	$loginUserNum = ($isLogin) ? get_cookie('usernum') : 0;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
 
	<meta property="og:site_name" content="서커스(Circus)">
	<meta property="og:url" content="<?=$snsSet[snsLink]?>">
	<meta property="og:title" content="<?=$snsSet[snsTitle]?>">
	<meta property="og:image" content="<?=$snsSet[snsImgUrl]?>">
	<meta property="og:description" content="서커스(Circus)-<?=$snsSet[snsMsg]?>">
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="circusflag.com">
	<meta name="twitter:title" content="<?=$snsSet[snsTitle]?>">
	<meta name="twitter:description" content="서커스(Circus)-<?=$snsSet[snsMsg]?>">
	<meta name="twitter:creator" content="@circusflag">
	<meta name="twitter:image:src" content="<?=$snsSet[snsImgUrl]?>">
	<meta name="twitter:domain" content="api.circusflag.com">


	<link rel="stylesheet" type="text/css" href="/css/app/item.css">
	<link rel="stylesheet" type="text/css" href="/css/app/swiper.min.css">
	<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>	
	<script src='http://connect.facebook.net/en_US/all.js'></script>	
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="/js/loading.js"></script>
	<script type="text/javascript" src="/js/app/swiper.min.js"></script>
	<script src="/js/mobile_common.js"></script>
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
		var sno = <?=$sNum?>;		
		var sino = <?=$siNum?>;
				
		$(document).ready(function(){
			var url = '/app/shop_a/bestlist/sno/<?=$sNum?>/format/json';
			var loading;
			var currentPage = 1;
			var viewPost;
			var listData;
			var $list = $('#all_item ul');
			
			init();
			
			function init(){
				loading = new Loading();
				$(window).on('scroll', onScroll);
				
				loadAjax(url, function(data){
					listData = data.bestRsSet;
					viewPost = data.listCount;
					
					if(listData.length){
						$('.h2title_craftshop').text('Items('+setComma(data.bestRsTotCnt)+')');
						renderList(listData);
					}
					
				}, {
					page:currentPage
				});
			}
			
			function onScroll(e){
				var scrollTop = $(this).scrollTop();
				var docHeight = $(document).height();
				var winHeight = $(window).height();
				if(docHeight-winHeight-10 <= scrollTop){
					addList();
				}
			}
			
			function addList(){
				loading.show();
				$(window).off('scroll', onScroll);
				currentPage++;
				loadAjax(url, function(data){
					loading.hide();
					$(window).on('scroll', onScroll);
					if(data.bestRsSet.length){					
						listData = data.bestRsSet;
						renderList(listData, true);
					}
				}, {
					page:currentPage
				});
			}
			
			function renderList(data, isAdd){
				var html = '';
				for(var i=0; i<data.length; i++){			
					var index = (i+(currentPage-1)*viewPost);
					var sale = data[i].DISCOUNT_YN == 'Y' ? 'sale' : '';
					var flag = data[i].ITEM_FLAG == '1' ? 'on' : '';
					var flagUrl = "webFlaging('item', '"+ data[i].SHOP_NUM+"', '"+ data[i].SHOPITEM_NUM+"');";
					var link = "/app/item_a/viewshare/sno/"+ data[i].SHOP_NUM+"/sino/"+ data[i].SHOPITEM_NUM;
					var itemImg;
					if (data[i].M_FILE_INFO != null && data[i].M_FILE_INFO != ''){
						var arrImg = data[i].M_FILE_INFO.split('|');
						itemImg = arrImg[2]+arrImg[3].replace('.', '_s.');							
					}					
					var soldout = '';
					if ((data[i].STOCKFREE_YN == 'N' && data[i].STOCK_COUNT == 0) || data[i].ITEMSTATECODE_NUM == '8070'){
						soldout = 'on';
					}
					var title = data[i].ITEM_NAME;
					var shop = data[i].SHOP_NAME;
					html += '<li>';
					html += '<span class="'+sale+'"></span>';
					html += '<span id="item_'+data[i].SHOP_NUM+'" class="flag '+flag+'" onclick="'+flagUrl+'"></span>';
					html += '<a href="'+link+'"><img src="'+itemImg+'" class="img_box" /></a>';
					html += '<p class="soldout '+soldout+'"><img src="/images/app/main/soldout.png" class="img_box" /></p>';
					html += '<p class="name">'+title+'</p>';
					html += '<p class="shop">'+shop+'</p>';
					html += '</li>';
				}
				if(isAdd){
					$list.append(html);
				}else{
					$list.html(html);
				}
			}
			
			function loadAjax(url, success, params){
				$.ajax({
					url:url + '/page/' + currentPage,
			        dataType:'json',
			        data:params,
			        type:'POST',
			        success:success
				});
			}
		});
	</script>
	</head>
<body>	
<div id="wrap">
<section id="craftshop_view_share">
		<p class="bg"><?if($bgimg){?><img src="<?=$bgimg?>"  onerror="this.src='/images/gray_bg.png'" alt="" /><?}?></p>
		<p class="photo"><?if($profile_img){?><img src="<?=$profile_img?>" onerror="this.src='/images/ic_no_user.png'" alt="" /><?}?></p>
		<div class="title"><?=$shopSet[SHOP_NAME]?><?if($shopSet[POPAUTHOR_YN]=="Y"){?><span class="icn_best">인기</span><?}?><?if($shopSet[TODAYAUTHOR_YN]=="Y"){?><span class="icn_today">오늘의 작가</span><?}?></div>
		<div class="text">
			<a href=""><?=$shopSet[PROFILE_CONTENT]?></a>
		</div>
		<ul class="sns_share">
			<li><a href=""><img src="/images/app/share/icn_sns1.png" alt="메세지" /></a></li>
			<li><a href="#" onclick="$('#layer_sns_share').show();"><img src="/images/app/share/icn_sns2.png" alt="공유" /></a></li>
		</ul>
	</section>

	<!-- 이 ITEM과 함께 보는 추천 ITEM -->
	<section id="item_view_more">
		<h2 class="h2title_craftshop">Items(0)</h2>
		
		<div id="all_item">
			<h3 class="h3title_craftshop">- All Items</h3>
			<!-- 제품리스트 -->
			<ul class="product_type1">

			</ul>
			<!-- //제품리스트 -->
		</div>

	</section>
	
</div>

<!-- <p><a href="javascript:;" onclick="$('#layer_sns').show();">sns 공유하기 레이어</a></p> -->
<!-- SNS 공유하기 레이어 -->
<div id="layer_sns_share" class="pop" style="display:;">
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
			<!--li><a href="javascript:snsShare('line');"><img src="/images/app/main/bestitem/sns_line.png" alt="라인" /><span>라인</span></a></li -->
		</ul>
	</div>
</div>
<!-- //SNS 공유하기 레이어 -->
<script src="/js/app/ui.js"></script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		