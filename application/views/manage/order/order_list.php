<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/order_m/list'.$addUrl;
	$writeNewUrl = '/manage/order_m/writeform'.$addUrl;
	$deleteUrl = '/manage/order_m/grpdelete';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script src="/js/jquery.battatech.excelexport.js"></script>	
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
	</script>
	<script type="text/javascript">
		    $(document).ready(function () {
		 
		        function itoStr($num)
		        {
		            $num < 10 ? $num = '0'+$num : $num;
		            return $num.toString();
		        }
		         
		        var btn = $('#btnExport');
		        var tbl = 'tblExport';
		        //var tbl = 'tblExport :not(checkbox)'; // 이친구를 Jquery Selector로 만져줘야됨 

		        btn.on('click', function () {
		            var dt = new Date();
		            var year =  itoStr( dt.getFullYear() );
		            var month = itoStr( dt.getMonth() + 1 );
		            var day =   itoStr( dt.getDate() );
		            var hour =  itoStr( dt.getHours() );
		            var mins =  itoStr( dt.getMinutes() );
		 
		            var postfix = year + month + day + "_" + hour + mins;
		            var fileName = "OrderList_"+ postfix + ".xls";
		 
		            var uri = $("#"+tbl).battatech_excelexport({
		                containerid: tbl
		                , datatype: 'table'
		                , returnUri: true
		            });
		 
		            $(this).attr('download', fileName).attr('href', uri).attr('target', '_blank');
		        });
		    });


	    </script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 주문현황]</h2>
			<div class="location">Home &gt; 주문관리 &gt; 전체 주문현황</div>
		</div>
		
		<?
			$col1 = 0;
			$col2 = 0;
			$col3 = 0;
			$col4 = 0;
			$col5 = 0;
			$col6 = 0;			
			$col7 = 0;
			$col8 = 0;
			$col9 = 0;
			$col10 = 0;
			$col11 = 0;
			$col12 = 0;
			$col13 = 0;	
			
			if (isset($ordStatsToDaySet))
			{
				$col1 = $ordStatsToDaySet['STATE_5040'];
				$col2 = $ordStatsToDaySet['STATE_5060'];
				$col3 = $ordStatsToDaySet['STATE_5070'];
				$col4 = $ordStatsToDaySet['STATE_5080'];
				$col5 = $ordStatsToDaySet['STATE_5110'];
				$col6 = $ordStatsToDaySet['STATE_5130'];
				$col7 = $ordStatsToDaySet['STATE_5150'];
				$col8 = $ordStatsToDaySet['STATE_5140'];
				$col9 = $ordStatsToDaySet['STATE_5160'];
				$col10 = $ordStatsToDaySet['STATE_5170'];
				$col11 = $ordStatsToDaySet['STATE_5190'];
				$col12 = $ordStatsToDaySet['STATE_5200'];
				$col13 = $ordStatsToDaySet['STATE_5220'];				
			}
			
			//일자를 무시한 통계인 경우
			/*
			if (isset($ordStatsToDaySet[0]['STATE_5040']))
			{
				foreach ($ordStatsToDaySet as $srs):
				$col1 = $srs['STATE_5040'];
				endforeach;
					
				foreach ($ordStatsSet as $srs):
				$col2 = $srs['STATE_5060'];
				$col3 = $srs['STATE_5070'];
				$col4 = $srs['STATE_5080'];
				$col5 = $srs['STATE_5110'];
				$col6 = $srs['STATE_5130'];
				$col7 = $srs['STATE_5150'];
				$col8 = $srs['STATE_5140'];
				$col9 = $srs['STATE_5160'];
				$col10 = $srs['STATE_5170'];
				$col11 = $srs['STATE_5190'];
				$col12 = $srs['STATE_5200'];
				$col13 = $srs['STATE_5220'];
				endforeach;
			}
			*/			
		?>		
		<table class="write2">
			<thead>
				<tr>
					<th>신규주문</th>
					<th>입금대기</th>
					<th>입금확인</th>
					<th>결제완료</th>
					<th>취소신청</th>
					<th>환불신청</th>
					<th>환불승인</th>
					<th>환불보류</th>
					<th>반품신청</th>
					<th>반품보류</th>
					<th>교환요청</th>
					<th>교환보류</th>
					<th>배송중</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#"><span class="blue bold"><?=number_format($col1)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col2)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col3)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col4)?></span>건 </a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col5)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col6)?></span>건 </a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col7)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col8)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col9)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col10)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col11)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col12)?></span>건</a></td>
					<td><a href="#"><span class="blue bold"><?=number_format($col13)?></span>건</a></td>
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
					<th rowspan="3">주문상태 <br /> <br /> <label><input type="checkbox" id="orderstatecheck" name="orderstatecheck" value="all" onclick="javascript:AllCheckBoxCheck('orderstate',this.id);" class="inp_check" />전체선택</label></th>
					<td colspan="3"><strong>결제</strong>
					<?
						$i = 2;
						foreach ($ordStCdSet as $crs):
							if ($crs['NUM'] >= 5060 && $crs['NUM'] <= 5090)
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
					<td colspan="3" class="bo_tn">
						<strong>배송</strong>
					<?
						//$i = 2;
						foreach ($ordStCdSet as $crs):
							if ($crs['NUM'] == 5100 || $crs['NUM'] >= 5220)
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
					<td colspan="3" class="bo_tn">
						<strong>취소/환불/교환</strong>
					<?
						//$i = 2;
						foreach ($ordStCdSet as $crs):
							if ($crs['NUM'] >= 5110 && $crs['NUM'] < 5220)
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
				<tr>
					<th>결제수단 <label><input type="checkbox" id="paytypeCheck" name="paytypeCheck" value="all" onclick="javascript:AllCheckBoxCheck('paytype',this.id);" class="inp_check" />전체선택</label></th>
					<td colspan="3">
					<?
						$i = 2;
						foreach ($payTypeCdSet as $crs):
							if ($crs['NUM'] > 5300)
							{
								$sel_chk = (strpos($grpPayType, $crs['NUM']) !== FALSE) ? 'checked="checked"' : '';								
					?>						
						<label><input type="checkbox" id="paytype<?=$i?>" name="paytype" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_check" /><?=$crs['TITLE']?></label>
					<?
								$i++;					
							}
						endforeach;					
					?>							
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
			<colgroup><col width="5%" /></colgroup>
			<thead>
				<tr>
					<th rowspan="2"><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th rowspan="2">주문번호</th>
					<th rowspan="2">주문일시</th>
					<th rowspan="2">주문Item</th>
					<th rowspan="2">Craft Shop</th>
					<th rowspan="2">주문자<br />(연락처)</th>
					<th rowspan="2">구매금액</th>
					<th rowspan="2">배송비</th>
					<th rowspan="2">총결제금액</th>
					<th rowspan="2">결제</th>
					<th colspan="5">주문상태</th>
				</tr>
				<tr>
					<th>상태</th>
					<th>배송상태</th>
					<th>구매<br />취소</th>
					<th>환불/<br />반품</th>
					<th>교환</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = $maxOrder = $rowSpanCnt = 1;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	$tmpOrderNum = $tmpOrderPart = 0;
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
					$deliveryTitle = (!empty($rs['DELIVERYCODE_TITLE']) && $rs['DELIVERYCODE_NUM'] != 10000) ? $rs['DELIVERYCODE_TITLE'].'<br />('.substr($rs['DELIVERY_DATE'], 0, 10).')' : '배송전'; 
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
				<?}?>					
					<td width="6%"><?=number_format($rs['PART_AMOUNT'])?></td>
					<td width="5%"><?=number_format($rs['DELIVERY_PRICE'])?></td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="7%" <?=$rowSpan?>><?=number_format($rs['TOTFINAL_AMOUNT'])?></td>
					<td width="5%" <?=$rowSpan?>><?=$rs['PAYCODE_TITLE']?></td>
				<?}?>					
					<td width="6%">
						<?
							if ($rs['ORDSTATECODE_NUM'] > 5050 && $rs['CHECK_YN'] == 'Y')
							{
								echo $rs['ORDSTATECODE_TITLE'].'<br /><span class="red">[주문확인함]</span>';
							}
							else 
							{
								echo ($rs['ORDSTATECODE_NUM'] == 5050) ? '<span class="red">'.$rs['ORDSTATECODE_TITLE'].'</span>' : $rs['ORDSTATECODE_TITLE'];
							}
						?>						
					</td>
					<td width="7%"><?=$deliveryTitle?></td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="4%" <?=$rowSpan?>><?=number_format($rs['TOTCANCEL_COUNT'])?></td>
					<td width="4%" <?=$rowSpan?>><?=number_format($rs['TOTREFUND_COUNT'] + $rs['TOTRETURN_COUNT'])?></td>
					<td width="4%" <?=$rowSpan?>><?=number_format($rs['TOTEXCHANGE_COUNT'])?></td>
				<?}?>					
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
					<td colspan="15">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>	
			</tbody>
		</table>

		<table  style="display: none" class="write3" id="tblExport">
		<!-- <table class="write3" id="tblExport"> -->
			<colgroup><col width="5%" /></colgroup>
			<thead>
				<tr>
					<!-- <th rowspan="2"><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th> -->
					<th rowspan="2">주문번호</th>
					<th rowspan="2">주문일시</th>
					<th rowspan="2">주문Item</th>
					<th rowspan="2">Craft Shop</th>
					<th rowspan="2">주문자<br />(연락처)</th>
					<th rowspan="2">구매금액</th>
					<th rowspan="2">배송비</th>
					<th rowspan="2">총결제금액</th>
					<th rowspan="2">결제</th>
					<th colspan="5">주문상태</th>
				</tr>
				<tr>
					<th>상태</th>
					<th>배송상태</th>
					<th>구매<br />취소</th>
					<th>환불/<br />반품</th>
					<th>교환</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = $maxOrder = $rowSpanCnt = 1;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	$tmpOrderNum = $tmpOrderPart = 0;
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
					$deliveryTitle = (!empty($rs['DELIVERYCODE_TITLE']) && $rs['DELIVERYCODE_NUM'] != 10000) ? $rs['DELIVERYCODE_TITLE'].'<br />('.substr($rs['DELIVERY_DATE'], 0, 10).')' : '배송전'; 
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
				<!-- <tr>
					<td width="3%"><input type="checkbox" id="chkCheck<?=$rs['ORDERPART_NUM']?>" name="chkCheck" value="<?=$rs['ORDERPART_NUM']?>" class="inp_check"/></td>	 -->			
				<?if ($maxOrder == $rowSpanCnt){?>				
					<td width="6%" <?=$rowSpan?>><?=$rs['ORDER_CODE']?></a></td>
					<td width="6%" <?=$rowSpan?>><?=$rs['CREATE_DATE']?></td>
				<?}?>					
					<td><?=$itemTitle?></td>
					<td width="8%"><?=$rs['SHOP_NAME']?><br />(<?=$rs['SHOP_CODE']?>)</td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="8%" <?=$rowSpan?>><?=$rs['ORDER_NAME']?><br />(<?=$rs['ORDER_MOBILE_DEC']?>)</td>
				<?}?>					
					<td width="6%"><?=number_format($rs['PART_AMOUNT'])?></td>
					<td width="5%"><?=number_format($rs['DELIVERY_PRICE'])?></td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="7%" <?=$rowSpan?>><?=number_format($rs['TOTFINAL_AMOUNT'])?></td>
					<td width="5%" <?=$rowSpan?>><?=$rs['PAYCODE_TITLE']?></td>
				<?}?>					
					<td width="6%">
						<?
							if ($rs['ORDSTATECODE_NUM'] > 5050 && $rs['CHECK_YN'] == 'Y')
							{
								echo $rs['ORDSTATECODE_TITLE'].'<br /><span class="red">[주문확인함]</span>';
							}
							else 
							{
								echo ($rs['ORDSTATECODE_NUM'] == 5050) ? '<span class="red">'.$rs['ORDSTATECODE_TITLE'].'</span>' : $rs['ORDSTATECODE_TITLE'];
							}
						?>						
					</td>
					<td width="7%"><?=$deliveryTitle?></td>
				<?if ($maxOrder == $rowSpanCnt){?>					
					<td width="4%" <?=$rowSpan?>><?=number_format($rs['TOTCANCEL_COUNT'])?></td>
					<td width="4%" <?=$rowSpan?>><?=number_format($rs['TOTREFUND_COUNT'] + $rs['TOTRETURN_COUNT'])?></td>
					<td width="4%" <?=$rowSpan?>><?=number_format($rs['TOTEXCHANGE_COUNT'])?></td>
				<?}?>					
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
					<td colspan="15">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>	
			</tbody>
		</table>

		<div class="btn_list">
			<a id='btnExport' href="#" class="btn1 fl_l" download="">엑셀다운로드</a>
			<!-- <a href="" class="btn1 fl_l">선택 주문 엑셀 다운로드</a> -->
			<!-- <a href="" class="btn1 fl_l">전체 주문 엑셀 다운로드</a> -->
			
			<span class="tdline">선택한 주문</span>
			<a href="javascript:grpOrderChange('5050', '주문확인');" class="btn1 fl_r">주문확인 처리</a>
		</div>

		<!-- paging -->
		<div class="pagination cboth"><?=$pagination?></div>
		<!--// paging -->


	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		