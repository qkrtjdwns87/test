<?
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script src="//code.jquery.com/jquery.min.js"></script>
	<script src='http://connect.facebook.net/en_US/all.js'></script>	
	<script src="/ckeditor/ckeditor.js"></script>
	<script src="/js/common.js"></script>
	
	<title>Insert title here</title>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	
		function send(){
			if (trim($('#uemail').val()) == ''){
				alert('이메일을 입력하세요.');
				return false;
			}

			if (!IsEmail($('#uemail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return false;
			}			
						
			if (trim($('#upass').val()) == ''){
				alert('비밀번호를 입력하세요.');
				return false;
			}

			if (trim($('#upass_check').val()) == ''){
				alert('비밀번호를 입력하세요.');
				return false;
			}

			if ($('#upass').val() != $('#upass_check').val()){
				alert('비밀번호가 일치하지 않습니다.');
				return false;
			}

			if (trim($('#uname').val()) == ''){
				alert('이름을 입력하세요.');
				return false;
			}

			if (trim($('#umobile1').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return false;
			}

			if (trim($('#umobile2').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return false;
			}

			if (trim($('#umobile3').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return false;
			}

			var sel = $(':radio[name="ugender"]:checked').val();

			if (!$('input:radio[name=ugender]').is(':checked')){
				alert('성별을 선택해 주세요.');
				return false;				
			}

			document.form.target = 'hfrm';
			document.form.action = "/user/write";			
		}
	</script>
</head>
	<body>
	    <div>회원 가입</div>
	    <form name="form" method="post" onsubmit="return send()">
	    <input type="hidden" id="returnUrl" name="return_url" value="<?=$returnUrl?>"/>	    
	    <div>
	    	<p>이메일<input type="text" id="uemail" name="uemail" value="" style="width:200px" /></p>
	    	<p>비번<input type="text" id="upass" name="upass" value="" style="width:200px" /></p>
			<p>비번확인<input type="text" id="upass_check" name="upass_check" value="" style="width:200px" /></p>
			<p>이름<input type="text" id="uname" name="uname" value="" style="width:200px" /></p>				    	
			<p>생년월일<input type="text" id="ubirth_year" name="ubirth_year" value="" style="width:60px" maxlength="4"/>년			
			<input type="text" id="ubirth_month" name="ubirth_month" value="" style="width:40px" maxlength="2" />월
			<input type="text" id="ubirth_day" name="ubirth_day" value="" style="width:40px" maxlength="2" />일
			<p>
			<p>휴대폰번호 
			<input type="text" id="umobile1" name="umobile1" value="" style="width:40px" maxlength="4" />
			<input type="text" id="umobile2" name="umobile2" value="" style="width:40px" maxlength="4" />
			<input type="text" id="umobile3" name="umobile3" value="" style="width:40px" maxlength="4" />
			</p>
			<p>성별 
			<input type="radio" id="ugender_m" name="ugender" value="M"/>남
			<input type="radio" id="ugender_f" name="ugender" value="F"/>여
			</p>						
	    </div>	    
	    <div>
	    	<p>
	    	<input type="submit" id="writeBtn" value="확인 "/>
	    	</p>
	    </div>
	    </form>
	    <p>Page rendered in <strong>{elapsed_time}</strong> seconds.</p>
		<?
			 include '/inc/hidden_frame.php';		
		?>	    
    </body>
</html>