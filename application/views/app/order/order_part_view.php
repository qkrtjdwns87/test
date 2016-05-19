<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$isDelivery = FALSE; //배송중 여부
	$refContent = '';
	$totPartCnt = $totItemCnt = 0;
	if ($ordSet)
	{
		$orderDate = substr($ordSet[0]['CREATE_DATE'], 0, 10);
		$orderCode = $ordSet[0]['ORDER_CODE'];
		$ordState = $ordSet[0]['ORDSTATECODE_NUM'];
		$partAmount = $ordSet[0]['PART_AMOUNT'];
		$totPartCnt = $ordSet[0]['TOTPART_COUNT']; //주문에 속한 샵갯수
		$totItemCnt = $ordSet[0]['TOTITEM_COUNT']; //전체 아이템 갯수
		
		$totPayAmount = $ordSet[0]['TOTFINAL_AMOUNT'];
		$deliveryPrice = $ordSet[0]['DELIVERY_PRICE'];
		$ordPartContent = $ordSet[0]['ORDERPART_CONTENT'];
		$payment = $ordSet[0]['PAYCODE_TITLE'];
		$payDate = $ordSet[0]['PAY_DATE'];
		$shopNum = $ordSet[0]['SHOP_NUM'];
		$shopName = $ordSet[0]['SHOP_NAME'];
		
		$img = $defaultImg = '';
		$arrFile = (!empty($rs['PROFILE_FILE_INFO'])) ? explode('|', $rs['PROFILE_FILE_INFO']) : array();
		if (count($arrFile) > 0)
		{
			$img = ($arrFile[4] == 'Y') ? str_replace('.', '_s.', $arrFile[2].$arrFile[3]) : $arrFile[2].$arrFile[3];	//썸네일생성 여부
		}
		$shopfileName = (!empty($img)) ? $img : $defaultImg;		
		
		$partTotAmount = $partAmount + $deliveryPrice;
		
		//배송지 정보
		$recName = $ordSet[0]['RECIPIENT_NAME'];
		$recMobile = $ordSet[0]['RECIPIENT_MOBILE_DEC'];
		$recZip = $ordSet[0]['RECIPIENT_ZIP_DEC'];
		$recAddr1 = $ordSet[0]['RECIPIENT_ADDR1_DEC'];
		$recAddr2 = $ordSet[0]['RECIPIENT_ADDR2_DEC'];
		$recAddrJibun = $ordSet[0]['RECIPIENT_ADDR_JIBUN_DEC'];
		
		$invoiceNo = $ordSet[0]['INVOICE_NO'];
		if (!empty($invoiceNo)) $isDelivery = TRUE;
		
		//샵 교환 및 환불정책
		//샵이 여러개인 경우 무조건 mall 정책으로
		$refPolCodeNum = ($totPartCnt > 1) ? '' : $ordSet[0]['REFPOLICYCODE_NUM'];
		if (empty($refPolCodeNum)) $refPolCodeNum = '12040'; //Mall 정책 사용
		if ($refPolCodeNum == '12020' && $totItemCnt == 1)
		{
			//아이템 개별
			 $itemPolicySet = $this->common->getItemPolicyRowData($ordSet[0]['SHOPITEM_NUM']);
			 if ($itemPolicySet)
			 {
			 	$refContent = $itemPolicySet['REFPOLICY_CONTENT'];
			 }
		}
		else if ($refPolCodeNum == '12030' && $totItemCnt == 1)
		{
			//shop 정책 사용
			$shopPolicySet = $this->common->getShopPolicyRowData($ordSet[0]['SHOP_NUM']);
			if ($shopPolicySet)
			{
				$refContent = $shopPolicySet['REFPOLICY_CONTENT'];
			}
		}
		else
		{
			// if ($refPolCodeNum == '12040')
			//circus 정책 사용
			$stdShopPolicySet = $this->common->getStandardShopPolicyRowData(); //기준샵(MALL)
			if ($stdShopPolicySet)
			{
				$refContent = $stdShopPolicySet['REFPOLICY_CONTENT'];
				echo $refContent;
			}			
		}
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
	<link rel="stylesheet" type="text/css" href="/css/app/popup.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="/js/app/tab.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
			
	    });	
	</script>
