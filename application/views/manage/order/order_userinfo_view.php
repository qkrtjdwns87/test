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
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
		$(function() {
			$( "#paydate, #refund_date" ).datepicker({
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

			$("#paydateImg").click(function() { 
				$("#paydate").datepicker("show");
			});
			$("#refund_dateImg").click(function() { 
				$("#refund_date").datepicker("show");
			});			
		});

		function sendUserInfo(){
			if (trim($('#order_email').val()) != ''){
				if (!IsEmail($('#order_email').val())){
					alert('올바른 이메일 주소를 입력하세요.');
					return;
				}
			}

			document.form.target = 'hfrm';
			document.form.action = "/manage/order_m/userinfoupdate/ordno/<?=$ordNum?>";
			document.form.submit();			
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
					<a href="" class="btn1 fl_r">주문서 인쇄</a><!-- <a href="" class="btn1 fl_r">거래증빙 인쇄</a> -->
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="tab1">
				<td><a href="/manage/order_m/ordinfo<?=$addUrl?>">주문 정보</a></td>
				<td><a href="/manage/order_m/ordpayinfo<?=$addUrl?>">결제 정보</a></td>
				<td class="on"><a href="/manage/order_m/orduserinfo<?=$addUrl?>">주문자 정보</a></td>
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
				<th colspan="4">주문자 정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>주문자명</th>
				<td><?=$rs['ORDER_NAME']?></td>
				<th>휴대폰</th>
				<td>
				<?
					$arrMb = explode('-', $rs['ORDER_MOBILE_DEC']);
					$mbNum1 = (isset($arrMb) && count($arrMb)>0) ? $arrMb[0] : '';
					$mbNum2 = (isset($arrMb) && count($arrMb)>1) ? $arrMb[1] : '';
					$mbNum3 = (isset($arrMb) && count($arrMb)>2) ? $arrMb[2] : '';				
				?>
					<input type="text" id="order_mobile1" name="order_mobile1" value="<?=$mbNum1?>" class="inp_sty40" style="width:60px;" maxlength="4" />-
					<input type="text" id="order_mobile2" name="order_mobile2" value="<?=$mbNum2?>" class="inp_sty40" style="width:60px;" maxlength="4" />-
					<input type="text" id="order_mobile3" name="order_mobile3" value="<?=$mbNum3?>" class="inp_sty40" style="width:60px;" maxlength="4" />
					<!-- <br /><span class="ex tdline">* 숫자로만 입력</span> -->
				</td>
			</tr>
			<tr>
				<th>이메일</th>
				<td colspan="3"><input type="text" id="order_email" name="order_email" value="<?=$rs['ORDER_EMAIL_DEC']?>" class="inp_sty40" /></td>
			</tr>
			<?
				$tmpShopNum = 0;
				foreach ($ordSet as $crs):
					if ($tmpShopNum != $crs['SHOP_NUM'])
					{
			?>			
			<tr>
				<th><?=$crs['SHOP_NAME']?><br />주문 요청사항</th>
				<td colspan="3">
					 <textarea id="order_content" name="order_content" rows="5" cols="5" class="textarea1"><?=$crs['ORDERPART_CONTENT']?></textarea>
				</td>
			</tr>
			<?
					}
					$tmpShopNum = $crs['SHOP_NUM'];
				endforeach;
			?>
		</tbody>
	</table>
	</form>
	
	<div class="btn_list">
		<a href="javascript:sendUserInfo();" class="btn2">저장</a>
	</div>		
	
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>			