<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$rs = $ordSet[0];
	$orderCode = $rs['ORDER_CODE'];
	$orderDate = $rs['CREATE_DATE'];
	$itemCount = $rs['TOTITEM_COUNT'];	
	
	$addUrl = (!empty($ordNum)) ? '/ordno/'.$ordNum : '';
	$addUrl .= (!empty($ordPtNum)) ? '/ordptno/'.$ordPtNum : '';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/js/jquery.browser.js"></script>	
	<script src="/js/jquery.iframe-auto-height.js"></script>		
	<script type="text/javascript">
		$(function() {
			$('#memofrm').iframeAutoHeight({minHeight: 300}); 	 
		});
	</script>
<!-- popup -->
<div id="popup">
	
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
				<td><a href="/manage/order_m/ordpayinfo<?=$addUrl?>">결제 정보</a></td>
				<td><a href="/manage/order_m/orduserinfo<?=$addUrl?>">주문자 정보</a></td>
				<td><a href="/manage/order_m/ordrecinfo<?=$addUrl?>">수령인 정보</a></td>
				<td class="on"><a href="/manage/order_m/ordinfomemo<?=$addUrl?>">관리자 메모</a></td>
				<td><a href="/manage/order_m/ordinfohistory<?=$addUrl?>">변경내역</a></td>
			</tr>
		<tbody>
	</table>
	
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /></colgroup>
		<thead>
			<tr>
				<th colspan="2">관리자 메모</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th colspan="2">
					<iframe id="memofrm" src="/manage/memo_m/list/t_no/<?=$ordNum?>/t_info/<?=$tblEnc?>" width="100%" scrolling="no" frameborder="0"></iframe>				
				</th>
			</tr>
		</tbody>
	</table>	

		
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>		