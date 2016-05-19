<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/manage/message_m/smswritepop';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	        $(':radio[name="smstype"]').click(function() {
	        	var selValue = $(this).val();
	        	if (selValue == "L"){
	        		$('#subjectDisp').show();
	        	}else if (selValue == "S"){
	        		$('#subjectDisp').hide();
	        	}
	        });
	    });
 	
		function sendSms(){
			if (trim($('#sendphone').val()) == ''){
				alert('발송번호 설정내용이 없습니다.');
				return;
			}
						
			if (trim($('#sms_content').val()) == ''){
				alert('내용을 입력하세요.');
				return;
			}

			var sel = $(':radio[name="smstype"]:checked').val();
			if (sel == 'L'){
				if (trim($('#sms_subject').val()) == ''){
					alert('장문문자 발송시 제목을 입력하셔야 합니다.');
					return;
				}
			}else{
				if (trim($('#sms_content').val()).length > 79){
					alert('80자 이상은 장문문자로 발송하셔야 합니다.');
					return;
				}				
			}	
						
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}
	</script>
<!-- popup -->
<div id="popup">
	<form name="form" method="post">
	<div class="title">
		<h3>[SMS 메세지 보내기]</h3>
	</div>
	
	<table class="write1">
		<colgroup><col width="20%" /><col /></colgroup>
		<tbody>
			<tr>
				<th>수신자</th>
				<td><?=$sendUserTxt?></td>
			</tr>
			<tr>
				<th>발송번호</th>
				<td>
					<input type="text" id="sendphone" name="sendphone" value="<?=$sendPhone?>" class="inp_sty40"/>
				</td>
			</tr>			
			<tr id="subjectDisp" style="display:none;">
				<th>제목</th>
				<td>
					<input type="text" id="sms_subject" name="sms_subject" value="" class="inp_sty60"/>
				</td>
			</tr>			
			<tr>
				<th>문자 내용</th>
				<td>
					<textarea id="sms_content" name="sms_content" rows="15" cols="17"></textarea>
				</td>
			</tr>
			<tr>
				<th>발송타입</th>
				<td>
					<label><input type="radio" id="smstype1" name="smstype" value="S" class="inp_radio" checked="checked" /><span>단문</span></label>
					<label><input type="radio" id="smstype2" name="smstype" value="L" class="inp_radio" /><span>장문</span></label>
				</td>
			</tr>			
		</tbody>
	</table>
	
	<div class="btn_list">
		<a href="javascript:top.layerPopClose();" class="btn1">취소</a>
		<a href="javascript:sendSms();" class="btn2">보내기</a>
	</div>
	</form>
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>	