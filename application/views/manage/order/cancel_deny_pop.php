<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	if (empty($reaSet))
	{
		$this->common->message('작성할 원본사유가 없습니다.', 'reload', 'parent');
	}
	
	$ordStateTitle = mb_substr($this->common->getCodeTitleByCodeNum($orderState), 0, 2, 'UTF-8');
	$submitUrl = '/manage/order_m/denyreq/ordno/'.$ordNum.'/ordptno/'.$ordPtNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		function sendDeny(){
			if (trim($('#deny_reason').val()) == ''){
				alert('사유를 입력 하세요.');
				return;
			}			

			document.form.target = 'hfrm';
			document.form.action = '<?=$submitUrl?>';
			document.form.submit();	
		}
	</script>
<!-- popup -->
<div id="popup">
	<form name="form" method="post">
	<input type="hidden" name="orderstate" value="<?=$orderState?>"/>
	<input type="hidden" name="hisno_org" value="<?=$reaSet['NUM']?>"/>	
	<div class="title">
		<h3>[<?=$ordStateTitle?>불가사유]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
		<?if (!empty($reaSet['REASON_CONTENT'])){?>
			<tr>
				<th>사유</th>
				<td><?=nl2br($reaSet['REASON_CONTENT'])?></td>
			</tr>		
		<?}?>
			<tr>
				<th>상세사유</th>
				<td>
					<span class="ex">*(최대150자)</span><br />
					<textarea id="deny_reason" name="deny_reason" rows="5" cols="5" class="textarea1"></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
	
	<div class="btn_list ag_c">
		<a href="javascript:sendDeny();" class="btn1">등록</a>
	</div>
	
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>		