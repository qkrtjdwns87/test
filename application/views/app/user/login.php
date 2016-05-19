<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/app/user_a/loginconfirm';


?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
	    $(document).ready(function () {
	
	    });	
	    
		function sendLogin(){
			if (trim($('#useremail').val()) == ''){
				alert('이메일을 입력하세요.');
				return;
			}
	
			if (!IsEmail($('#useremail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}		
						
			if (trim($('#userpw').val()) == ''){
				alert('비밀번호를 입력하세요.');
				return;
			}
	
			$('.login_active').removeClass('on');
			$('.login_active').addClass('off');		
			$('.login_ing').removeClass('off');		
			$('.login_ing').addClass('on');
	
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}
	
		function facebookLogin() {
			//openNewsWin('<?=$fbLoginUrl?>');
			location.href='<?=$fbLoginUrl?>';
		}
	
		function facebookLogout(){
			location.href = '<?=$fbLogoutUrl?>';
			
		}
	
		function twitterLogin() {
			//location.href = '<?=$twLoginUrl?>';
			location.href = '<?=$twLoginUrl?>';        	            
		}
	
		function twitterLogout() {
			location.href = '<?=$twLogoutUrl?>';
		}    
	
		function naverLogin() {
			location.href = '<?=$nvLoginUrl?>';
			//openNewsWin('<?=$nvLoginUrl?>');
		}
	
		function naverLogout() {
			location.href = '<?=$nvLogoutUrl?>';
		}
	
		function kakaoLogin(){
			//openNewsWin('<?=$kaLoginUrl?>');
				location.href = '<?=$kaLoginUrl?>';
		}
	
		function kakaoLogout(){
			location.href = '<?=$kaLogoutUrl?>';
		}
	
	    function googleLogin(){
	    	location.href = '<?=$ggLoginUrl?>';
	    }
	
	    function googleLogout(){
	    	location.href = '<?=$ggLogoutUrl?>';
	    }        
	
	    function memberJoin() {
	        location.href = '/app/user_a/join/return_url/'+$.base64.encode('/app/user_a/writeform?jointype=sns');
	    }
	
	    function passChangeNotice(m, url){
		    if (getCookie('changeAfterYn') == 'Y'){
			    location.href = url;
			    return;
		    }
	    	loginBtnReset();
	    	var msg = '주기적인 비밀번호 변경으로 개인정보<br />를 안전하게 보호하세요.<br />비밀번호는 <span style="color:#a40f0f;font-weight:bold;">'+m+'</span>개월마다 변경하시는 것이<br />안전합니다.';
	    	var btn_cnt = 2;
	    	var btn1_title = '30일 후 변경';
	    	var btn2_title = '지금 변경';
	    	var btn1_css = ' red';
	    	var btn2_css = '';
	    	var btn1_url = "javascript:passChangeAfter('"+url+"');";
	    	var btn2_url = '/app/user_a/pwreissueform';
	    	msgAlert(2, msg, btn1_title, btn2_title, btn1_css, btn2_css, btn1_url, btn2_url);
	    	/*
	        if (confirm('주기적인 비밀번호 변경으로 개인정보\n를 안전하게 보호하세요.\n비밀번호는 '+m+'개월마다 변경하시는 것이\n안전합니다.')){
	            alert('비번설정 페이지 이동');
	        }else{
		        location.href = '/';
	        }
	        */
	    }  

	    function joinNotice(url){
	    	loginBtnReset();
	    	var msg = 'CIRCUS에 가입되지 않은 계정입니다.<br />회원가입페이지로 이동합니다.';
	    	var btn_cnt = 1;
	    	var btn1_title = '회원가입';
	    	var btn2_title = '';
	    	var btn1_css = '';
	    	var btn2_css = '';
	    	var btn1_url = "/app/user_a/join";
	    	var btn2_url = '';
	    	msgAlert(btn_cnt, msg, btn1_title, btn2_title, btn1_css, btn2_css, btn1_url, btn2_url);		    
	    }

	    function dormantNotice(){
	    	//loginBtnReset();
	    	location.href = '/app/user_a/dormantclearform';
	    }	
	</script>
</head>
<body>	
<div id="wrap">
	<div id="fb-root"></div>
	<form name="form" method="post">
	<input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>
	<input type="hidden" id="deviceid" name="deviceid" value="<?=$deviceId?>"/>
	<input type="hidden" id="pushid" name="pushid" value="<?=$pushId?>"/>
	<section id="section_login">
		<dl class="login_tit">
			<dt>CIRCUS 계정을 사용하여 로그인</dt>
			<dd>Item 구매 시 보다 편하게 이용하실 수 있어요.</dd>
		</dl>
		<ul class="login_inp">
			<li><input type="email" id="useremail" name="useremail" class="inp_login_style1" placeholder="CIRCUS 계정 (이메일 주소)" /></li>
			<li><input type="password" id="userpw" name="userpw" class="inp_login_style1" placeholder="비밀번호(알파벳,숫자,특수문자조합8자이상)" onkeydown="javascript:if(event.keyCode==13){sendLogin(); return false;}" /></li>
		</ul>
		<ul class="login_check">
			<li>
				<label for="login_check1"><input type="checkbox" class="inp_checkbox1" id="login_check1" name="" />비밀번호 표시</label>
				<a href="/app/user_a/pwreissueform" class="btn_find">비밀번호를 잊어버리셨나요?</a>
			</li>
			<li><label for="login_check2"><input type="checkbox" class="inp_checkbox1" id="login_check2" name="autologin" value="Y" />자동 로그인</label></li>
		</ul>
				
		<!-- 클래스명 on 일때 활성화 
			  클래스명 없을때 비활성화
		-->
		<!-- 로그인 비활성화 
		<a href="#" class="login_default on">로그인</a>
		-->
		
		<!-- 로그인 활성화 -->
		<a href="javascript:sendLogin();" class="login_active on">로그인</a>
		
		<!-- 로그인 진행중 -->
		<a href="#" class="login_ing off">
			<div class="sk-fading-circle">
				<div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div>
			</div>
			<span>로그인중</span>
		</a>

		<div class="login_sns">
			<p class="title_or"><span>또는</span></p>
			<dl>
				<dt>SNS/네이버 계정으로 로그인</dt>
				<dd>
			    	<a href="javascript:facebookLogin();"><img src="/images/app/login/icn_fb.png" alt="페이스북" /></a>
			    	<a href="javascript:twitterLogin();"><img src="/images/app/login/icn_tw.png" alt="트위터" /></a>
			    	<a href="javascript:naverLogin();"><img src="/images/app/login/icn_naver.png" alt="네이버" /></a>
			    	<a href="javascript:kakaoLogin();"><img src="/images/app/login/icn_kt.png" alt="카카오톡" /></a>
			    	<a href="javascript:googleLogin();"><img src="/images/app/login/icn_google.png" alt="구글" /></a>
				</dd>
			</dl>
			<p class="title_join"><span>아직 회원이 아니세요?</span>
				<!-- <a href="javascript:memberJoin();" class="btn_join">회원가입</a> -->
				<!-- <a href="javascript:memberJoin();" class="login_active on"> -->
					<!-- <img class="new_join" src="/images/app/login/join_bt.png"> -->
				<!-- </a> -->
			</p>
			<a href="javascript:memberJoin();" class="login_active on">회원가입</a>

		</div>
		<!-- <div>
			<img class="new_join" src="/images/app/login/join_bt.png">
		</div> -->

	</section>
	</form>
</div>
<script>
	circus.initLogin();
</script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>	