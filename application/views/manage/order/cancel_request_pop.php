<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$ordStateTitle = mb_substr($this->common->getCodeTitleByCodeNum($orderState), 0, 2, 'UTF-8');
	
	$submitUrl = '/manage/order_m/cancelreq/ordno/'.$ordNum.'/ordptno/'.$ordPtNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		function sendReason(){
			if (trim($('#recision_code').val()) == ''){
				alert('사유를 선택하셔야 합니다.');
				return;
			}

			if (trim($('#reason_content').val()) == ''){
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
	<div class="title">
		<h3>[<?=$ordStateTitle?>신청사유]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>사유선택</th>
				<td>
					<select id="recision_code" name="recision_code">
						<option value="" selected="selected">선택</option>
					<?
						$i = 2;
						foreach ($cancelCdSet as $crs):
							if ($crs['NUM'] > 6300)
							{
								$sel_chk = '';//(strpos($reaSet['RECISIONCODE_NUM'], $crs['NUM']) !== FALSE) ? 'selected="selected"' : '';								
					?>
						<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
					<?
								$i++;					
							}
						endforeach;					
					?>	
					</select>
				</td>
			</tr>
			<tr>
				<th>상세 사유</th>
				<td>
					<textarea id="reason_content" name="reason_content" rows="5" cols="5" class="textarea1"><?//echo $reaSet['REASON_CONTENT']?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
	
	<div class="btn_list ag_c">
		<a href="javascript:sendReason();" class="btn1">확인</a>
	</div>
	
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>		