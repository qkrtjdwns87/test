<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	
	</script>
</head>
<body>
<div id="wrap">
	<div id="buy_container">
		<!-- 주문/배송조회 목록 -->
    <?
    	$i = 1;
    	$defaultImg = '/images/adm/@thumb.gif';
    	$tmpOrderNum = $tmpOrderPart = 0;
    	foreach ($recordSet as $rs):
			$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
			
			$orderNum = $rs['NUM'];
			$orderCode = $rs['ORDER_CODE'];
			$orderDate = subStr($rs['CREATE_DATE'], 0, 10);
			$url = '/app/order_a/partview/ordno/'.$orderNum.'/ordptno/'.$rs['ORDERPART_NUM'];
			$itemNum = 0;
			$itemTitle = '';		
			
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
			$itemTitle = ($rs['PARTITEM_COUNT'] > 1) ? $itemTitle.' <span>외'.($rs['PARTITEM_COUNT'] -1).'개</span>' : $itemTitle;
			$partTotAmount = $rs['PART_AMOUNT'] + $rs['DELIVERY_PRICE'];
	?>
	<?if ($tmpOrderNum != $orderNum){?>		
		<!-- 주문내역 있을 경우 -->
		<section id="buy_total_title">
			<div class="title_wrap type2">
				<span class="day"><?=$orderDate?></span>
				<span class="number">주문번호 <?=$orderCode?></span>
			</div>
		</section>
	<?}?>
	
	<?if ($tmpOrderNum != $orderNum){?>
		<section class="order_list">
			<ul>
	<?}?>			
				<li>
					<a href="<?=$url?>" class="btn_order_go">
						<div class="order_total_title">
							<span class="name"><?=$rs['SHOP_NAME']?></span>
							<span class="btn_order"><?=$rs['ORDSTATECODE_TITLE']?></span>
						</div>
						<dl>
							<dt class="photo"><img src="<?=$fileName?>" width="280" height="190" alt="" /></dt>
							<dd><?=$itemTitle?></dd>
							<dd class="total_price"><strong><?=number_format($partTotAmount)?><span>원</span></strong></dd>
						</dl>
					</a>
				</li>
	<?if ($tmpOrderNum == $orderNum){?>				
			</ul>
		</section>
	<?}?>		
	<?
			$tmpOrderNum = $orderNum;
			$i++;
		endforeach;
		
	?>		
	</div>
	<?
		if ($rsTotalCount == 0)
		{
	?>
		<!-- 주문/배송조회_목록 없음 -->
		<section id="error_popup">
			<strong>주문내역이 없습니다</strong>
			<p>마음은 팔 수도 살 수도 없는 것이지만 줄 수 있는 보물입니다. <br>
			CIRCUS는 작가의 마음도 함께 전합니다.</p>
			<span>-  Given by CIRCUS Master</span>
		</section>
	<?
		}
	?>		
</div>
<script src="/app/js/ui.js"></script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		