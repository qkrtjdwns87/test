<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script type="text/javascript">
	    $(document).ready(function () {
	    	//app_showMenuWindow('비밀번호재발급', '<?=$currentUrl?>');
	    });	

	    function sendPassRe(type){
			if (trim($('#useremail').val()) == ''){
				alert('이메일을 입력하세요.');
				return;
			}
	
			if (!IsEmail($('#useremail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}	

			$('#reqtype').val(type);
			document.form.target = 'hfrm';
			document.form.action = "/app/user_a/pwreissue";
			document.form.submit();		
	    }

	    function sendResult(){
	    }
	</script>
</head>
<body>	
<div id="wrap">
	<form name="form" method="post">
	<input type="hidden" id="reqtype" name="reqtype" value=""/>
	<input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>
	<section id="section_pw_reissue">
		<dl>
			<dt>등록한 정보로 재발급</dt>
			<dd>CIRCUS 회원가입 시 등록해 주신 메일주소나 휴대폰 번호로 임시비밀번호를 발송해 드립니다. </dd>
			<dd>가입 시 등록한 이메일 주소를 입력해 주세요.</dd>
			<dd>수신하신 임시비밀번호는 24시간만 유효합니다.</dd>
		</dl>
		
		<p><input type="email" id="useremail" name="useremail" class="inp_login_style1" placeholder="이메일 주소" /></p>
	
		<ul class="btn_list">
			<li><a href="javascript:sendPassRe('email');">이메일로 받기</a></li>
			<li><a href="javascript:sendPassRe('mobile');">휴대폰으로 받기</a></li>
		</ul>
	</section>
	</form>
</div>	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>			