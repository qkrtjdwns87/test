<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/craftshop/mobile.css">
<script type="text/javascript" src="/js/jquery-1.9.1.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".notice_list dt").click(function(){
		$(".notice_list dd").slideUp();
		$(".notice_list dt").addClass("faq_off");
		$(".notice_list dt").removeClass("faq_on");
		if(!$(this).next().is(":visible"))
		{
			$(this).addClass("faq_on");
			$(this).removeClass("faq_off");
			$(this).next().slideDown();
		}
	});
});
</script>
</head>
<body>
<div id="wrap">

	<section id="section_notice_list">

		<ul class="notice_list">
			<li>
				<dl class="first">
					<dt class="faq_on">
						<p class="title">공지사항</p>
						<!-- on 일때 활성화 off일때 비활성화 -->
						<span class="new on">NEW</span>

						<p class="time">2015-12-17 15:25</p>
						<span class="arrow"></span>
					</dt>
					<dd>CIRCUS AWARD를 진행합니다. <br />각 분야별로 가장 좋았던 Item과 Craft Shop를 추천 투표해주세요. <br />12월말 그 결과가 발표됩니다.</dd>
				</dl>
			</li>

			<li>
				<dl>
					<dt>
						<p class="title">CIRCUS AWARD를 진행합니다CIRCUS AWARD를 진행합니다CIRCUS AWARD를 진행합니다CIRCUS AWARD를 진행합니다CIRCUS AWARD를 진행합니다.1</p>
						<!-- on 일때 활성화 off일때 비활성화 -->
						<span class="new on">NEW</span>

						<p class="time">2015-12-17 15:25</p>
						<span class="arrow"></span>
					</dt>
					<dd>CIRCUS AWARD를 진행합니다. <br />각 분야별로 가장 좋았던 Item과 Craft Shop를 추천 투표해주세요. <br />12월말 그 결과가 발표됩니다.</dd>
				</dl>
			</li>
		<ul>
	</section>

</div>
</body>
</html>