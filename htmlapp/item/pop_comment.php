<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/item.css">
<link rel="stylesheet" type="text/css" href="/css/app/popup.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/loading.js"></script>
<script>
	$(document).ready(function(){
		var url = 'comment.json';
		var loading;
		var currentPage = 1;
		var viewPost;
		var listData;
		var $list = $('.item_comment ul');
		
		init();
		
		function init(){
			loading = new Loading();
			$(window).on('scroll', onScroll);
			
			loadAjax(url, function(data){
				listData = data.list;
				viewPost = data.viewPost;
				
				$('.total_num span').text(data.totalPost);
				
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
			for(var i=0; i<data.length; i++){			
				var index = (i+(currentPage-1)*viewPost);
				var isDel = data[i].isDel;
				var delBtn = data[i].deleteBtn == 'true' ? '<p class="btn_del"><a href="">삭제</span></a></p>' : '';
				
				var profile;				
				var reple;
				if(data[i].isReple == 'true'){
					reple = 'reple';
					profile = '/images/app/main/bestitem/icn_reple.png';
				}else{
					reple = '';
					profile = data[i].profile;
				}
				if(isDel == 'true'){
					html += '<li class="reple_del">';
					html += '<p><img src="/images/app/main/icn_reple_del.png" alt="circus" /></p>';
					html += '<span>관리자에 의해 삭제된 흔적입니다.</span>';
					html += '</li>';
				}else{
					html += '<li data-index="'+index+'" class="'+reple+'">';
					html += '<div class="img"><img src="'+profile+'" alt="" /></div>';
					html += '<div class="name"><span>'+data[i].userId+'</span></div>';
					html += '<span class="time">'+data[i].date+'</span>';
					html += '<div class="text">'+data[i].content+delBtn+'</div>';
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

	<!-- 한줄 흔적 남기기 -->
	<section id="item_view_comment" class="pop_comment">
		<div class="item_comment">
			<p class="total_num">총 <span>245</span>개</p>
			<ul>
				<li class="first_comment">
					<p class="img"><img src="/images/app/main/first_comment.png" alt="circus" /></p>
					<p class="title"><span>첫번째 발견자</span>가 되어 보세요.</p>
				</li>
				<!--
				<li>
					<div class="img"><img src="/images/app/main/photo.jpg" alt="" /></div>
					<div class="name"><span>applesamsung</span></div>
					<span class="time">2015-12-17 15:25</span>
					<div class="text">
						정말 예쁘긴 한데 정말 예쁘긴 한데정말 예쁘긴 한데정말 예쁘긴 <br />한데정말 예쁘긴 한데정말 예쁘긴 한데정말 <br />예쁘긴 한데정말 <br />예쁘긴 한데<br />
						<p class="btn_del"><a href="">삭제</span></a></p>
					</div>
				</li>
				<li>
					<div class="img"><img src="/images/app/main/photo.jpg" alt="" /></div>
					<div class="name"><span>yellobanana</span></div>
					<span class="time">2015-12-17 15:25</span>
					<div class="text">
						정말 예쁘긴 한데 정말 예쁘긴 한데정말 예쁘긴 한데정말 예쁘긴 <br />한데정말 예쁘긴 한데정말 예쁘긴 한데정말 <br />예쁘긴 한데정말 <br />예쁘긴 한데<br />
						<p class="btn_del"><a href=""><span>삭제</span></a></p>
					</div>
				</li>
				

				<li class="reple">
					<div class="img"><img src="/images/app/main/bestitem/icn_reple.png" alt="답변" /></div>
					<div class="name"><span>관리자</span></div>
					<span class="time">2015-12-17 15:25</span>
					<div class="text">
						실제로 보면 더 예쁜데...<br />실제로 보면 더 예쁜데...<br />실제로 보면 더 예쁜데...<br />
					</div>
				</li>



				<li class="reple_del">
					<p><img src="/images/app/main/icn_reple_del.png" alt="circus" /></p>
					<span>관리자에 의해 삭제된 흔적입니다.</span>
				</li>
				-->
			</ul>
		</div>
		<div class="item_view_comment_write">
			<dl>
				<dt><textarea type="text" class="inp_write" placeholder="최대 100자 댓글 입력"></textarea></dt>
				<dd><a href="" class="btn_write"><img src="/images/app/main/bestitem/btn_write.png" alt="등록" /></a></dd>
			</dl>
		</div>
	</section>
</div>





<p><a href="javascript:;" onclick="$('#layer_tip_topright').show();">한줄남기기 tip 버튼</a></p>
<!-- 한줄남기기 tip -->
<div id="layer_tip_topright" onclick="$('#layer_tip_topright').hide();">
	<span class="icn"></span>
	<div class="popup_box">
		함께 즐거운 CIRCUS를 위해 통신예절에 어긋나거나, 비방, 상업적인 글 등은 관리자에 의해 사전 통보없이 삭제될 수 있습니다. 반복적으로 게시 시 서비스 이용에도 제약이 있을 수도 있습니다.
	</div>
</div>
<!-- //한줄남기기 tip -->
</body>
</html>