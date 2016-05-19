<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/app/user_a/snsemailregist/uno/'.$uNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script type="text/javascript">
	    $(document).ready(function () {
	    	
	    });	
	    
		function sendEmailRegist(){
			if (trim($('#useremail').val()) == ''){
				alert('이메일을 입력하세요.');
				return;
			}
	
			if (!IsEmail($('#useremail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}			
	
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";	
			document.form.submit();		
		}
	</script>
</head>
<body>	
<div id="wrap">
	<form name="form" method="post">
	<input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>
	<section id="section_pw_reissue">
		<dl>
			<dt>이메일 등록</dt>
			<dd>선택하신 SNS에 이메일 정보가 없습니다.</dd>
			<dd>주문·배송 정보발송, 인증번호 발송 등에 꼭 필요한 이메일이므로, <br />정확하게 입력해 주세요.</dd>
		</dl>
		
		<p><input type="email" id="useremail" name="useremail" class="inp_login_style1" placeholder="이메일 주소" onkeydown="javascript:if(event.keyCode==13){sendEmailRegist(); return false;}" />
			<!-- 잘못된형식 일때 class="on" 추가 -->
			<span class="input_error">이메일 주소를 정확하게 입력해주세요<img src="/images/app/login/icn_error.png" class="icn_error" alt="" /></span>
		</p>
		<a href="javascript:sendEmailRegist();" class="btn_join">가입</a>

	</section>
	</form>
</div>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>	