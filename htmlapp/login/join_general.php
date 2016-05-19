<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/login.css">
<link rel="stylesheet" type="text/css" href="/css/app/popup.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
</head>
<body>
<div id="wrap">

	<section id="section_join_general">
		<p class="title">Item 구매 시 보다 빠르게 이용하시기 위해 정확한 정보를 입력해 주세요.</p>
		<ul>
			<li><input type="email" class="inp_login_style1" placeholder="CIRCUS 계정 (이메일 주소)" /></li>
			<li><input type="password" class="inp_login_style1" placeholder="비밀번호(알파벳,숫자,특수문자조합8자이상)" /></li>
			<li><input type="password" class="inp_login_style1" placeholder="비밀번호 확인" /></li>
		</ul>

		<div class="tip_box">
			<a href="javascript:;:" onclick="$('#layer_tip_topleft').show();" class="btn_tip">비밀번호 설정 도움말 ⓘ</a>
			<!-- 해당 class에 on시 활성화  -->
			<span class="safety">안전성 단계 안전</span>
			<span class="normal">안전성 단계 보통</span>
			<span class="danger on">안전성 단계 위험</span>
		</div>

		<ul>
			<li><input type="text" class="inp_login_style1" placeholder="이름" /></li>
			<li><input type="tel" class="inp_login_style1" placeholder="생년월일 (ex. 19800216)" maxlength="8" /></li>
			<li class="gender">
				<span class="title">성별 선택</span>
				<label for="male"><span>남성</span><input type="radio" id="male" name="gender" class="inp_checkbox1" /></label>
				<label for="female"><span>여성</span><input type="radio" id="female" name="gender" class="inp_checkbox1" /></label>
			</li>
			<li><input type="tel" class="inp_login_style1" placeholder="결제정보, 인증번호 수신 휴대폰번호(숫자만)" maxlength="11" /></li>
		</ul>

		<div class="privacy">
			<ul>
				<li>
					<label for="privacy1"><input type="checkbox" class="inp_checkbox1" id="privacy1" name="" />CIRCUS 이용약관 동의 (필수)</label>
					<a href="" class="btn_privacy">전문보기</a>
				</li>
				<li>
					<label for="privacy2"><input type="checkbox" class="inp_checkbox1" id="privacy2" name="" />개인정보 수집 및 이용 동의 (필수)</label>
					<a href="" class="btn_privacy">전문보기</a>
				</li>
				<li>
					<label for="privacy3"><input type="checkbox" class="inp_checkbox1" id="privacy3" name="" />마케팅정보 수신 동의 (선택)</label>
				</li>
			</ul>
		</div>

		<a href="" class="btn_join">가입</a>
	</section>

	<!-- 일반회원가입 - 비밀번호 설정 도움말 tip -->
	<div id="layer_tip_topleft" onclick="$('#layer_tip_topleft').hide();" style="top:200px;">
		<span class="icn"></span>
		<div class="popup_box">
			3회 연속된 숫자, 생년월일, 동일한 숫자 3회 연속 사용은 사용하지 않는 것이 좋습니다.
		</div>
	</div>
	<!-- //일반회원가입 - 비밀번호 설정 도움말 tip -->

</div>
</body>
</html>