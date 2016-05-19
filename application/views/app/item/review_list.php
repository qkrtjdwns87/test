<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$isLoginJs = ($isLogin) ? 'true' : 'false';
	$loginUserNum = ($isLogin) ? get_cookie('usernum') : 0;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/item.css">
	<link rel="stylesheet" type="text/css" href="/css/app/popup.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="/js/loading.js"></script>
	<script type="text/javascript">
		var isLogin = <?=$isLoginJs?>;
		var sno = <?=$sNum?>;		
		var sino = <?=$siNum?>;		
		$(document).ready(function(){
			var loading;
			var currentPage = 1;
			var maxNo = 0;
			var viewPost;
			var listData;
			var $list = $('.item_comment_buy ul');
			var url = '/app/item_a/reviewlist/sno/'+sno+'/sino/'+sino+'/format/json';
			
			init();
			
			function init(){
				loading = new Loading();
				$(window).on('scroll', onScroll);
				
				loadAjax(url, function(data){
					listData = data.reviewRsSet;
					viewPost = data.reviewRsTotCnt;
					
					$('.total_num span').text(data.reviewRsTotCnt);
					
					if(listData.length){
						maxNo = listData[0].NUM;
						renderList(listData);
					}else{
						//$list.html(html);
						$('#list_none').show();
						$('.total_num').hide();						
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
					var isDel = data[i].DEL_YN;
					var profile;
					var arrProfile;						
					if(isDel == 'Y'){
						html += '<li class="reple_del">';
						html += '<p><img src="/images/app/main/icn_reple_del.png" alt="circus" /></p>';
						html += '<span>관리자에 의해 삭제된 흔적입니다.</span>';
						html += '</li>';
					}else{
						if (data[i].PROFILE_FILE_INFO != null && data[i].PROFILE_FILE_INFO != ''){
							arrProfile = data[i].PROFILE_FILE_INFO.split('|');
							profile = arrProfile[2]+arrProfile[3].replace('.', '_s.');							
						}else{
							profile = '/images/app/main/photo.jpg';
						}
												
						html += '<li data-index="'+index+'" id="cindex_'+data[i].NUM+'">';
						html += '<div class="img"><img src="'+profile+'" alt="" /></div>';
						html += '<div class="name"><span>'+data[i].USER_EMAIL_DEC.substring(0,3)+'****</span></div>';
						html += '<span class="star star'+data[i].SCORE+'"></span>';
						html += '<div class="text">'+data[i].CONTENT+'</div>';
						html += '</li>';
					}
				}
				if(isAdd){
					$list.append(html);
				}else{
					$list.html(html);
				}
			}
			
			function loadAjax(url, success, params){
				$.ajax({
					url:url + '/page/' + currentPage + '?maxNo=' + maxNo,
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

	<!-- 구매후기 -->
	<section id="item_view_comment_buy" class="pop_review">
		<div class="item_comment_buy">
			<p id="totalnum" class="total_num">총 <span>0</span>개</p>
			<ul>
				<li class="first_comment" id="list_none" style="display:none;">
					<!-- <p class="img"><img src="/images/app/main/first_comment.png" alt="circus" /></p>
					<p class="title"><span>첫번째 발견자</span>가 되어 보세요.</p> -->
					<p class="img"><img src="/images/app/common/no_content_big.png" alt="circus" /></p>
					<p class="title"><span>구매후기</span>를 남겨 보세요.</p>
				</li>

			</ul>
		</div>
	</section>
</div>


<!-- 구매후기 tip -->
<div id="layer_tip_topright" onclick="$('#layer_tip_topright').hide();">
	<span class="icn"></span>
	<div class="popup_box">
		함께 즐거운 CIRCUS를 위해 통신예절에 어긋나거나, 비방, 상업적인 글 등은 관리자에 의해 사전 통보없이 삭제될 수 있습니다. 반복적으로 게시 시 서비스 이용에도 제약이 있을 수도 있습니다.
	</div>
</div>
<!-- //구매후기 tip -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		