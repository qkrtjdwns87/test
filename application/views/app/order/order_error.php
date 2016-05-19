<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$errCode = $this->input->post_get("errcode" , FALSE);
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

	<!-- 장바구니_결제_정상결제오류 -->
	<section id="error_popup" class="sorry">
		<dl>
			<strong>결제가 정상적으로 완료되지 않았습니다</strong>
			<p class="type2">주문을 다시 하시려면 '다시 주문'을 눌러주세요.</p>
			<p class="type2">(Code : <?=$errCode?>)</p>
		</dl>

		<div class="btn_list">
			<a href="/app/order_a/orderform" class="btn_black">다시 주문</a>
		</div>
	</section>
</div>

<script src="/js/app/ui.js"></script>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		