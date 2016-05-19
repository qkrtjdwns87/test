<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CIRCUS ADMIN</title>
<script type="text/javascript" src="/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/admin.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/jquery.base64.min.js"></script>
<link rel="stylesheet" type="text/css" href="/css/adm/common.css" />
<link rel="stylesheet" type="text/css" href="/css/adm/style.css" />
	<script type="text/javascript">
		$(function() {

		});

		function loginSend(){
			if ($('#useremail').val() == ''){
				alert('이메일을 입력하세요.');
				return;
			}
						
			if ($('#userpw').val() == ''){
				alert('비밀번호를 입력하세요.');
				return;
			}

			document.form.target = 'hfrm';
			document.form.action = "/manage/user_m/loginconfirm";
			document.form.submit();			
		}
	</script>
</head>
<body style="overflow:hidden;">
<!-- header -->
<div id="index_header">
	
</div>
<!--// header -->

<!-- container -->
<div id="container">
	<div id="content">
		<div class="index_login">
			<h1><img src="/images/adm/index_logo.png" alt="CIRCUS" /></h1>
			<div class="index_box">
				<form name="form" method="post">
				<input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>
					<ul>
						<li><span>ID</span><input type="text" id="useremail" name="useremail" class="input_style1" /></li>
						<li><span>PASSWORD</span><input type="password" id="userpw" name="userpw" class="input_style1" onkeydown="javascript:if(event.keyCode==13){loginSend(); return false;}" /></li>
					</ul>
					<a href="javascript:loginSend();" class="btn_login"><img src="/images/adm/btn_login.png" alt="login" /></a>
				</form>
			</div>
		</div>
	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		