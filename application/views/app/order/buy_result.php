<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$defaultImg = $img = '';
	$arrFile = explode('|', $orderSet['FIRST_FILE_INFO']);
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
	
	$arrItem = explode('|', $orderSet['FIRST_ITEM_INFO']);
	$itemName = (count($arrItem) > 0) ? $arrItem[1] : '';
	$itemName .= ($orderSet['TOTITEM_COUNT'] > 1) ? '<span>외 '.($orderSet['TOTITEM_COUNT'] -1).'개</span>' : '';
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
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
		<!-- 주문완료 -->
		<section id="buy_total_title">
			<div class="title_wrap">
				<span class="text complete">주문이 완료되었습니다</span>
				<span class="number">주문번호 <?=$orderSet['ORDER_CODE']?></span>
			</div>
		</section>

		<section id="buy_total_detail">
			<dl>
				<dt><?=$itemName?></dt>
				<dd class="photo"><img src="<?=$fileName?>" width="280" height="190" alt="" /></dd>
				<dd class="total_price">총 결제금액 <span><strong><?=number_format($orderSet['TOTFINAL_AMOUNT'])?></strong>원</span></dd>
			</dl>
		</section>

		<section class="buy_info_box">
			<dl class="info_style1">
				<dt>배송지 정보</dt>
				<dd>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<th>수령인</th>
							<td><?=$orderSet['ORDER_NAME']?></td>
						</tr>
						<tr>
							<th>연락처</th>
							<td><?=$orderSet['ORDER_MOBILE_DEC']?></td>
						</tr>
						<tr>
							<th>이메일</th>
							<td><?=$orderSet['ORDER_EMAIL_DEC']?></td>
						</tr>						
						<tr>
							<th>배송지</th>
							<td><?=$orderSet['RECIPIENT_ZIP_DEC']?><br /><?=$orderSet['RECIPIENT_ADDR1_DEC']?><br /><?=$orderSet['RECIPIENT_ADDR2_DEC']?><!-- <br /><?=$orderSet['RECIPIENT_ADDR_JIBUN_DEC']?> --></td>
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
							<td><?=$orderSet['PAYCODENUM_TITLE']?></td>
						</tr>
						<tr>
							<th>결제확인일시</th>
							<td><?=(!empty($orderSet['PAY_DATE'])) ? $orderSet['PAY_DATE'] : '-';?></td>
						</tr>
						<tr>
							<th>결제금액</th>
							<td><strong><?=number_format($orderSet['TOTFINAL_AMOUNT'])?>원</strong></td>
						</tr>
					</table>
				</dd>
			</dl>
		</section>
	</div>

	<!-- 메뉴바 -->
	<div class="buy_box">
		<ul class="btn2">
			<li><a href="javascript:app_showMenuWindow('주문/배송조회', '<?=$siteDomain?>/app/order_a/list');" class="emphasis">주문/배송조회</a></li>
			<li><a href="javascript:app_moveToHome();" class="emphasis">메인으로</a></li>
		</ul>
	</div>
	<!-- //메뉴바 -->

</div>
<script src="/js/app/ui.js"></script>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		