<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$orderCode = $ordSet[0]['ORDER_CODE'];
	$orderDate = $ordSet[0]['CREATE_DATE'];
	$itemCount = $ordSet[0]['TOTITEM_COUNT'];
	
	$addUrl = (!empty($ordNum)) ? '/ordno/'.$ordNum : '';
	$addUrl .= (!empty($ordPtNum)) ? '/ordptno/'.$ordPtNum : '';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var cancelSinum;
		var refundSinum;
		var changeSinum;
		
		function grpOrderChange(){
			var ordst = $('#ordstate').val();
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}

			if (ordst == ''){
				alert('변경하고자 하는 상태값을 선택하세요.');
				return;
			}
			
			var arrSt=ordst.split('|');
			var stNum=arrSt[0];	//상태값
			var stTitle=arrSt[1]; //상태값title
			var isForbid=false; //취소,환불,교환 금지
			var arrSel=sel.split(',');	//arrsel [주문아이템고유번호|아이템고유번호]
			var arrOrd;
			var ordiNo;
			var ordItNo;
			var arrIt;
			var itemNo; 
			var itemNm;
						
			if (cancelSinum.length > 0 && stNum==5110){
				var arrCan=cancelSinum.split(',');
				for(i=0; i<arrCan.length;i++){
					arrIt=arrCan[i].split('|');
					itemNo=arrIt[0]; 
					itemNm=arrIt[1];						
					for(j=0; j<arrSel.length;j++){
						arrOrd=arrSel[j].split('|');
						ordiNo=arrOrd[0];
						ordItNo=arrOrd[1];
						if (itemNo==ordItNo){
							isForbid=true;
							break;
						}
					}
				}  
			}

			if (!isForbid){
				if (refundSinum.length > 0 && stNum==5130){
					var arrRef=refundSinum.split(',');
					for(i=0; i<arrRef.length;i++){
						arrIt=arrRef[i].split('|');
						itemNo=arrIt[0]; 
						itemNm=arrIt[1];						
						for(j=0; j<arrSel.length;j++){
							arrOrd=arrSel[j].split('|');
							ordiNo=arrOrd[0];
							ordItNo=arrOrd[1];							
							if (itemNo==ordItNo){
								isForbid=true;
								break;
							}
						}
					}  
				}				
			}

			if (!isForbid){
				if (changeSinum.length > 0 && stNum==5190){
					var arrChg=changeSinum.split(',');
					for(i=0; i<arrChg.length;i++){
						arrIt=arrChg[i].split('|');
						itemNo=arrIt[0]; 
						itemNm=arrIt[1];						
						for(j=0; j<arrSel.length;j++){
							arrOrd=arrSel[j].split('|');
							ordiNo=arrOrd[0];
							ordItNo=arrOrd[1];							
							if (itemNo==ordItNo){
								isForbid=true;
								break;
							}
						}
					}  
				}				
			}

			var msg='';		
			if (isForbid){
				msg='아이템중 ' + stTitle + ' 불가항목으로 지정된 아이템이 있습니다.\n';
				msg+='아이템은 '+itemNm+' 입니다.\n';
				msg+='정말로 ' + stTitle + ' 처리하시겠습니까?';
			}else{
				msg='선택한 주문내용을 ' + stTitle + ' 상태로 변경 하시겠습니까?';
			}			
						
			if (confirm(msg)){
				var url = '/manage/order_m/change';
				url += '/return_url/' + $.base64.encode(location.pathname + location.search);				
				hfrm.location.href = url + '?selval='+sel+'&orderstate='+stNum+'&target=parent';	
				//location.href = url + '?selval='+sel+'&orderstate='+stNum+'&target=parent';
			}
		}
	</script>
