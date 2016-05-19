<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script type="text/javascript">
	    $(document).ready(function () {
	
	    });	
	</script>
</head>
<body>	
<div id="wrap">

	<section id="section_quiescence">
		<dl class="title">
			<dt>인증번호 입력</dt>
			<dd>휴면 계정 해제를 위해 인증번호를 이메일로 발송해 드립니다.</dd>
			<dd>인증번호는 회원가입 수신메일로 등록한 이메일 또는 SNS/네이버 계정의 이메일로 발송됩니다.</dd>
		</dl>
		
		<div class="code_mail">
			<p><input type="tel" class="inp_login_style1" placeholder="수신된 인증번호 입력" maxlength="10" /></p>
			<a href="" class="btn_code">인증메일발송</a>
			<p class="time">인증번호 유효시간 <span>05:00</span></p>
		</div>
		
		<div class="tip">
			<dl>
				<dt>휴면 계정 해제를 하시면…</dt>
				<dd>회원님의 이메일로 발송된 인증번호 확인 후 바로 휴면상태가 해제됩니다.</dd>
				<dd><strong>휴면 계정 해제 후 복구되는 서비스</strong></dd>
				<dd>
					<ul>
						<li>회원계정 및 프로필</li>
						<li>구매내역</li>
						<li>메시지</li>
						<li>장바구니</li>
					</ul>
				</dd>
			</dl>
		</div>


		<a href="" class="btn_ok">확인</a>
	</section>

</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>			