</head>
<body>
<div id="wrap">
	<div id="buy_container">
		<!-- 주문/배송조회 목록 -->
		<section id="buy_total_title"> 
			<div class="title_wrap">
				<span class="day"><?=$orderDate?></span>
				<span class="number">주문번호 <?=$orderCode?></span>
			</div>
		</section>

		<section class="order_total_detail">
		<?
			$i = 1;
			$tmpOrderPart = 0;
			foreach ($ordSet as $rs):
				if ($tmpOrderPart != $rs['PART_ORDER'])
				{
					$img = $defaultImg = '';
					$arrFile = (!empty($rs['PROFILE_FILE_INFO'])) ? explode('|', $rs['PROFILE_FILE_INFO']) : array();
					if (count($arrFile) > 0)
					{
						$img = ($arrFile[4] == 'Y') ? str_replace('.', '_s.', $arrFile[2].$arrFile[3]) : $arrFile[2].$arrFile[3];	//썸네일생성 여부
					}
					$shopfileName = (!empty($img)) ? $img : $defaultImg;
					$shopName = $rs['SHOP_NAME'];
		?>
			<dl>
				<dt><a href="javascript:app_showCraftShopPage('<?=$rs['SHOP_NUM']?>');"><img src="<?=$shopfileName?>" alt="" /></a></dt>
				<dd><?=$shopName?></dd>
				<dd class="btn"><a href="javascript:app_showMessageRoom('<?=$shopName?>', '<?=$rs['SHOP_NUM']?>', '<?=$rs['ORDERS_NUM']?>');">메시지</a></dd>
			</dl>
		<?
				}
				$tmpOrderPart = $rs['PART_ORDER'];
				$i++;
			endforeach;
		?>
		</section>
		
		<section id="order_tab">
			<div class="cate">
				<ul>
					<li class="list"><a class="on" href="#">구매정보</a></li>
					<li class="list"><a href="#">배송정보</a></li>
					<li class="list"><a href="#">교환 및 환불정책</a></li>
				</ul>
			</div>
			
			<!-- 구매정보 tab -->
			<div class="content">
				<div class="order_detail_info">
					<p class="title">주문정보</p>
					<ul>
				    <?
				    	$i = 1;
				    	$defaultImg = '/images/adm/@thumb.gif';
				    	$tmpOrderPart = 0;
				    	foreach ($ordSet as $rs):
							$img = '';
							$arrFile = (!empty($rs['FIRST_FILE_INFO'])) ? explode('|', $rs['FIRST_FILE_INFO']) : array();	    	
							if (count($arrFile) > 0)
							{
								$img = ($arrFile[4] == 'Y') ? str_replace('.', '_s.', $arrFile[2].$arrFile[3]) : $arrFile[2].$arrFile[3];	//썸네일생성 여부
							}
							$fileName = (!empty($img)) ? $img : $defaultImg;				
							
							$itemTitle = $rs['ITEM_NAME'];
							$itemNum = $rs['SHOPITEM_NUM'];
							$itemUrl = '/app/item_a/view/sno/'.$shopNum.'/sino/'.$itemNum;
							$arrOpt = (!empty($rs['ITEMOPTION_INFO'])) ? explode('-', $rs['ITEMOPTION_INFO']) : array();
							$amount = $rs['AMOUNT'];
							$quantity = $rs['QUANTITY'];
							
							$cancelForbidSiNum = ($rs['PAYAFTER_CANCEL_YN'] == 'N') ? $itemNum.'|'.$itemTitle.',' : '';
							$refundForbidSiNum = ($rs['MADEAFTER_REFUND_YN'] == 'N') ? $itemNum.'|'.$itemTitle.',' : '';
							$changeForbidSiNum = ($rs['MADEAFTER_CHANGE_YN'] == 'N') ? $itemNum.'|'.$itemTitle.',' : '';
					?>						
						<li>
							<a href="javascript:app_moveToItemDetail('<?=$shopNum?>', '<?=$itemNum?>');" class="btn_order_go">
								<dl>
									<dt><img src="<?=$fileName?>" width="280" height="190" alt="" /></dt>
									<dd class="name"><?=$itemTitle?></dd>
								<?
									foreach ($arrOpt as $ot) //옵션선택사항
									{
										$arrOptInfo = explode('|', $ot);
								?>
									<dd><?=$arrOptInfo[0]?>: <?=$arrOptInfo[2]?></dd>
								<?
									}
								?>										
									<dd>수량: <?=number_format($quantity)?></dd>
									<dd class="total_price">구매가격: <?=number_format($amount)?>원</dd>
								</dl>
							</a>
						</li>
					<?
							$tmpOrderPart = $rs['PART_ORDER'];
							$i++;
						endforeach;	
						
						$cancelForbidSiNum = (strlen($cancelForbidSiNum) > 0) ? substr($cancelForbidSiNum, 0, -1) : '';
						$refundForbidSiNum = (strlen($refundForbidSiNum) > 0) ? substr($refundForbidSiNum, 0, -1) : '';
						$changeForbidSiNum = (strlen($changeForbidSiNum) > 0) ? substr($changeForbidSiNum, 0, -1) : '';
						
						//echo '<br />cancelForbidSiNum='.$cancelForbidSiNum;
						//echo '<br />refundForbidSiNum='.$refundForbidSiNum;
						//echo '<br />changeForbidSiNum='.$changeForbidSiNum;
					?>							
				</div>

				<dl class="info_style2">
					<dt class="total_price">총 구매가격</dt>
					<dd class="total_price"><?=number_format($partTotAmount)?>원</dd>
					<dt class="end">배송비</dt>
					<dd class="end"><?=number_format($deliveryPrice)?>원</dd>
				</dl>

				<dl class="info_style1">
					<dt>주문요청사항</dt>
					<dd>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>내용</th>
								<td><?=$ordPartContent?></td>
							</tr>
						</table>
					</dd>
				</dl>

				<dl class="info_style1">
					<dt>배송지 정보</dt>
					<dd>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>수령인</th>
								<td><?=$recName?></td>
							</tr>
							<tr>
								<th>연락처</th>
								<td><?=$recMobile?></td>
							</tr>
							<tr>
								<th>배송지</th>
								<td><?=$recZip?><br /><?=$recAddr1?><br /><?=$recAddr2?></td>
							</tr>
						</table>
					</dd>
				</dl>

				<dl class="info_style1">
					<dt>결제 정보</dt>
					<dd>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<th>결제방법</th>
								<td><?=$payment?></td>
							</tr>
							<tr>
								<th>결제확인일시</th>
								<td><?=$payDate?></td>
							</tr>
							<tr class="total_price">
								<th>결제금액</th>
								<td><strong><?=number_format($totPayAmount)?>원</strong></td>
							</tr>
						</table>
					</dd>
				</dl>

			</div>
			<!-- //구매정보 tab -->

			<!-- 배송정보 tab -->
			<div class="content">
			<?
				if ($isDelivery)
				{
					$deliveryTitle = $ordSet[0]['DELIVERYCODE_TITLE'];
					$deliveryDate = $ordSet[0]['DELIVERY_DATE'];
					
			?>
				<!-- 내용있을 경우 -->
				<dl class="info_style4">
					<dt>발송일</dt>
					<dd><?=$deliveryDate?></dd>
					<dt>택배사</dt>
					<dd><?=$deliveryTitle?></dd>
					<dt>운송장번호</dt>
					<dd><?=$invoiceNo?></dd>
				</dl>
				<div class="process">
					<ul class="state">
						<li <?if (in_array($ordState, array(5050, 5080, 5100))){?>class="on"<?}?>><span>준비중</span></li>
						<li <?if (in_array($ordState, array(5220))){?>class="on"<?}?>><span>배송중</span></li>
						<li <?if (in_array($ordState, array(5230))){?>class="on"<?}?>><span>배송완료</span></li>
					</ul>
					<!-- api 연동후 
					<ol class="state_list">
						<li>2016 - 02 - 01 12:15:24 <strong>[강남A] 집화처리</strong></li>
						<li>2016 - 02 - 01 14:25:12 <strong><span class="thumb"><img src="/images/app/@thumb/@thumb1.png" alt=""></span>Lovely Shop 발송처리</strong></li>
						<li>2016 - 02 - 01 12:15:24 <strong>[강남A] 집화처리</strong></li>
						<li>2016 - 02 - 01 12:15:24 <strong>[강남A] 집화처리</strong></li>
						<li>2016 - 02 - 01 12:15:24 <strong>[강남A] 집화처리</strong></li>
					</ol>
					 -->
				</div>
				<!-- //내용있을 경우 -->
			<?
				}
				else 
				{
			?>
				<!-- 내용없을 경우 -->
				<dl class="info_style4">
					<dt>발송일</dt>
					<dd>-</dd>
					<dt>택배사</dt>
					<dd>-</dd>
					<dt>운송장번호</dt>
					<dd></dd>
				</dl>
				<!--
				<div class="process">
					<ul class="state">
						<li class="on"><span>준비중</span></li>
						<li><span>배송중</span></li>
						<li><span>배송완료</span></li>
					</ul>
				</div>				
				<div class="shipping_soon">
					조금만 기다려 주세요. <br>곧 배송출발합니다!!
				</div>
			-->
				<!-- //내용없을 경우 -->
			<?
				}
			?>
			</div>
			<!-- //배송정보 tab -->

			<!-- 교환 및 환불정책 tab -->
			<div class="content">
			 <ul class="refund_rule">
			 	<li><?=nl2br($refContent)?></li>
			 	<!-- 
			 	<li>- 제작 기간 2주 소요 (주말, 공휴일 제외)</li>
			 	<li>- 핸드메이드 특성상 배송 시 파손 될 우려가 있어 단순 변심, 본인실수로 인한 교환 및 환불은 불가합니다.</li>
			 	<li>- 형태나 색상 등을 정확히 확인 후 주문 바랍니다.  핸드메이드 특성상 크기나 그림이 조금씩 다른 점을 유의하세요. 이는 교환 및 환불 사유가 될 수 없습니다.</li>
			 	<li>- 물품반송주소 : 서울시 금천구 독산로 28길 6 1층</li>
			 	 -->			 	
			 </ul>
			</div>
			<!-- //교환 및 환불정책 tab -->
		</div>
		<script>
			var tab = new Tab({
				wrap: $('#order_tab'), //container
				index:0, //start content index default:0
				transition:'none' //effect default:'none'( 'slide', 'fade' )
			});

			function ordRequest(type, ordno, ordptno){
				//if (confirm('신청하시겠습니까?')){
					var url = '/app/order_a/'+type+'reqform/ordno/'+ordno+'/ordptno/'+ordptno;
					location.href = url;
				//}
			}
		</script>
	</section>

	<?
		$btnCnt = 0;
		$cancelUrl = $refundUrl = $changeUrl = '';
		$isCancelDisp = FALSE;
		if ($ordState < 5090) //item준비단계, 결제취소완료 이전인 경우만
		{
			$btnCnt++;
			$isCancelDisp = TRUE;
			if (!empty($cancelForbidSiNum))
			{
				$cancelUrl = "onclick=\"$('#layer_tip_bottomleft').show();\"";
			}
			else
			{
				$cancelUrl = "onclick=\"ordRequest('cancel', '".$ordNum."', '".$ordPtNum."');\"";
			}
		}
		
		$isRefundDisp = FALSE;
		if (in_array($ordState, array(5220, 5230)))
		{
			$btnCnt++;
			$isRefundDisp = TRUE;
			if (!empty($refundForbidSiNum))
			{
				$refundUrl = "onclick=\"$('#layer_tip_bottomleft').show();\"";
			}
			else
			{
				$refundUrl = "onclick=\"ordRequest('refund', '".$ordNum."', '".$ordPtNum."');\"";
			}			
		}
		
		$isChangeDisp = FALSE;
		if (in_array($ordState, array(5230)))
		{
			$btnCnt++;
			$isChangeDisp = TRUE;
			if (!empty($changeForbidSiNum))
			{
				$changeUrl = "onclick=\"$('#layer_tip_bottomleft').show();\"";
			}
			else
			{
				$changeUrl = "onclick=\"ordRequest('exchange', '".$ordNum."', '".$ordPtNum."');\"";
			}		
		}
		
		$isReviewDisp = FALSE;
		if (in_array($ordState, array(5230, 5380))) //배송완료, 주문완료
		{
			$btnCnt++;
			$isReviewDisp = TRUE;
		}
		$isReviewDisp = TRUE; //테스트를 위해서 무조건 보이게끔
		if ($btnCnt > 3) $btnCnt = 3;
	?>
	<!-- 메뉴바 -->
	<div class="buy_box">
		<!-- 버튼 1개 일 경우 -->
		<!-- <div class="btn1">
			<a href="" class="emphasis">구매후기 남기기</a>
		</div> -->
		<!-- //버튼 1개 일 경우 -->
		<!-- 버튼 2개 일 경우 -->
		<ul class="btn<?=$btnCnt?>">
		<?if ($isCancelDisp){?>
			<li><a href="#" <?=$cancelUrl?> class="emphasis">구매취소 신청</a></li>		
		<?}?>
		
		<?if ($isRefundDisp){?>
			<li><a href="#" <?=$refundUrl?> class="emphasis">환불 신청</a></li>		
		<?}?>		
		
		<?if ($isChangeDisp){?>
			<li><a href="#" <?=$changeUrl?> class="emphasis">교환 신청</a></li>		
		<?}?>		
			
		<?if ($isReviewDisp){?>
			<li><a href="javascript:app_moveToPurchaseReview('<?=$ordNum?>', '<?=$ordPtNum?>');" class="normal">구매후기 남기기</a></li>
		<?}?>
		</ul>
		<!-- //버튼 2개 일 경우 -->
		<!-- 버튼 3개 일 경우 -->
		<!-- [D] dim일 경우 class="dim" 추가 -->
		<!-- <ul class="btn3">
			<li><a href="" class="normal">환불신청<span><a href="#"></a></span></a></li>
			<li><a href="" class="normal dim">교환불가<span><a href="#"></a></span></a></a></li>
			<li><a href="" class="emphasis">구매후기 남기기</a></li>
		</ul> -->
		<!-- //버튼 3개 일 경우 -->
	</div>
	<!-- //메뉴바 -->
</div>

<!-- 한줄남기기 tip -->
<div id="layer_tip_bottomleft" onclick="$('#layer_tip_bottomleft').hide();">
	<span class="icn"></span>
	<div class="popup_box">
		해당 Item은 주문제작 Item 또는 재료의 특성상
		요청하신 내용으로 처리가 어렵습니다.
		양해 부탁드립니다. 
		보다 상세한 문의는 메시지 주세요.  
	</div>
</div>
<!-- //한줄남기기 tip -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		