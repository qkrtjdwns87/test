<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	/*
	 * 카드, 무통장, 휴대폰 결제 모두 진행을 시작하였으나
	 * 후에 환불 및 교환조건(혹은 부분취소)이 복잡하여
	 * 카드결제로만 프로세스를 맞추기로 함
	 * */
	
	$ors = $ordSet[0];
	$orderCode = $ors['ORDER_CODE'];
	$orderDate = $ors['CREATE_DATE'];
	$itemCount = $ors['TOTITEM_COUNT'];	
	$payCode = $ors['PAYCODE_NUM'];
	$ordPartCnt = $ors['TOTPART_COUNT'];
	
	$addUrl = (!empty($ordNum)) ? '/ordno/'.$ordNum : '';
	$addUrl .= (!empty($ordPtNum)) ? '/ordptno/'.$ordPtNum : '';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
		$(function() {


			//$("#paydateImg").click(function() { 
			//	$("#paydate").datepicker("show");
			//});
		<?
			for($i=0; $i<=$ordPartCnt; $i++)
			{		
		?>
			$( "#refund_date_<?=$i?>" ).datepicker({
				dateFormat: 'yy-mm-dd',
				prevText: '이전 달',
				nextText: '다음 달',
				monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
				monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
				dayNames: ['일','월','화','수','목','금','토'],
				dayNamesShort: ['일','월','화','수','목','금','토'],
				dayNamesMin: ['일','월','화','수','목','금','토'],
				showMonthAfterYear: true,
				yearSuffix: '년'    	
			});
				
			$("#refund_dateImg_<?=$i?>").click(function() { 
				$("#refund_date_<?=$i?>").datepicker("show");
			});
		<?
			}
		?>
		});

		function sendPayInfo(){
			document.form.target = 'hfrm';
			document.form.action = "/manage/order_m/payinfoupdate/ordno/<?=$ordNum?>";
			document.form.submit();				
		}

		function changePayment(index, type, ordptno, maxamount){
			if (type == 'refund' || type == 'refundall' || type == 'refundonce'){
				if (trim($('#refund_bankacoount_name_'+index).val()) == ''){
					alert('예금주명을 입력하세요.');
					return;
				}

				if (trim($('#refund_bank_'+index).val()) == ''){
					alert('은행을 선택하세요.');
					return;
				}

				if (trim($('#refund_bankacoount_'+index).val()) == ''){
					alert('계좌번호를 입력하세요.');
					return;
				}

				if (trim($('#refund_amount_'+index).val()) == ''){
					alert('환불금액을 입력하세요.');
					return;
				}

				if (!IsNumber($('#refund_amount_'+index).val())){
					alert('환불금액은 숫자로만 입력하세요.');
					return;
				}		

				if ($('#refund_amount_'+index).val() > maxamount){
					alert('환불금액이 결제금액을 초과할 수 없습니다.');
					return;				
				}	

				if (trim($('#refund_date_'+index).val()) == ''){
					alert('환불일자를 선택하세요.');
					return;
				}
			}
			
			if (confirm('진행하시겠습니까?')){
				var param = '?bankacoount_name='+$('#refund_bankacoount_name_'+index).val();
				param += '&bank='+$('#refund_bank_'+index).val();
				param += '&bankacoount='+$('#refund_bankacoount_'+index).val();
				if (type == 'refund' || type == 'refundall' || type == 'refundonce'){
					param += '&amount='+$('#refund_amount_'+index).val();
				}else{
					param += '&amount='+$('#cancel_amount_'+index).val();
				}
				param += '&date='+$('#refund_date_'+index).val();
				param += '&type='+type;
				hfrm.location.href = '/manage/order_m/paymentupdate/ordno/<?=$ordNum?>/ordptno/'+ordptno+param;
				//location.href = '/manage/order_m/paymentupdate/ordno/<?=$ordNum?>/ordptno/'+ordptno+param;
			}			
		}
	</script>