<!-- popup -->
<div id="popup">
	<form name="form" method="post">
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
					<a href="javascript:;" class="btn1 fl_r">주문서 인쇄</a><!-- <a href="" class="btn1 fl_r">거래증빙 인쇄</a> -->
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="tab1">
				<td class="on"><a href="/manage/order_m/ordinfo<?=$addUrl?>">주문 정보</a></td>
				<td><a href="/manage/order_m/ordpayinfo<?=$addUrl?>">결제 정보</a></td>
				<td><a href="/manage/order_m/orduserinfo<?=$addUrl?>">주문자 정보</a></td>
				<td><a href="/manage/order_m/ordrecinfo<?=$addUrl?>">수령인 정보</a></td>
				<td><a href="/manage/order_m/ordinfomemo<?=$addUrl?>">관리자 메모</a></td>
				<td><a href="/manage/order_m/ordinfohistory<?=$addUrl?>">변경내역</a></td>
			</tr>
		<tbody>
	</table>
		
	<div class="sub_title mg_t10">
		<strong class="fl_l font15">주문정보</strong>
		<div class="fl_r">
			<span class="tdline">주문상태를</span>
			<select class="inp_select" id="ordstate" name="ordstate">
				<option value="" selected="selected">상태값 선택</option>
			<?
				foreach ($ordStCdSet as $crs):
					if ($crs['NUM'] >= 5050)
					{
						if (!in_array($crs['NUM'], array(5150))) //5120, 
						{
			?>			
				<option value="<?=$crs['NUM'].'|'.$crs['TITLE']?>"><?=$crs['TITLE']?></option>
			<?
						}
					}
				endforeach;					
			?>			
			</select> 
			<span class="tdline">으로</span>
			<a href="javascript:grpOrderChange();" class="btn1">변경</a><span class="tdline"></span>
		</div>
	</div>
	
	<table class="write2">
		<thead>
			<tr>
				<th><span style="display:none;"><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" checked="checked"/></span>주문Item / 옵션</th>
				<th>수량</th>
				<th>구매금액</th>
				<th>배송비</th>
				<th>Craft Shop</th>
				<th>배송정보</th>
				<th>주문상태</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = $maxOrder = $rowSpanCnt = 1;
	    	$defaultImg = '/images/adm/@thumb.gif';
	    	$tmpOrderPart = 0;
	    	foreach ($ordSet as $rs):
				$img = '';
				$arrFile = (!empty($rs['FIRST_FILE_INFO'])) ? explode('|', $rs['FIRST_FILE_INFO']) : array();	    	
				if (count($arrFile) > 0)
				{
					if ($arrFile[4] == 'Y')	//썸네일생성 여부
					{
						$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
					}
					else
					{
						$img = $arrFile[2].$arrFile[3];
					}
				}
				$fileName = (!empty($img)) ? $img : $defaultImg;				
				
				$itemTitle = $rs['ITEM_NAME'];
				$itemNum = $rs['SHOPITEM_NUM'];
				$itemUrl = '/manage/item_m/updateform/sino/'.$itemNum;
				$arrOpt = (!empty($rs['ITEMOPTION_INFO'])) ? explode('-', $rs['ITEMOPTION_INFO']) : array();
				
				$cancelForbidSiNum = ($rs['PAYAFTER_CANCEL_YN'] == 'N') ? $itemNum.'|'.$itemTitle.',' : '';
				$refundForbidSiNum = ($rs['MADEAFTER_REFUND_YN'] == 'N') ? $itemNum.'|'.$itemTitle.',' : '';
				$changeForbidSiNum = ($rs['MADEAFTER_CHANGE_YN'] == 'N') ? $itemNum.'|'.$itemTitle.',' : '';
				
				$rowSpan = '';
				if ($tmpOrderPart != $rs['PART_ORDER'])
				{
					//페이지 넘김후에도 이어지는 경우를 위해서도 order를 이용
					$rowSpan = 'rowspan="'.$rs['ITEM_ORDER'].'"'; //최초 rowspan 선언
				}
		?>	
			<tr>
				<td>
					<ul class="list">
						<li><img src="<?=CDN.$fileName?>" width="100" height="100" alt="" /></li>
						<li style="text-align:left;"><span style="display:none;"><input type="checkbox" id="chkCheck<?=$rs['SHOPITEM_NUM']?>" name="chkCheck" value="<?=$rs['ORDERPART_NUM'].'|'.$rs['SHOPITEM_NUM']?>" class="inp_check" checked="checked"/></span><a href="<?=$itemUrl?>" class="alink" target="_blank"><?=$itemTitle?></a></li>
					<?
						foreach ($arrOpt as $ot) //옵션선택사항
						{
							$arrOptInfo = explode('|', $ot);
					?>
						<li style="text-align:left;margin-left:10px;">-<?=$arrOptInfo[0]?>: <?=$arrOptInfo[2]?></li>
					<?
						}
					?>						
					</ul>
				</td>
				<td width="8%"><?=number_format($rs['QUANTITY'])?></td>
				<td width="11%"><?=number_format($rs['AMOUNT'])?>원</td>
			<?if ($tmpOrderPart != $rs['PART_ORDER']){?>				
				<td width="9%" <?=$rowSpan?>><?=number_format($rs['DELIVERY_PRICE'])?>원</td>
				<td width="15%" <?=$rowSpan?>><?=$rs['SHOP_NAME']?><br />(<?=$rs['SHOP_CODE']?>)</td>
				<td width="18%" <?=$rowSpan?>>
				<?if (!empty($rs['INVOICE_NO'])){?>
					<p><?=$rs['DELIVERYCODE_TITLE']?><br /><?=$rs['INVOICE_NO']?><br />(<?=subStr($rs['INVOICE_WRITE_DATE'], 0, 10)?>) </p>
					<a href="#" class="btn2">배송추적</a><br /><br />
				<?}?>
				<?if ($rs['ORDSTATECODE_NUM'] < 5110){ //아이템준비단계까지?>
					<a href="javascript:deliveryInfoRequest('<?=$rs['ORDSTATECODE_NUM']?>', '<?=$rs['ORDERS_NUM']?>', '<?=$rs['ORDERPART_NUM']?>');" class="btn2">등록</a>
				<?}?>				
				</td>
				<td width="18%" <?=$rowSpan?>><?=$rs['ORDSTATECODE_TITLE']?><br /><br />
				<?
					if (in_array($rs['ORDSTATECODE_NUM'], array(5110, 5130, 5160, 5190)))
					{
						$ordStTitle = mb_substr($rs['ORDSTATECODE_TITLE'], 0, 2, 'UTF-8');
				?>
					<a href="javascript:cancelRequest('<?=$rs['ORDSTATECODE_NUM']?>', '<?=$rs['ORDERS_NUM']?>', '<?=$rs['ORDERPART_NUM']?>');" class="btn2"><?=$ordStTitle?>사유등록</a><br /><br />
					<a href="javascript:denyRequest('<?=$rs['ORDSTATECODE_NUM']?>', '<?=$rs['ORDERS_NUM']?>', '<?=$rs['ORDERPART_NUM']?>');" class="btn2">불가사유등록</a>
				<?
					}
				?>
				</td>
			<?}?>				
			</tr>
		<?
				$tmpOrderPart = $rs['PART_ORDER'];
				$i++;
			endforeach;	
			
			$cancelForbidSiNum = (strlen($cancelForbidSiNum) > 0) ? substr($cancelForbidSiNum, 0, -1) : '';
			$refundForbidSiNum = (strlen($refundForbidSiNum) > 0) ? substr($refundForbidSiNum, 0, -1) : '';
			$changeForbidSiNum = (strlen($changeForbidSiNum) > 0) ? substr($changeForbidSiNum, 0, -1) : '';
		?>					
		</tbody>
	</table>
	<script type="text/javascript">
		cancelSinum='<?=$cancelForbidSiNum?>';
		refundSinum='<?=$refundForbidSiNum?>';
		changeSinum='<?=$changeForbidSiNum?>';
	</script>	
	</form>
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>			