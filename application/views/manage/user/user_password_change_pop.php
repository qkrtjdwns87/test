<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	$updateType = 'select';
	$submitUrl  = '/manage/user_m/passwordupdate';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		$(function() {
			
		});

		
		function sendPassWdChange(){
            var password = "";
            password = $("#passwd1").val();
			
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
			            
			document.srcfrm.target = 'hfrm';
			document.srcfrm.action = "<?=$submitUrl?>";	
			document.srcfrm.submit();		
		}
	</script>
<!-- popup -->
<div id="popup">
	<div id="content">
		<div class="title">
			<h2>[비밀번호 재설정]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 비밀번호 재설정</div>
		</div>
		<form name="srcfrm" method="post" enctype="multipart/form-data">
		<input type="hidden" id="updateType" name="updateType" value="<?=$updateType?>"/>
		<input type="hidden" id="selUserNum" name="selUserNum" value="<?=$uNum?>"/>
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>신규비밀번호</th>
					<td><input type="password" id="passwd1" name="passwd1" class="inp_login_style1" placeholder="비밀번호(알파벳,숫자,특수문자조합8자이상)"/> <span class="ex">신규비밀번호 입력 </span></td>
				</tr>
				<tr>
					<th>신규비밀번호 재확인</th>
					<td><input type="password" id="passwd2" name="passwd2" class="inp_login_style2" placeholder="비밀번호 확인"/> <span class="ex">신규비밀번호 재확인</span></td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:sendPassWdChange();" class="btn1">비밀번호 변경</a>
		</div>
	</div>
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>			