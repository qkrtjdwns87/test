<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$ordStateTitle = mb_substr($this->common->getCodeTitleByCodeNum($orderState), 0, 2, 'UTF-8');
	$submitUrl = '/manage/order_m/deliverywrite/ordno/'.$ordNum.'/ordptno/'.$ordPtNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		function sendDeliveryInfo(){
			if (trim($('#deliverytype').val()) == ''){
				alert('택배사를 선택 하세요.');
				return;
			}			

			if (trim($('#invoiceno').val()) == ''){
				alert('운송번호를 입력 하세요.');
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
		<h3>[배송정보 등록]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="25%" /></colgroup>
		<tbody>
			<tr>
				<th>택배사</th>
				<td>
					<select id="deliverytype" name="deliverytype">
						<option value="">택배사선택</option>
					<?
						$i = 2;
						foreach ($deliCdSet as $crs):
							if ($crs['NUM'] > 10000)
							{
								$sel_chk = (strpos($deliveryType, $crs['NUM']) !== FALSE) ? 'selected="selected"' : '';								
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
				<th>운송장번호</th>
				<td><input type="text" id="invoiceno" name="invoiceno" class="inp_sty90" onkeydown="javascript:if(event.keyCode==13){sendDeliveryInfo(); return false;}" /></td>
			</tr>
		</tbody>
	</table>
	</form>
	
	<div class="btn_list ag_c">
		<a href="javascript:sendDeliveryInfo();" class="btn3">저장</a>
	</div>
	
</div>
<!-- //popup -->	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>			