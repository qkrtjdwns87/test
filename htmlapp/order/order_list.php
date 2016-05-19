<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/loading.js"></script>
<script>

	$(document).ready(function(){
		var url = 'order_list.json';
		var loading;
		var currentPage = 1;
		var viewPost;
		var listData;
		var $list = $('.order_list ul');
		
		init();
		
		function init(){
			loading = new Loading();
			$(window).on('scroll', onScroll);
			
			loadAjax(url, function(data){
				listData = data.list;
				viewPost = data.viewPost;
				
				if(listData.length){
					renderList(listData);
				}else{
					$list.html(html);
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
			console.log(data);
			for(var i=0; i<data.length; i++){				
				var date = data[i].date;
				
				html += '<li>';
				html += '<span class="day">'+date+'</span>';
				var orderData = data[i].order_list;
				for(var j=0; j<orderData.length; j++){
					
					var shop = orderData[j].shop;
					var deliver = orderData[j].deliver;
					var thumb = orderData[j].thumb;
					var title = orderData[j].title;
					var price = orderData[j].price;
					var link = orderData[j].link ? orderData[j].link : 'javascript:;'
					var rest = orderData[j].rest ? '<span>외 '+orderData[j].rest+'개</span>' : '';
					var order_num = orderData[j].order_num;
					var deliverClass = deliver == '배송완료' ? 'btn_order_end' : 'btn_order_ing';
					
					html += '<a href="'+link+'" class="btn_order_go">';
					html += '<div class="order_total_title">';
					html += '<span class="name">'+shop+'</span>';
					html += '<span class="'+deliverClass+'">'+deliver+'</span>';
					html += '</div>';
					html += '<dl>';
					html += '<dt class="photo"><img src="'+thumb+'" width="280" height="190" alt="" /></dt>';
					html += '<dd class="number"><span>주문번호 '+order_num+'</span></dd>';
					html += '<dd>'+title+rest+'</dd>';
					html += '<dd class="total_price"><strong>'+price+'<span>원</span></strong></dd>';
					html += '</dl>';
					html += '</a>';
				}				
				
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
	<div id="buy_container">
		<!-- 주문/배송조회 목록 -->
		<!-- 주문내역 있을 경우 -->
		
		<!-- 
			배송완료일때와 아닐 때의 class명이 다릅니다. 
			배송완료시 class명 : btn_order_end
			배송완료시가 아닐때 class명 : btn_order_ing
		-->
		<section class="order_list">
			<ul>
				<li>
					<span class="day">2016-02-02</span>
					<a href="" class="btn_order_go">
						<div class="order_total_title">
							<span class="name">Lovely Shop</span>
							<span class="btn_order_end">배송완료</span>
						</div>
						<dl>
							<dt class="photo"><img src="/images/app/cart/test.jpg" width="280" height="190" alt="" /></dt>
							<dd class="number"><span>주문번호 12457895445</span></dd>
							<dd>Winter Holiday Clutch </dd>
							<dd class="total_price"><strong>76,000<span>원</span></strong></dd>
						</dl>
					</a>
					<a href="" class="btn_order_go">
						<div class="order_total_title">
							<span class="name">러블리샵</span>
							<span class="btn_order_ing">배송중</span>
						</div>
						<dl>
							<dt class="photo"><img src="/images/app/cart/test.jpg" width="280" height="190" alt="" /></dt>
							<dd class="number"><span>주문번호 12457895445</span></dd>
							<dd>Winter Holiday Clutch <span>외 3개</span></dd>
							<dd class="total_price"><strong>32,000<span>원</span></strong></dd>
						</dl>
					</a>
				</li>
				<li>
					<span class="day">2016-02-01</span>
					<a href="" class="btn_order_go">
						<div class="order_total_title">
							<span class="name">대한민국</span>
							<span class="btn_order_end">배송완료</span>
						</div>
						<dl>
							<dt class="photo"><img src="/images/app/cart/test.jpg" width="280" height="190" alt="" /></dt>
							<dd class="number"><span>주문번호 12457895445</span></dd>
							<dd>Winter Holiday Clutch </dd>
							<dd class="total_price"><strong>76,000<span>원</span></strong></dd>
						</dl>
					</a>
				</li>
				
			</ul>
		</section>
		<!-- //주문내역 있을 경우 -->
	</div>
</div>
</body>
</html>