<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CIRCUS ADMIN</title>
<script type="text/javascript" src="/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/admin.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<link rel="stylesheet" type="text/css" href="/css/adm/common.css" />
<link rel="stylesheet" type="text/css" href="/css/adm/style.css" />
</head>
<body>
<!-- header -->
<div id="header">
	<h1><a href="/manage/main_m/main"><img src="/images/adm/logo.png" alt="IRCUS ADMIN" /></a></h1>
	<div class="lnb">
		<p class="name"><span><?=$sessionData['user_name']?></span> 님 안녕하세요!</p>
		<a href="/" class="btn1" target="_blank">CIRCUS 바로가기</a>
		<!-- joon mod 20Line -->
		<a href="/manage/user_m/passwordchange" class="btn1">비밀번호 변경</a>
		<a href="/manage/user_m/logout" class="btn1">Log out</a>
	</div>

	<div id="gnb">
		<ul class="gnb">
		<?if ($isAdmin){?>
			<li><a href="">회원관리</a>
				<ul class="sub">
					<li><a href="/manage/user_m/list">전체회원현황</a></li>
					<li><a href="/manage/user_m/leavelist">탈퇴관리</a></li>
				</ul>
			</li>
		<?} ?>
			<li><a href="">Craft Shop 관리</a>
				<ul class="sub">
				<?if ($isAdmin){?>
					<li><a href="/manage/shop_m/list">전체Shop현황</a></li>
					<li><a href="/manage/shop_m/apprlist">승인대기현황</a></li>
					<li><a href="/manage/shop_m/writeform">신규신청</a></li>
					<li><a href="/manage/board_m/list/setno/9020">공지사항(샵)</a></li>
					<li><a href="/manage/board_m/list/setno/9110">1:1문의(샵)</a></li>					
				<?}else{?>
					<li><a href="/manage/shop_m/view">Craft Shop 관리</a></li>				
					<li><a href="/manage/shop_m/shopbestitemform">대표 Item 관리</a></li>
					<li><a href="/manage/board_m/list/setno/9020">공지사항</a></li>
					<li><a href="/manage/board_m/list/setno/9110">1:1문의</a></li>					
				<?} ?>
				</ul>
			</li>
			<li><a href="">Item 관리</a>
				<ul class="sub">
					<li><a href="/manage/item_m/list">전체Item현황</a></li>
				<?if ($isAdmin){?>					
					<li><a href="/manage/item_m/apprlist">승인대기현황</a></li>
					<!-- <li><a href="/manage/item_m/denylist">승인보류/거부현황</a></li> -->
					<li><a href="/manage/item_m/modilist">수정요청현황</a></li>					
					<li><a href="/manage/item_m/enlist/evtype/s">기획전관리</a></li>
					<li><a href="/manage/item_m/enlist/evtype/g">Gift관리</a></li>
				<?}else{?>
					<li><a href="/manage/item_m/modilist">수정요청현황</a></li>				
				<?}?>
					<li><a href="/manage/item_m/catelist">카테고리관리</a></li>
				</ul>
			</li>
			<li><a href="">주문관리</a>
				<ul class="sub">
					<li><a href="/manage/order_m/list">전체 주문현황</a></li>
					<li><a href="/manage/order_m/paylist">입금/결제관리</a></li>
					<li><a href="/manage/order_m/cancellist">취소관리</a></li>
					<li><a href="/manage/order_m/refundlist">환불관리</a></li>
					<li><a href="/manage/order_m/exchangelist">교환관리</a></li>
					<li><a href="/manage/order_m/deliverylist">배송관리</a></li>
					<li><a href="/manage/order_m/deliveryfinishlist">배송완료</a></li>
				</ul>
			</li>
			<li><a href="">정산관리</a>
				<ul class="sub">
					<li><a href="">정산관리</a></li>
					<li><a href="">매출내역</a></li>
					<li><a href="">정산현황</a></li>
					<li><a href="">지급대기</a></li>
					<li><a href="">지급완료</a></li>
				</ul>
			</li>
		<?if ($isAdmin){?>			
			<li><a href="">게시물관리</a>
				<ul class="sub">
					<li><a href="/manage/story_m/list">Story</a></li>
					<li><a href="/manage/board_m/list/setno/9120">새소식</a></li>
					<li><a href="/manage/item_m/enlist/evtype/e">이벤트</a></li>
					<li><a href="/manage/board_m/list/setno/9130">FAQ</a></li>
					<li><a href="/manage/board_m/list/setno/9010">공지사항</a></li>
					<li><a href="/manage/board_m/list/setno/9020">공지사항(샵)</a></li>
					<li><a href="/manage/board_m/list/setno/9150">공지사항(앱)</a></li>
					<li><a href="/manage/board_m/list/setno/9100">1:1문의</a></li>					
					<li><a href="/manage/board_m/list/setno/9140">약관관리</a></li>
				</ul>			
			</li>
			<!-- 게시물관리 메뉴가 너무 많아 고객응대 메뉴를 새로 구성 -->
			<li><a href="">고객응대</a>
				<ul class="sub">
					<li><a href="/manage/message_m/list">전체메시지</a></li>
					<li><a href="/manage/message_m/listshop">Shop대화</a></li>
					<li><a href="/manage/message_m/listuser">회원대화</a></li>
					<li><a href="/manage/comment_m/list">댓글</a></li>
					<li><a href="/manage/review_m/list">구매후기</a></li>
				</ul>			
			</li>			
			<!-- 
			<li><a href="">앱관리</a>
				<ul class="sub">
					<li><a href="">앱버전관리</a></li>
				</ul>			
			</li>
			 -->
			<li><a href="">시스템관리</a>
				<ul class="sub">
					<li><a href="/manage/main_m/visualform">비주얼</a></li>
					<li><a href="/manage/main_m/todayform">Today</a></li>
					<li><a href="/manage/main_m/trendform">Trending</a></li>
					<li><a href="/manage/main_m/storyform">Story</a></li>
					<li><a href="/manage/main_m/recommsearchform">추천검색어</a></li>
					<li><a href="/manage/main_m/passchangeform">비번관리</a></li>
					<li><a href="/manage/main_m/newitemform">신상품</a></li>
					<li><a href="/manage/main_m/bestitemform">베스트셀러</a></li>
					<!-- 
					<li><a href="">관리자</a></li>
					<li><a href="">권한관리</a></li>
					<li><a href="">금칙어관리</a></li>
					 -->
				</ul>			
			</li>
			<!-- 
			<li><a href="">마케팅관리</a></li>
			 -->
		<?}else{?>
			<li><a href="">고객응대</a>
				<ul class="sub">
					<li><a href="/manage/message_m/list">전체메시지</a></li>
					<li><a href="/manage/message_m/listshop">Circus대화</a></li>
					<li><a href="/manage/message_m/listusershop">회원대화</a></li>
					<li><a href="/manage/comment_m/list">댓글</a></li>
					<li><a href="/manage/review_m/list">구매후기</a></li>
				</ul>			
			</li>		
		<?}?>
			<!-- 
			<li><a href="">통계/랭킹</a></li>
			 -->
		</ul>
	</div>
</div>
<!--// header -->