<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = ($pageMethod == 'writeshopformpop') ? '/manage/message_m/writeshoppop' : '/manage/message_m/writeuserpop';
	$submitUrl .= (!empty($msgType)) ? '/msgtype/'.$msgType : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
		    $('#sendall_yn').change(function() {
		        if($(this).is(":checked")) {
			        alert('전체회원대상은 성능상 문제로 보류된 상태입니다.\n전체발송은 당분간 할 수 없습니다.');
			        return;
					//$('#target_txt').val('');
					//$('#targetno').val('');
		        }else{

		        }
		    });
	    });
 	
		function sendMessage(){
			if ($('#targetno').val() == ''){
				alert('대상을 선택 하세요.');
				return;
			}
						
			if ($('#msg_content').val() == ''){
				alert('내용을 입력하세요.');
				return;
			}
						
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}

		function msgUserSearch(){
			var arrShop = $('#targetno').val().split(',');
			if (arrShop.length==10){
				alert('발송대상은 최대 10건까지만 가능합니다.');
				return;
			}			
			userSearch();
		}
		
		function msgShopSearch(){
			var arrShop = $('#targetno').val().split(',');
			if (arrShop.length==10){
				alert('발송대상은 최대 10건까지만 가능합니다.');
				return;
			}
			shopSearch();
		}
		
		function userResultSet(uno, uname){
			var targetNo=$('#targetno').val();
			var targetTxt=$('#target_txt').val();
			if (targetNo == ''){
				$('#targetno').val(uno);
				$('#target_txt').val(uname);
			}else{
				$('#targetno').val(targetNo+','+uno);
				$('#target_txt').val(targetTxt+','+uname);
			}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}	

		function shopResultSet(shopno, shopname, shopcode){
			var shop=shopname+'('+shopcode+')';
			var targetNo=$('#targetno').val();
			var targetTxt=$('#target_txt').val();
			if (targetNo == ''){
				$('#targetno').val(shopno);
				$('#target_txt').val(shop);
			}else{
				$('#targetno').val(targetNo+','+shopno);
				$('#target_txt').val(targetTxt+','+shop);
			}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}	
	</script>
<!-- popup -->
<div id="popup">
	<form name="form" method="post" enctype="multipart/form-data">
	<div class="title">
		<h3>[메세지 보내기]</h3>
	</div>
	
	<table class="write1">
		<colgroup><col width="20%" /><col /></colgroup>
		<tbody>
			<tr>
				<th>수신자</th>
				<td>
					<input type="text" id="target_txt" name="target_txt" value="<?=$sendUserTxt?>" class="inp_sty40" readonly/>
					<input type="hidden" id="targetno" name="targetno" value="<?=$sendUserNum?>"/>
					<!-- 
					<?if ($pageMethod == 'writeformshop'){?>
					<a href="javascript:shopSearch();" class="btn2">찾아보기</a>
					<?}else{?>
					<a href="javascript:userSearch();" class="btn2">찾아보기</a>						
					<?}?>
					<p class="ex">* 수신자는 1회에 최대 10건까지 지정 가능합니다</p><br />
					<label><input type="checkbox" id="sendall_yn" name="sendall_yn" value="Y" class="inp_check" />전체 <?//$tblTitle?></label>
					 -->
				</td>
			</tr>
			<tr>
				<th>내용</th>
				<td>
					<textarea id="msg_content" name="msg_content" rows="5" cols="5" class="textarea1"></textarea>
				</td>
			</tr>
			<tr>
				<th>이미지첨부</th>
				<td><input type="file" id="userfile0" name="userfile0" class="inp_file mg_t10" /></td>
			</tr>
		</tbody>
	</table>
	
	<div class="btn_list">
		<a href="javascript:top.layerPopClose();" class="btn1">취소</a>
		<a href="javascript:sendMessage();" class="btn2">보내기</a>
	</div>
	</form>
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>	