<!-- popup -->
<div id="popup">
	<form name="form" method="post">
	<input type="hidden" id="paycode" name="paycode" value="<?=$payCode?>"/>
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
		<tbody>
			<tr>
				<th>주문번호</th>
				<td><?=$orderCode?></td>
				<th>주문일시</th>
				<td><?=$orderDate?></td>
			</tr>
		</tbody>
	</table>
	
	<table class="write2 mg_t10">
		<thead>
			<tr>
				<th  colspan="6">주문상세정보
					<a href="" class="btn1 fl_r">주문서 인쇄</a><!-- <a href="" class="btn1 fl_r">거래증빙 인쇄</a> -->
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="tab1">
				<td><a href="/manage/order_m/ordinfo<?=$addUrl?>">주문 정보</a></td>
				<td class="on"><a href="/manage/order_m/ordpayinfo<?=$addUrl?>">결제 정보</a></td>
				<td><a href="/manage/order_m/orduserinfo<?=$addUrl?>">주문자 정보</a></td>
				<td><a href="/manage/order_m/ordrecinfo<?=$addUrl?>">수령인 정보</a></td>
				<td><a href="/manage/order_m/ordinfomemo<?=$addUrl?>">관리자 메모</a></td>
				<td><a href="/manage/order_m/ordinfohistory<?=$addUrl?>">변경내역</a></td>
			</tr>
		<tbody>
	</table>
		
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
		<thead>
			<tr>
				<th colspan="4">결제정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>총 구매금액</th>
				<td><?=number_format($ors['TOT_AMOUNT'])?>원</td>
				<th>총 배송비</th>
				<td><?=number_format($ors['TOTDELIVERY_PRICE'])?>원</td>
			</tr>
			<tr>
				<th>총 실결제금액</th>
				<td colspan="3"><span class="red"><?=number_format($ors['TOTFINAL_AMOUNT'])?></span>원</td>
			</tr>
		</tbody>
	</table>
	
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /><col /></colgroup>
		<thead>
			<tr>
				<th colspan="2">결제처리</th>
			</tr>
		</thead>
		<tbody>
		<?
			$payDate = $ors['PAY_DATE'];
			if (!empty($payDate))
			{
				$payDate = ($ors['PAYAUTO_YN'] == 'N') ? $payDate.'(수동)' : $ors['PAY_DATE'].'(자동)';
			}
		?>
			<tr>
				<th>결제확인일시</th>
				<td><?=$payDate?></td>
			</tr>
			<?
				if (in_array($payCode, array(5520, 5530))) //무통장
				{
			?>
			<tr>
				<th rowspan="3">상세정보</th>
				<td>
					<span class="tdline">예금주</span> <?=$ors['PAYRESULT_BANKACCOUNT_NAME']?><!-- <input type="text" id="result_bankacoount_name" name="result_bankacoount_name" value="<?=$ors['PAYRESULT_BANKACCOUNT_NAME']?>" class="inp_sty20" /> -->
				</td>
			</tr>
			<tr>
				<td class="bo_tn"><span class="tdline">입금계좌</span>
				<?
					//if (empty($ors['PAYRESULT_BANKCODE_NUM']) || $ors['PAYRESULT_BANKCODE_NUM'] == 12500)
					//{
						echo '['.$ors['PAYRESULT_CODENAME'].']';	
					//}
					/*
					else 
					{
				?>
					<select class="inp_select" id="result_bank" name="result_bank">
						<option value="" selected="selected">은행선택</option>
					<?
						$i = 2;
						foreach ($bankCdSet as $crs):
							if ($crs['NUM'] > 12500)
							{
								$sel_chk = (strpos($ors['PAYRESULT_BANKCODE_NUM'], $crs['NUM']) !== FALSE) ? 'selected="selected"' : '';								
					?>
						<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
					<?
								$i++;					
							}
						endforeach;					
					?>		
					</select>
				<?
					}
					*/
				?>
					<?=$ors['PAYRESULT_BANKACCOUNT']?><!-- <input type="text" id="result_bankacoount" name="result_bankacoount" value="<?=$ors['PAYRESULT_BANKACCOUNT']?>" class="inp_sty40" /> -->
				</td>
			</tr>
			<tr>
				<td class="bo_tn"><span class="tdline">입금일</span>
					<?=substr($ors['PAY_DATE'], 0, 10)?><!-- <input type="text" id="paydate" name="paydate" value="<?=substr($ors['PAY_DATE'], 0, 10)?>" class="inp_sty20" /><a href="javascript:void(0);" id="paydateImg" class="calendar"></a> -->
				</td>
			</tr>
			<?
				}
			?>			
			
			<?
				if (in_array($payCode, array(5510, 5560))) //카드결제,휴대폰
				{ 
					if ($payCode == 5510)
					{
						$cardMn = (!empty($ors['PAYRESULT_CARDMONTH'])) ? '('.$ors['PAYRESULT_CARDMONTH'].'개월)' : '';
						$payTitle = '신용카드 ['.$ors['PAYRESULT_CODENAME'].'] '.$cardMn;
					}
					else 
					{
						$payTitle = '휴대폰 결제';
					}
			?>
			<tr>
				<th rowspan="2">상세정보</th>
				<td><span class="tdline">결제인</span> <strong><?=$ors['ORDER_NAME']?></strong></td>
			</tr>
			<tr>
				<td class="bo_tn"><span class="tdline">결제정보</span><strong><?=$payTitle?></strong></td>
			</tr>
			<?
				}
			?>			
			<tr <?if (!in_array($payCode, array(5520, 5530))){?>style="display:none;"<?}?>>
				<th>현금영수증</th>
				<td>
					<label><input type="radio" id="cashreceipt_yn1" name="cashreceipt_yn" value="Y" <?if ($ors['CASHRECEIPT_YN'] == 'Y'){?>checked="checked"<?}?> class="inp_radio"/>신청</label>
					<label><input type="radio" id="cashreceipt_yn2" name="cashreceipt_yn" value="N" <?if ($ors['CASHRECEIPT_YN'] == 'N'){?>checked="checked"<?}?> class="inp_radio"/>미신청</label>
				</td>
			</tr>
			
			<tr>
				<th>세금계산서</th>
				<td class="bg_tn">
					<label><input type="radio" id="taxbill_yn1" name="taxbill_yn" value="Y" <?if ($ors['TAXBILL_YN'] == 'Y'){?>checked="checked"<?}?> class="inp_radio"/>신청</label>
					<label><input type="radio" id="taxbill_yn2" name="taxbill_yn" value="N" <?if ($ors['TAXBILL_YN'] == 'N'){?>checked="checked"<?}?> class="inp_radio"/>미신청</label>
				</td>
			</tr>
		</tbody>
	</table>

	
	<?if (in_array($payCode, array(5510, 5520, 5530))){ //무통장?>	
	<div class="btn_list">
		<a href="javascript:sendPayInfo();" class="btn2">저장</a>
	</div>	
	<?}?>
