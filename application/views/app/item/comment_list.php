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
			var viewPost = <?=$listCount?>;
			var listData;
			var $list = $('.item_comment ul');
	    	var url = '/app/item_a/commentlist/sno/'+sno+'/sino/'+sino+'/format/json';
	    				
			init();
			
			function init(){
				loading = new Loading();
				$(window).on('scroll', onScroll);
				
				loadAjax(url, function(data){
					listData = data.commentRsSet;
					viewPost = data.listCount;
					
					$('.total_num span').text(data.commentRsTotCnt);
					
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
					if(data.commentRsSet.length){					
						listData = data.commentRsSet;
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
					var delBtn = (isLogin && data[i].USER_NUM == '<?=$loginUserNum?>') ? '<p class="btn_del"><a href="javascript:commentDel('+data[i].NUM+');">삭제</span></a></p>' : '';
					var profile;
					var arrProfile;				
					var reple;
					if(data[i].DEPTH > 0){
						reple = 'reple';
						profile = '/images/app/main/bestitem/icn_reple.png';
					}else{
						reple = '';
						if (data[i].PROFILE_FILE_INFO != null && data[i].PROFILE_FILE_INFO != ''){
							arrProfile = data[i].PROFILE_FILE_INFO.split('|');
							profile = arrProfile[2]+arrProfile[3].replace('.', '_s.');							
						}else{
							profile = '/images/app/main/photo.jpg';
						}
					}
					if(isDel == 'Y'){
						html += '<li class="reple_del">';
						html += '<p><img src="/images/app/main/icn_reple_del.png" alt="circus" /></p>';
						html += '<span>관리자에 의해 삭제된 흔적입니다.</span>';
						html += '</li>';
					}else{
						html += '<li data-index="'+index+'" id="cindex_'+data[i].NUM+'" class="'+reple+'">';
						html += '<div class="img"><img src="'+profile+'" alt="" /></div>';
						html += '<div class="name"><span>'+data[i].USER_EMAIL_DEC.substring(0,3)+'****</span></div>';
						html += '<span class="time">'+data[i].CREATE_DATE.substring(0,16)+'</span>';
						html += '<div class="text">'+data[i].CONTENT+delBtn+'</div>';
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
	<!-- 한줄 흔적 남기기 -->
	<section id="item_view_comment" class="pop_comment">
		<div class="item_comment">
			<p id="totalnum" class="total_num">총 <span>0</span>개</p>
			<ul>
				<li class="first_comment" id="list_none" style="display:none;">
					<!-- <p class="img"><img src="/images/app/main/first_comment.png" alt="circus" /></p>
					<p class="title"><span>첫번째 발견자</span>가 되어 보세요.</p> -->
					<p class="img"><img src="/images/app/common/no_content_big.png" alt="circus" /></p>
					<p class="title"><span>한 줄 댓글</span>을 남겨 보세요.</p>
				</li>
			</ul>
		</div>
		<form name="form" method="post">
		<input type="hidden" id="itemno" name="itemno" value="<?=$siNum?>"/>
		<div class="item_view_comment_write">
			<dl>
				<dt><textarea type="text" id="brd_content" name="brd_content" class="inp_write" placeholder="최대 100자 댓글 입력"></textarea></dt>
				<dd><a href="javascript:sendComment();" class="btn_write"><img src="/images/app/main/bestitem/btn_write.png" alt="등록" /></a></dd>
			</dl>
		</div>
		</form>
	</section>
</div>


<!-- <p><a href="javascript:;" onclick="$('#layer_tip_topright').show();">한줄남기기 tip 버튼</a></p> -->
<!-- 한줄남기기 tip -->
<div id="layer_tip_topright" onclick="$('#layer_tip_topright').hide();">
	<span class="icn"></span>
	<div class="popup_box">
		함께 즐거운 CIRCUS를 위해 통신예절에 어긋나거나, 비방, 상업적인 글 등은 관리자에 의해 사전 통보없이 삭제될 수 있습니다. 반복적으로 게시 시 서비스 이용에도 제약이 있을 수도 있습니다.
	</div>
</div>
<!-- //한줄남기기 tip -->
		
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>			