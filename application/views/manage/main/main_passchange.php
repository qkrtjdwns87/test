<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$passCycle = 0;
	if (isset($recordSet))
	{
		$passCycle = $recordSet['ORDER'];
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/manage/main_m/passchangewrite';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var searchIndex; //검색후 결과값 세팅될 index
		$(function() {
			
		});

		function sendPassChange(){
			if (trim($('#passcycle').val()) == ''){
				alert('변경주기를 입력해 주세요.');
				return;
			}	

			if (!IsNumber($('#passcycle').val())){
				alert('변경주기는 숫자만 가능합니다.');
				return;
			}	

			if (trim($('#passcycle').val()) == '0'){
				alert('변경주기를 입력해 주세요.');
				return;
			}	
						
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();	
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post">
	<div id="content">

		<div class="title">
			<h2>[비밀번호 변경주기 관리]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 비밀번호 변경주기 관리</div>
		</div>

		<div class="sub_title">
			- 회원의 비밀번호는 주기적으로 변경해 주는 것이 좋습니다.
			- 입력하신 기간동안 비밀번호를 사용한 회원에게 비밀번호 변경권유 메시지가 노출됩니다. 
		</div>
		
		<table class="write2">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th>비밀번호 변경주기</th>
					<td class="ag_l"><input type="text" id="passcycle" name="passcycle" value="<?=$passCycle?>" class="inp_sty10" /><span class="tdline">개월</span></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="javascript:sendPassChange();" class="btn2">저장</a>
		</div>
	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		