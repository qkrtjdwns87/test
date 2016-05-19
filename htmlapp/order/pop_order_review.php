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

	<!-- 구매후기등록 -->
	<section id="order_popup">
		<div class="select_box">
			<select id="">
				<option value="" selected="selected">Item 선택</option>
				<option value="">Item1</option>
				<option value="">Item2</option>
				<option value="">Item3</option>
				<option value="">Item4</option>
			</select>
		</div>

		<div class="review_star">
			<p>마음에 드신 만큼 별을 채워 주세요.</p>
			<a href="" class="star star2"><span class="hidden">구매후기 별점</span></a>
		</div>
	</section>
</div>

<script src="js/ui.js"></script>

<p><a href="javascript:;" onclick="$('#layer_tip_topright').show();">구매후기  버튼</a></p>
<!-- 한줄남기기 tip -->
<div id="layer_tip_topright" onclick="$('#layer_tip_topright').hide();">
	<span class="icn"></span>
	<div class="popup_box">
		함께 즐거운 CIRCUS를 위해 통신예절에
		어긋나거나, 비방, 상업적인 글 등은
		관리자에 의해 사전 통보없이 삭제될 수
		있습니다. 반복적으로 게시 시 서비스
		이용에도 제약이 있을 수도 있습니다.  
	</div>
</div>
<!-- //한줄남기기 tip -->

</body>
</html>