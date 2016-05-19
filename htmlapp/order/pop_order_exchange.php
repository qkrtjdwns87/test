<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/popup.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
</head>
<body>
<div id="wrap">

	<!-- 교환요청 -->
	<section id="order_popup">
		<ul class="title">
			<li>교환요청 전에 ‘교환 및 환불정책＇을 살펴봐주세요.</li>
			<li>Item 성격에 따라 교환이 어려울 수 있습니다.</li>
		</ul>

		<div class="select_box">
			<select id="">
				<option value="" selected="selected">교환요청 사유선택</option>
				<option value="">주문한 내용과 다름</option>
				<option value="">배송된 Item의 상태 불량</option>
				<option value="">주문 요청 추가</option>
				<option value="">기타</option>
			</select>

			<select id="">
				<option value="" selected="selected">교환요청 Item 선택</option>
				<option value="">Item1</option>
				<option value="">Item2</option>
				<option value="">Item3</option>
				<option value="">Item4</option>
			</select>
		</div>

		<div class="comment">
			<textarea id="" rows="5" cols="5" placeholder="l 상세 사유를 입력해 주세요"></textarea>
		</div>
	</section>

	<!-- 메뉴바 -->
	<div class="order_btn_list">
		<a href="">교환요청</a></li>
	</div>
	<!-- //메뉴바 -->
</div>

<script src="js/ui.js"></script>
</body>
</html>