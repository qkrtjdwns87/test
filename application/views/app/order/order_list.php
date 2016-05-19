<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="/js/loading.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
			var url = '/app/order_a/list/format/json';
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
					listData = data.recordSet;
					viewPost = data.listCount;
					
					if(listData.length){
						renderList(listData);
					}else{
						//$list.html(html);
						$('#error_popup').show();
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
					if(data.recordSet.length){					
						listData = data.recordSet;
						renderList(listData, true);
					}
				}, {
					page:currentPage
				});
			}
			
			function renderList(data, isAdd){
				var html = '';
				var tmpOrdNum = orderNum = 0;
				//console.log(data);
				for(var i=0; i<data.length; i++){				
					var date = data[i].CREATE_DATE.substring(0,10);
					var orderNum = data[i].NUM;
					if (tmpOrdNum != orderNum){
						html += '<li>';
						html += '<span class="day">'+date+'</span>';
					}

					var orderState = data[i].ORDSTATECODE_NUM;
					var orderStateTitle = data[i].ORDSTATECODE_TITLE;
					var itemImg;
					if (data[i].FIRST_FILE_INFO != null && data[i].FIRST_FILE_INFO != ''){
						var arrImg = data[i].FIRST_FILE_INFO.split('|');
						itemImg = arrImg[2]+arrImg[3].replace('.', '_s.');							
					}
					var itemTitle;
					var itemNum;
					var itemUrl;
					if (data[i].FIRST_ITEM_INFO != null && data[i].FIRST_ITEM_INFO != ''){
						var arrTitle = data[i].FIRST_ITEM_INFO.split('|');
						itemNum = arrTitle[0];							
						itemTitle = arrTitle[1];
						if (data[i].PARTITEM_COUNT > 1){
							itemTitle = itemTitle+'<span>외 '+(data[i].PARTITEM_COUNT-1)+'개</span>'
						}
						itemUrl = '/app/item_a/view/sno/'+data[i].SHOP_NUM+'/sino/'+itemNum;
					}
					var partUrl = '/app/order_a/partview/ordno/'+data[i].NUM+'/ordptno/'+data[i].ORDERPART_NUM;
					var shop = data[i].SHOP_NAME;
					var title = data[i].itemTitle;
					var price = parseInt(data[i].PART_AMOUNT) + parseInt(data[i].DELIVERY_PRICE);
					var order_code = data[i].ORDER_CODE;
					var deliverClass = (orderState == '5230' || orderState == '5380') ? 'btn_order_end' : 'btn_order_ing';
					
					html += '<a href="'+partUrl+'" class="btn_order_go">';
					html += '<div class="order_total_title">';
					html += '<span class="name">'+shop+'</span>';
					html += '<span class="'+deliverClass+'">'+orderStateTitle+'</span>';
					html += '</div>';
					html += '<dl>';
					html += '<dt class="photo"><img src="'+itemImg+'" width="280" height="190" alt="" /></dt>';
					html += '<dd class="number"><span>주문번호 '+order_code+'</span></dd>';
					html += '<dd>'+itemTitle+'</dd>';
					html += '<dd class="total_price"><strong>'+setComma(price)+'<span>원</span></strong></dd>';
					html += '</dl>';
					html += '</a>';
				
					if (tmpOrdNum == orderNum){	
						html += '</li>';	
					}

					tmpOrdNum = orderNum;
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

				
			</ul>
		</section>
		<!-- //주문내역 있을 경우 -->
		
		<!-- 주문/배송조회_목록 없음 -->
		<section id="error_popup" style="display:none;">
			<strong>주문내역이 없습니다</strong>
			<!-- <p>마음은 팔 수도 살 수도 없는 것이지만 줄 수 있는 보물입니다. <br>
			CIRCUS는 작가의 마음도 함께 전합니다.</p>
			<span>-  Given by CIRCUS Master</span> -->
		</section>		
	</div>
</div>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		