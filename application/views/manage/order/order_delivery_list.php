<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/order_m/deliverylist'.$addUrl;
	$writeNewUrl = '/manage/order_m/writeform'.$addUrl;
	$deleteUrl = '/manage/order_m/grpdelete';
	//사유는 상세의 히스토리를 봐야함
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
		$(function() {
			$( "#sdate, #edate" ).datepicker({
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

			$("#sdateImg").click(function() { 
				$("#sdate").datepicker("show");
			});
			$("#edateImg").click(function() { 
				$("#edate").datepicker("show");
			});			
		});

		function search(){
			var pay_sel = getCheckboxSelectedValue('paytype');
			var state_sel = getCheckboxSelectedValue('orderstate');
			$('#grppaytype').val(pay_sel);
			$('#grporderstate').val(state_sel);
			
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function grpOrderChange(ordstate, methodTitle){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
						
			if (confirm('선택한 주문내용을 ' + methodTitle + ' 처리 하시겠습니까?')){

				var url = '/manage/order_m/change';
				url += '/return_url/' + $.base64.encode(location.pathname + location.search);				
				hfrm.location.href = url + '?selval='+sel+'&orderstate='+ordstate+'&target=top';	
			}
		}

		function deliveryInfoWrite(index, orderstate, ordnum, ordptnum){
			if (trim($('#deliverytype_'+index).val()) == ''){
				alert('택배사를 선택 하세요.');
				return;
			}			

			if (trim($('#invoiceno_'+index).val()) == ''){
				alert('운송번호를 입력 하세요.');
				return;
			}	
						
			var url='/manage/order_m/deliverywrite/ordno/'+ordnum+'/ordptno/'+ordptnum;
			url +='?orderstate='+orderstate;
			url +='&deliverytype='+$('#deliverytype_'+index).val();
			url +='&invoiceno='+$('#invoiceno_'+index).val();
			hfrm.location.href=url;						
		}			
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[배송현황관리]</h2>
			<div class="location">Home &gt; 주문관리 &gt; 배송현황관리</div>
		</div>
		
		<?
			$col1 = 0;
			$col2 = 0;
			$col3 = 0;
			$col4 = 0;
			
			if (isset($ordStatsToDaySet))
			{
				$col1 = $ordStatsToDaySet['STATE_5100'];				
				$col2 = $ordStatsToDaySet['STATE_5220'];
				$col3 = $ordStatsToDaySet['STATE_5240'];
				$col4 = $ordStatsToDaySet['STATE_5250'];
			}			
			
			//일자를 무시한 통계인 경우
			/*			
			if (isset($ordStatsToDaySet[0]['STATE_5100']))
			{			
				foreach ($ordStatsToDaySet as $srs):
					$col1 = $srs['STATE_5100'];
				endforeach;
				
				foreach ($ordStatsSet as $srs):
					$col2 = $srs['STATE_5220'];
					$col3 = $srs['STATE_5240'];			
					$col4 = $srs['STATE_5250'];
				endforeach;
			}
			*/
		?>
		<table class="write2">
			<colgroup><col width="25%" /><col width="25%" /><col width="25%" /><col width="25%" /></colgroup>
			<thead>
				<tr>
					<th>Item준비중</th>
					<th>배송중</th>
					<th>Item준비중(교환Item)</th>
					<th>배송중(교환Item)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#"><span class="blue bold"><?=number_format($col1)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col2)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col3)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col4)?></span>건 </a></td>
				</tr>
			</tbody>
		</table>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<input type="hidden" id="grppaytype" name="grppaytype" />		
		<input type="hidden" id="grporderstate" name="grporderstate" />
		<table class="write1 mg_t10">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>검색어</th>
					<td colspan="3">
						<select class="inp_select" id="ordsearchkey" name="ordsearchkey">
							<option value="ordcode" <?if ($ordSearchKey=='ordcode'){?>selected="selected"<?}?>>주문번호</option>
							<option value="ordname" <?if ($ordSearchKey=='ordname'){?>selected="selected"<?}?>>주문자명</option>
							<option value="ordmobile" <?if ($ordSearchKey=='ordmobile'){?>selected="selected"<?}?>>주문자연락처</option>
							<option value="ordrecname" <?if ($ordSearchKey=='ordrecname'){?>selected="selected"<?}?>>수령인명</option>
							<option value="ordrecmobile" <?if ($ordSearchKey=='ordrecmobile'){?>selected="selected"<?}?>>수령인연락처</option>
							<option value="ordinvoiceno" <?if ($ordSearchKey=='ordinvoiceno'){?>selected="selected"<?}?>>운송장번호</option>																												
						</select>
						<input type="text" id="ordsearchword" name="ordsearchword" value="<?=$ordSearchWord?>" class="inp_sty40" />
					</td>
				</tr>
				<tr>
					<th>주문상태 <label><input type="checkbox" id="orderstatecheck" name="orderstatecheck" value="all" onclick="javascript:AllCheckBoxCheck('orderstate',this.id);" class="inp_check" />전체선택</label></th>
					<td colspan="3">
					<?
						$i = 2;
						foreach ($ordStCdSet as $crs):
							if (in_array($crs['NUM'], array(5100, 5220, 5240, 5250)))
							{
								$sel_chk = (strpos($grpOrderState, $crs['NUM']) !== FALSE) ? 'checked="checked"' : '';								
					?>					
						<label><input type="checkbox" id="orderstate<?=$i?>" name="orderstate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_check" /><?=$crs['TITLE']?></label>
					<?
								$i++;					
							}
						endforeach;					
					?>							
					</td>
				</tr>			
				<tr>
					<th>Item명</th>
					<td><input type="text" id="itemname" name="itemname" value="<?=$itemName?>" class="inp_sty40" /></td>
					<th>Item코드</th>
					<td><input type="text" id="itemcode" name="itemcode" value="<?=$itemCode?>" class="inp_sty40"  placeholder="코드 8자리 입력" /></td>
				</tr>
				<?if ($isAdmin){?>
				<tr>
					<th>Craft Shop명</th>
					<td><input type="text" id="shopname" name="shopname" value="<?=$shopName?>" class="inp_sty40" /></td>
					<th>Craft Shop코드</th>
					<td><input type="text" id="shopcode" name="shopcode" value="<?=$shopCode?>" class="inp_sty40"  placeholder="코드 8자리 입력" /></td>
				</tr>
				<?}?>
				<tr>
					<th>배송업체</th>
					<td>
						<select class="inp_select" id="deliverytype" name="deliverytype">
							<option value="">업체선택</option>
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
					<th>송장발송여부</th>
					<td>
						<label><input type="radio" id="invoiceyn1" name="invoiceyn" value="" <?if (empty($invoiceYn)){?>checked="checked"<?}?> class="inp_check" />발행</label>					
						<label><input type="radio" id="invoiceyn2" name="invoiceyn" value="Y" <?if ($invoiceYn == 'Y'){?>checked="checked"<?}?> class="inp_check" />발행</label>
						<label><input type="radio" id="invoiceyn3" name="invoiceyn" value="N" <?if ($invoiceYn == 'N'){?>checked="checked"<?}?> class="inp_check" />미발행</label>
					</td>
				</tr>				
				<tr>
					<th>기간선택</th>
					<td colspan="3">
						<select class="inp_select" id="datesearchkey" name="datesearchkey">
							<option value="order" <?if ($dateSearchKey=='order'){?>selected="selected"<?}?>>주문일</option>
							<option value="pay" <?if ($dateSearchKey=='pay'){?>selected="selected"<?}?>>결제일</option>							
							<option value="payverify" <?if ($dateSearchKey=='payverify'){?>selected="selected"<?}?>>입금확인일</option>							
						</select>
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
		
		<div class="sub_title">총 <?=number_format($rsTotalCount)?>건</div>
		<table class="write2">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>주문번호</th>
					<th>주문일시</th>
					<th>주문Item</th>
					<th>Craft Shop</th>
					<th>주문자<br />(연락처)</th>
					<th>수령인<br />(연락처)</th>
					<th>배송비</th>
					<th>총결제금액</th>
					<th>결제수단</th>
					<th>배송상태<br />(발송일)</th>
					<th>배송정보 입력</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = $maxOrder = $rowSpanCnt = 1;
		    	$tmpOrderNum = $tmpOrderPart = 0;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/item_m/updateform/ordno/'.$rs['NUM'].$addUrl;
					
					$orderNum = $rs['NUM'];
					$itemNum = 0;
					$itemUrl = $itemTitle = '';		
					
					$arrItem = (!empty($rs['FIRST_ITEM_INFO'])) ? explode('|', $rs['FIRST_ITEM_INFO']) : array();
					if (count($arrItem) > 0)
					{
						$itemNum = $arrItem[0];
						$itemTitle = $arrItem[1];
						$itemUrl = '/manage/item_m/updateform/sino/'.$itemNum;						
					}
					
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
					$deliveryTitle = (!empty($rs['DELIVERYCODE_TITLE'])) ? $rs['DELIVERYCODE_TITLE'].'<br />('.substr($rs['DELIVERY_DATE'], 0, 10).')' : ''; 
					$itemTitle = ($rs['PARTITEM_COUNT'] > 1) ? $itemTitle.'외'.($rs['PARTITEM_COUNT'] -1).'개' : $itemTitle;

					$rowSpan = '';
					$partOrder = ($userLevelType == 'SHOP') ? 1 : $rs['PART_ORDER'];
					if ($orderNum != $tmpOrderNum && $i > 1)
					{
						$maxOrder = $rowSpanCnt = 0;
					}
					else 
					{
						if ($partOrder > 1 && $rowSpanCnt == 1)
						{
							//페이지 넘김후에도 이어지는 경우를 위해서도 order를 이용
							$rowSpan = 'rowspan="'.$rs['PART_ORDER'].'"'; //최초 rowspan 선언
							$maxOrder = $rowSpanCnt = $rs['PART_ORDER'];  //td출현여부 결정
						}						
					}
			?>			
				<tr>
					<td width="3%"><input type="checkbox" id="chkCheck<?=$rs['ORDERPART_NUM']?>" name="chkCheck" value="<?=$rs['ORDERPART_NUM']?>" class="inp_check"/></td>				
				<?if ($maxOrder == $rowSpanCnt){?>				
					<td width="6%" <?=$rowSpan?>><a href="javascript:orderDetailList('<?=$orderNum?>', 'ordinfo');" class="alink"><?=$rs['ORDER_CODE']?></a></td>
					<td width="6%" <?=$rowSpan?>><?=$rs['CREATE_DATE']?></td>
				<?}?>
					<td><a href="<?=$itemUrl?>" class="alink" target="_blank"><?=$itemTitle?></a></td>
					<td width="8%"><?=$rs['SHOP_NAME']?><br />(<?=$rs['SHOP_CODE']?>)</td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="8%" <?=$rowSpan?>><?=$rs['ORDER_NAME']?><br />(<?=$rs['ORDER_MOBILE_DEC']?>)</td>
					<td width="8%" <?=$rowSpan?>><?=$rs['RECIPIENT_NAME']?><br />(<?=$rs['RECIPIENT_MOBILE_DEC']?>)</td>
				<?}?>					
					<td width="6%"><?=number_format($rs['DELIVERY_PRICE'])?></td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="7%" <?=$rowSpan?>><?=number_format($rs['TOTFINAL_AMOUNT'])?></td>
					<td width="8%" <?=$rowSpan?>><?=$rs['PAYCODE_TITLE']?></td>
				<?}?>					
					<td width="8%"><?=substr($rs['INVOICE_WRITE_DATE'], 0, 10)?></td>
					<td width="15%">
						<select class="inp_select" id="deliverytype_<?=$i?>" name="seldeliverytype_<?=$i?>">
							<option value="">택배사선택</option>
					<?
						$t = 2;
						foreach ($deliCdSet as $crs):
							if ($crs['NUM'] > 10000)
							{
								$sel_chk = (strpos($rs['DELIVERYCODE_NUM'], $crs['NUM']) !== FALSE) ? 'selected="selected"' : '';								
					?>						
							<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
					<?
								$t++;					
							}
						endforeach;					
					?>	
						</select><br />
						<input type="text" id="invoiceno_<?=$i?>" name="invoiceno_<?=$i?>" value="<?=$rs['INVOICE_NO']?>" class="inp_sty40 mg_l3" /><br />
						<a href="javascript:deliveryInfoWrite('<?=$i?>', '<?=$rs['ORDSTATECODE_NUM']?>', '<?=$rs['NUM']?>', '<?=$rs['ORDERPART_NUM']?>');" class="btn2 mg_t10">등록</a>
					</td>					
				</tr>
			<?
					$tmpOrderNum = $rs['NUM'];
					if ($rowSpanCnt > 1) $rowSpanCnt--; //td가림 역카운트
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="12">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>	
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">선택 주문 엑셀 다운로드</a>
			<a href="" class="btn1 fl_l">전체 주문 엑셀 다운로드</a>

			<span class="tdline">선택한 주문</span>
			<a href="javascript:grpOrderChange('5100', 'Item준비중');" class="btn1 fl_r">Item준비중 처리</a>			
			<!-- PG연동이 필요한 부분 <a href="javascript:grpOrderChange('5220', '배송중');" class="btn1 fl_r">배송중 처리</a> -->
			<a href="javascript:grpOrderChange('5230', '배송완료');" class="btn1 fl_r">배송완료 처리</a>
		</div>

		<!-- paging -->
		<div class="pagination cboth"><?=$pagination?></div>
		<!--// paging -->

		<div class="help_tip">
			<dl>
				<dt>배송정보 일괄등록</dt>
				<dd>엑셀 파일로 배송정보를 일괄 등록합니다. 주문번호, 배송사, 송장번호는 필수입력사항입니다. <a href="" class="btn2">샘플다운로드</a></dd>
				<dd><a href="" class="btn2">배송정보 일괄등록</a></dd>
			</dl>
		</div>

		<div class="help_tip">
			<dl>
				<dt><img src="/images/adm/icn_q1.png" alt="느낌표" class="icn_q1" />도움말</dt>
				<dd>- 배송정보를 입력하시면 상태는 배송중 / 배송중(교환Item) 으로 자동변경됩니다.</dd>
				<dd>- 배송정보 일괄등록 시 1개의 주문에 Item이 다수일 경우에는 Item별 주문번호를 모두 동일한 것으로 기입해 주십시오,</dd>
				<dd>- 배송완료가 된 주문 건은 ‘배송완료’목록에서 확인이 가능합니다.</dd>
				<dd>- 배송시작일은 송장번호입력일과 동일합니다.</dd>
			</dl>
		</div>

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			