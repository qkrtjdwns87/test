<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/item.css">
<link rel="stylesheet" type="text/css" href="/css/app/swiper.min.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/app/swiper.min.js"></script>
<script type="text/javascript" src="/js/loading.js"></script>
<script type="text/javascript" src="/js/app/tab.js"></script>
<script>
	$(document).ready(function(){
		var url = 'all_item.json';
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
				listData = data.list;
				viewPost = data.viewPost;
				
				if(listData.length){
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
				if(data.list.length){					
					listData = data.list;
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
				var sale = data[i].sale == 'true' ? 'sale' : '';
				var flag = data[i].flag == 'true' ? 'on' : '';
				var link = data[i].link == 'true' ? data[i].link : 'javascript:;';
				var thumb = data[i].thumb;
				var soldout = data[i].soldout == 'true' ? 'on' : '';
				var title = data[i].title;
				var shop = data[i].shop;
				html += '<li>';
				html += '<span class="'+sale+'"></span>';
				html += '<span class="flag '+flag+'" onclick=""></span>';
				html += '<a href="'+link+'"><img src="'+thumb+'" class="img_box" /></a>';
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
				url:url,
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
		<p class="bg"><img src="/images/app/share/sample1.jpg" alt="" /></p>
		<p class="photo"><img src="/images/app/share/photo.jpg" alt="" /></p>
		<div class="title">POFF FATION JWEWLRY<span class="icn_best">인기</span><span class="icn_today">오늘의 작가</span></div>
		<div class="text">
			<a href="">자연스러운 멋을 추구하는 홍대 장인이라 자부합니다. <br />누군가에게 값을 받고 제대로 되지 않은 물건을 드린..</a>
		</div>
		<ul class="sns_share">
			<li><a href=""><img src="/images/app/share/icn_sns1.png" alt="메세지" /></a></li>
			<li><a href=""><img src="/images/app/share/icn_sns2.png" alt="공유" /></a></li>
		</ul>
	</section>

	<section id="craftshop_share_tab">
		<div class="cate">
			<ul>
				<li class="list"><a class="on" href="#">items</a></li>
				<li class="list"><a href="#">contents</a></li>
			</ul>
		</div>

		<!-- 첫번째 tab item -->
		<div class="content">
			<div id="item_view_more">
				<h2 class="h2title_craftshop">Items(83)</h2>
				
				<div>
					<h3 class="h3title_craftshop">- Best</h3>
					<!-- 제품리스트 -->
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
					</ul>
					<!-- //제품리스트 -->
				</div>

				<div id="all_item">
					<h3 class="h3title_craftshop">- All Items</h3>
					<!-- 제품리스트 -->
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
							<span class="flag on" onclick="alert('flag');"></span>
							<a href="javascirpt:;" onclick="alert('링크1');"><img src="/images/app/main/sample0.jpg" class="img_box" /></a>
							<p class="soldout"><img src="/images/app/main/soldout.png" class="img_box" /></p>
							<p class="name">Winter Holiday Clutch</p>
							<p class="shop">Lovely Shop</p>
						</li>
					</ul>
					<!-- //제품리스트 -->
				</div>

			</div>
		</div>
		<!-- //첫번째 tab item -->

		<!-- 두번째 tab contents -->
		<div class="content">
			<!-- 내용이 없을때 -->
			<div class="text_none">
				<p class="img"><img src="/images/app/main/first_comment.png" alt="circus" /></p>
				<p class="title"><span>제품을 준비</span> 하고 있습니다.</p>
			</div>
		</div>
		<!-- //두번째 tab contents -->
		<script>
			var tab = new Tab({
				wrap: $('#craftshop_share_tab'), //container
				index:0, //start content index default:0
				transition:'none' //effect default:'none'( 'slide', 'fade' )
			});
		</script>
	</section>


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

<script src="/js/app/ui.js"></script>


</body>
</html>