<?
	$i = 1;
	$tmpOrdPartNum = 0;
	$orderConfirmYn = $ors['ORDERCONFIRM_YN']; //PG사로부터 구매확인 여부를 받은 경우
	$orderConfirmMsg = '';
	if ($orderConfirmYn == 'N' && !empty($ors['ORDERCONFIRM_DATE']))
	{
		//구매확인 취소한 케이스(PG사로 부터 통보받는 내용)
		$orderConfirmMsg = $ors['ORDERCONFIRM_MSG'];
	}
	
	/************************************************************************************
	 ************************************************************************************
	 *
	 * 샵별 결제 상황 시작
	 * 
	 ************************************************************************************
	 *************************************************************************************/	
	foreach ($ordSet as $rs):
		if ($tmpOrdPartNum != $rs['ORDERPART_NUM']) //PART_NUM 중복 제거
		{
			$isDelivery = (!empty($rs['INVOICE_NO'])) ? TRUE : FALSE; //송장번호가 있으면 배송(중)상태
			if ($payCode == 5510)
			{
				$payTitle = '신용카드 ['.$rs['PAYRESULT_CODENAME'].'] '.$cardMn;
			}
			else if ($payCode == 5520)
			{
				$payTitle = '무통장 결제';
			}
			else if ($payCode == 5530)
			{
				$payTitle = '가상계좌 결제';
			}
			else if ($payCode == 5560)
			{
				$payTitle = '휴대폰 결제';
			}
			
			/************************************************************************************
			 *
			 * 한번에 전체 주문건 모두 취소 시작
			 * 슈퍼관리자이거나 주문번호안에 샵이 하나인 경우에도 아래 내용 출현
			 * 
			*************************************************************************************/
			if (($i == 1 && $ordPartCnt == 1) || ($i == 1 && $isAdmin))
			{
	?>
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /></colgroup>
		<thead>
			<tr>
				<th colspan="2">전체 결제 취소 (주문번호에 해당되는 모든 내용 취소)</th>
			</tr>
		</thead>
		<tbody>
			<?
				$isCancel = ($rs['CANCEL_YN'] == 'Y' && !empty($rs['CANCEL_END_DATE'])) ? TRUE : FALSE;
				//PG사 연동후 취소작업					
			?>
			<tr>
				<th>결제취소</th>
				<?
					if ($isCancel)
					{
				?>
				<td>
					<span class="tdline"><?=$payTitle?> 결제 취소 <span class="red">완료</span>(<?=$rs['CANCEL_END_DATE']?>)</span>
				</td>
				<?
					}
					else
					{
						
				?>				
				<td><span class="tdline bold"><?=$payTitle?> 취소</span>
					<span class="tdline" style="display:none;">취소금액</span>
					<input type="text" id="cancel_amount_0" name="cancel_amount" value="<?=$rs['TOTFINAL_AMOUNT']?>" class="inp_sty10" style="display:none;" /><span class="tdline" style="display:none;">원(배송비포함)</span>				
					<a href="javascript:changePayment('0', 'cancelonce', '0', <?=$rs['PART_AMOUNT']+$rs['DELIVERY_PRICE']?>);" class="btn2">취소</a><br />
					<span class="ex"> ※ 취소 버튼 클릭 시 실제 PG 승인취소가 진행됩니다. <br />※ 취소는 전체취소만 가능합니다.</span>
				</td>
				<?
					}
				?>
			</tr>
		</tbody>
	</table>
	<?
			}
			/************************************************************************************
			 *
			 * 한번에 전체 주문건 모두 취소 끝
			 * 
			*************************************************************************************/
			
			/************************************************************************************
			 * 주문번호안에 샵이 여러개인 경우 시작
			 *************************************************************************************/			
			if ($ordPartCnt > 1) 
			{
	?>
	
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /></colgroup>
		<thead>
			<tr>
				<th colspan="2">[<?=$rs['SHOP_NAME']?>] 결제 내용 변경</th>
			</tr>
		</thead>
		<tbody>
			<?
				/************************************************************************************
				 * 무통장, 가상계좌 환불 시작
				 *************************************************************************************/			
				if (in_array($payCode, array(5520, 5530)))
				{
			?>			
			<tr>
				<th rowspan="3">상세정보</th>
				<td><span class="tdline">예금주</span> <input type="text" id="refund_bankacoount_name_<?=$i?>" name="refund_bankacoount_name" value="<?=$rs['REFBANKACCOUNT_NAME']?>" class="inp_sty20" /></td>
			</tr>		
			<tr>
				<td class="bo_tn"><span class="tdline">환불방법</span>
					<select class="inp_select" id="refund_bank_<?=$i?>" name="refund_bank">
						<option value="" selected="selected">은행선택</option>
					<?
						$t = 2;
						foreach ($bankCdSet as $crs):
							if ($crs['NUM'] > 12500)
							{
								$sel_chk = (strpos($rs['REFBANKCODE_NUM'], $crs['NUM']) !== FALSE) ? 'selected="selected"' : '';								
					?>
						<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
					<?
								$t++;					
							}
						endforeach;					
					?>		
					</select>
					<input type="text" id="refund_bankacoount_<?=$i?>" name="refund_bankacoount" value="<?=$rs['REFBANKACCOUNT']?>" class="inp_sty40" />
				</td>
			</tr>
			
			<tr>
				<td class="bo_tn">
					<span class="tdline">환불금액</span>
					<input type="text" id="refund_amount_<?=$i?>" name="refund_amount" value="<?=$rs['PART_AMOUNT']+$rs['DELIVERY_PRICE']?>" class="inp_sty10" /><span class="tdline">원(배송비포함)</span>
					<span class="tdline">환불일</span>
					<input type="text" id="refund_date_<?=$i?>" name="refund_date" value="<?=substr($rs['REFUND_END_DATE'], 0, 10)?>" class="inp_sty20" readonly/><a href="javascript:void(0);" id="refund_dateImg_<?=$i?>" class="calendar">
				</td>
			</tr>
			<tr>
				<th>결제환불</th>
				<?
					$isRefund = ($rs['REFUND_YN'] == 'Y') ? TRUE : FALSE; //환불이 됐었는지 여부				
					if ($isRefund)
					{
				?>
				<td>
					<span class="tdline"><?=$payTitle?> 결제 환불 <span class="red">완료</span>(<?=$rs['REFUND_END_DATE']?>)</span>
				</td>
				<?
					}
					else
					{
				?>				
				<td>
					<span class="tdline bold"><?=$payTitle?> <?if ($orderConfirmYn == 'Y'){?>환불가능<?}else{?>환불불가능 (구매자 구매확인 후 가능합니다.)<?}?></span>
					<?if ($orderConfirmYn == 'Y'){?>
					<a href="javascript:changePayment('<?=$i?>', 'refundpart', '<?=$rs['ORDERPART_NUM']?>', <?=$rs['PART_AMOUNT']+$rs['DELIVERY_PRICE']?>);" class="btn2">환불</a><br />
					<span class="ex"> ※환불 버튼 클릭 시 실제 PG 승인 환불이 진행됩니다. <br />※ 환불은 1회만 가능합니다.</span>
					<?}?>
				</td>
				<?
					}
				?>
			</tr>
			<?
				}
				/************************************************************************************
				 * 무통장, 가상계좌 환불 끝
				 *************************************************************************************/
				
				$isCancel = ($rs['CANCEL_YN'] == 'Y' && !empty($rs['CANCEL_END_DATE'])) ? TRUE : FALSE;
				//PG사 연동후 취소작업	
				
				/************************************************************************************
				 * 결제 취소 시작 (카드, 무통장, 휴대폰)
				 *************************************************************************************/				
			?>
			<tr>
				<th>결제취소</th>
			<?
				if ($isCancel)
				{
			?>
				<td>
					<span class="tdline"><?=$payTitle?> 결제 취소 <span class="red">완료</span>(<?=$rs['CANCEL_END_DATE']?>)</span>
				</td>
			<?
				}
				else
				{
					//카드결제만 고려함
					//부분취소는 구매확인후에만 가능함
					if (!empty($orderConfirmMsg)) $orderConfirmMsg = '<br />- 구매자가 구매확인 취소함<br />- 사유 : '.$orderConfirmMsg
			?>				
				<td><span class="tdline bold"><?=$payTitle.$orderConfirmMsg?> <br />(<?if ($orderConfirmYn == 'Y'){?>취소가능<?}else{?><?=$rs['SHOP_NAME']?>만 취소불가능 - 구매자 구매확인 후 취소가 가능합니다.<?}?>)</span>
					<?if ($orderConfirmYn == 'Y'){?>
					<span class="tdline" style="display:none;">취소금액</span>
					<input type="text" id="cancel_amount_<?=$i?>" name="cancel_amount" value="<?=$rs['PART_AMOUNT']+$rs['DELIVERY_PRICE']?>" class="inp_sty10" style="display:none;" /><span class="tdline" style="display:none;">원(배송비포함)</span>				
					<a href="javascript:changePayment('<?=$i?>', 'cancelpart', '<?=$rs['ORDERPART_NUM']?>', <?=$rs['PART_AMOUNT']+$rs['DELIVERY_PRICE']?>);" class="btn2">취소</a><br />
					<span class="ex"> ※ 취소 버튼 클릭 시 실제 PG 승인취소가 진행됩니다. </span>
					<?}?>
				</td>
			<?
				}
			?>
			</tr>
			<?
				/************************************************************************************
				 * 결제 취소 끝 (카드, 무통장, 휴대폰)
				 *************************************************************************************/				
			?>
		</tbody>
	</table>
<?	
			}
			/************************************************************************************
			 * 주문번호안에 샵이 여러개인 경우 끝
			 *************************************************************************************/
		}
		$tmpOrdPartNum = $rs['ORDERPART_NUM'];
		$i++;
		
	endforeach;	
	/************************************************************************************
	 ************************************************************************************
	 *
	 * 샵별 결제 상황 끝
	 *
	 ************************************************************************************
	 *************************************************************************************/	
?>
	</form>


</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>		