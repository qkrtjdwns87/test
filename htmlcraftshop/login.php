<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/craftshop/mobile.css">
<script type="text/javascript" src="/js/jquery-1.9.1.js"></script>
</head>
<body>
<div id="wrap">

	<section id="section_login">
		<dl class="login_tit">
			<dt>CIRCUS 계정을 사용하여 로그인</dt>
			<dd>Item 구매 시 보다 편하게 이용하실 수 있어요.</dd>
		</dl>
		<ul class="login_inp">
			<li><input type="email" class="inp_login_style1" placeholder="CIRCUS 계정 (이메일 주소)" /></li>
			<li><input type="password" class="inp_login_style1" placeholder="비밀번호(알파벳,숫자,특수문자조합8자이상)" /></li>
		</ul>
		<ul class="login_check">
			<li>
				<label for="login_check1"><input type="checkbox" class="inp_checkbox1" id="login_check1" name="" />비밀번호 표시</label>
			</li>
			<li><label for="login_check2"><input type="checkbox" class="inp_checkbox1" id="login_check2" name="" />자동 로그인</label></li>
		</ul>
		
		<!-- 클래스명 on 일때 활성화 
			  클래스명 없을때 비활성화
		-->
		<!-- 로그인 비활성화 -->
		<a href="" class="login_default off">로그인</a>

		<!-- 로그인 활성화 -->
		<a href="" class="login_active off">로그인</a>
		
		<!-- 로그인 진행중 -->
		<a href="" class="login_ing on">
			<div class="sk-fading-circle">
				<div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div>
			</div>
			<span>로그인중</span>
		</a>
		
		<p class="login_text">※ 로그인 관련 문의, 입점관련 문의는 CIRCUS Craft Shop 담당자에게 문의해 주세요.</p>
	</section>

</div>
<script>
	circus.initLogin();
</script>
</body>
</html>