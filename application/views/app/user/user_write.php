<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$readonly = '';
	$style = '';

	if ($joinType == 'sns' && !empty($userEmail))
	{
		$readonly = 'readonly';
		$style = 'style="border:none;"';
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/app/user_a/write';
	$submitUrl .= (!empty($uNum)) ? '/uno/'.$uNum : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script type="text/javascript">
	    $(document).ready(function () {
	    	//app_showMenuWindow('정보입력및약관동의', '<?=$currentUrl?>');
	    });	
	    
		function sendMemberRegist(){
            var password = "";
            password = $("#passwd1").val();

			if (trim($('#useremail').val()) == ''){
				alert('이메일을 입력하세요.');
				$('#useremail').focus();
				return;
			}
	
			if (!IsEmail($('#useremail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}	

            var pwCheck = charValueCheck(password, 8, 20, "passwd");
            if (pwCheck == "minlength") {
                alert("비밀번호는 8자 이상만 가능합니다.");
                $("#passwd1").focus();
                return;
            }

            if (pwCheck == "han") {
                alert("비밀번호에는 한글을 사용하실 수 없습니다.");
                $("#passwd1").focus();
                return;
            }
                        
            /*
            if (pwCheck == "maxlength") {
                alert("비밀번호는 20자 이하만 가능합니다.");
                return;
            }

            if (pwCheck == "reg2") {
                alert("비밀번호는 8자이상의 영문,특수문자,숫자를 혼용하여야만 가능합니다.");
                return;
            }

            if (pwCheck == "samechar") {
                alert("비밀번호는 동일문자를 3번이상(aaa,111) 사용하실수 없습니다.");
                return;
            }

            if (pwCheck == "contchar") {
                alert("비밀번호는 연속된 문자열을 3개 이상(abc,123) 쓰실수 없습니다.");
                return;
            }
            */

			if (trim($('#passwd2').val()) == ''){
				alert('비밀번호 확인을 입력하세요.');
				$("#passwd2").focus();
				return;
			}               

			if (password != trim($('#passwd2').val())){
				alert('비밀번호 확인이 일치하지 않습니다.');
				$("#passwd2").focus();
				return;
			}            

			if (trim($('#username').val()) == ''){
				alert('이름을 입력하세요.');
				return;
			}

			if (trim($('#userbirth').val()) == ''){
				alert('생년월일을 입력하세요.');
				return;
			}			

			if (!IsNumber(trim($('#userbirth').val()))){
				alert('생년월일은 숫자만 입력할 수 있습니다.');
				return;
			}	

			if (trim($('#userbirth').val()).length < 8 || trim($('#userbirth').val()).length > 8){
				alert('생년월일을 정확히 입력하세요.');
				return;
			}

			if (!$('input:radio[name=usergender]').is(':checked')){
				alert('성별을 선택해 주세요.');
				return;				
			}								

			if (trim($('#usermobile').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}	

			if (!IsNumber(trim($('#usermobile').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}				

			if ($('#usermobile').val().substr(0, 2) != '01'){
				alert('올바른 휴대폰번호를 입력하세요.');
				return;
			}	

			if (trim($('#usermobile').val()).length < 10 || trim($('#usermobile').val()).length > 11){
				alert('올바른 휴대폰번호를 입력하세요.');
				return;
			}			

            if ($("input:checkbox[id='privacy1']").is(":checked") == false){
                alert('CIRCUS이용약관 동의가 필요합니다.');
                return;
            }

            if ($("input:checkbox[id='privacy2']").is(":checked") == false){
                alert('개인정보 수집 및 이용 동의가 필요합니다.');
                return;
            }			
			            
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";	
			document.form.submit();		
		}

		function emailDuplicate(){
			$('#useremail').focus();
			alert('입력하신 계정은 이미 CIRCUS에 가입되어\n있는 계정입니다.\n입력하신 정보를 다시 한번 확인해 주세요.');
		}
	</script>
</head>
<body>	
<div id="wrap">
	<form name="form" method="post">
	<input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>
	<input type="hidden" name="jointype" value="<?=$joinType?>" />
	<input type="hidden" name="joinemail" value="<?=$userEmail?>" />
	<section id="section_join_general">
		<p class="title">Item 구매 시 보다 빠르게 이용하시기 위해 정확한 정보를 입력해 주세요.</p>
		<ul>
			<li><input type="email" id="useremail" name="useremail" value="<?=$userEmail?>" class="inp_login_style1" placeholder="CIRCUS 계정 (이메일 주소)" <?=$readonly?> <?=$style?> /></li>
			<li><input type="password" id="passwd1" name="passwd1" class="inp_login_style1" placeholder="비밀번호(알파벳,숫자,특수문자조합8자이상)" onkeyup="javascript:passSafetyCheck();" /></li>
			<li><input type="password" id="passwd2" name="passwd2" class="inp_login_style1" placeholder="비밀번호 확인" /></li>
		</ul>

		<div class="tip_box">
			<a href="javascript:;:" onclick="$('#layer_tip_topleft').show();" class="btn_tip">비밀번호 설정 도움말 ⓘ</a>
			<!-- 해당 class에 on시 활성화  -->
			<span class="safety">안전성 단계 안전</span>
			<span class="normal">안전성 단계 보통</span>
			<span class="danger on">안전성 단계 위험</span>
		</div>

		<ul>
			<li><input type="text" id="username" name="username" value="<?=$userName?>" class="inp_login_style1" placeholder="이름" /></li>
			<li><input type="tel" id="userbirth" name="userbirth" value="<?=$userBirth?>" class="inp_login_style1" placeholder="생년월일 (ex. 19800216)" maxlength="8" /></li>
			<li class="gender">
				<span class="title">성별 선택</span>
				<label for="male"><span>남성</span><input type="radio" id="male" name="usergender" value="M" class="inp_checkbox1" <?if ($userGender == 'M'){?>checked="checked"<?}?> /></label>
				<label for="female"><span>여성</span><input type="radio" id="female" name="usergender" value="F" class="inp_checkbox1" <?if ($userGender == 'F'){?>checked="checked"<?}?> /></label>
			</li>
			<li>
				<input type="tel" id="usermobile" name="usermobile" class="inp_login_style1" placeholder="결제정보, 인증번호 수신 휴대폰번호(숫자만)" maxlength="11" />
			</li>
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
					<label for="privacy3"><input type="checkbox" class="inp_checkbox1" id="privacy3" name="privacy3" value="Y" />마케팅정보 수신 동의 (선택)</label>
				</li>
			</ul>
		</div>

		<a href="javascript:sendMemberRegist();" class="btn_join">가입</a>
	</section>
	</form>
	
	<!-- 일반회원가입 - 비밀번호 설정 도움말 tip -->
	<div id="layer_tip_topleft" onclick="$('#layer_tip_topleft').hide();" style="top:200px;">
		<span class="icn"></span>
		<div class="popup_box">
			3회 연속된 숫자, 생년월일, 동일한 숫자 3회 연속 사용은 사용하지 않는 것이 좋습니다.
		</div>
	</div>
	<!-- //일반회원가입 - 비밀번호 설정 도움말 tip -->

</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		