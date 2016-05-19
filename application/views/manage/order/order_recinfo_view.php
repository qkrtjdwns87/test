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
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>	
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

		function sendRecInfo(){
			if (trim($('#rec_name').val()) == ''){
				alert('수령인 이름을 입력하세요.');
				return;
			}
						
			if (trim($('#rec_mobile1').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (trim($('#rec_mobile2').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (trim($('#rec_mobile3').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (!IsNumber(trim($('#rec_mobile1').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#rec_mobile2').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#rec_mobile3').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}	

			if (trim($('#rec_zip').val()) == ''){
				alert('주소를 입력하세요.');
				return;
			}

			if (trim($('#rec_addr1').val()) == ''){
				alert('주소를 입력하세요.');
				return;
			}

			if (trim($('#rec_addr2').val()) == ''){
				alert('나머지 주소를 입력하세요.');
				return;
			}
			

			document.form.target = 'hfrm';
			document.form.action = "/manage/order_m/recinfoupdate/ordno/<?=$ordNum?>";
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
				<td><a href="/manage/order_m/orduserinfo<?=$addUrl?>">주문자 정보</a></td>
				<td class="on"><a href="/manage/order_m/ordrecinfo<?=$addUrl?>">수령인 정보</a></td>
				<td><a href="/manage/order_m/ordinfomemo<?=$addUrl?>">관리자 메모</a></td>
				<td><a href="/manage/order_m/ordinfohistory<?=$addUrl?>">변경내역</a></td>
			</tr>
		<tbody>
	</table>
		
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
		<thead>
			<tr>
				<th colspan="4">수령인 정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>수령인명</th>
				<td><input type="text" id="rec_name" name="rec_name" value="<?=$rs['RECIPIENT_NAME']?>" class="inp_sty40" /></td>
				<th>연락처</th>
				<td>
				<?
					$arrMb = explode('-', $rs['RECIPIENT_MOBILE_DEC']);
					$mbNum1 = (isset($arrMb) && count($arrMb)>0) ? $arrMb[0] : '';
					$mbNum2 = (isset($arrMb) && count($arrMb)>1) ? $arrMb[1] : '';
					$mbNum3 = (isset($arrMb) && count($arrMb)>2) ? $arrMb[2] : '';				
				?>
					<input type="text" id="rec_mobile1" name="rec_mobile1" value="<?=$mbNum1?>" class="inp_sty40" style="width:60px;" maxlength="4" />-
					<input type="text" id="rec_mobile2" name="rec_mobile2" value="<?=$mbNum2?>" class="inp_sty40" style="width:60px;" maxlength="4" />-
					<input type="text" id="rec_mobile3" name="rec_mobile3" value="<?=$mbNum3?>" class="inp_sty40" style="width:60px;" maxlength="4" />				
					<br /><span class="ex tdline">* 숫자로만 입력</span>
				</td>
			</tr>
			<tr>
				<th rowspan="3">배송지</th>
				<td colspan="3"><input type="text" id="rec_zip" name="rec_zip" value="<?=$rs['RECIPIENT_ZIP_DEC']?>" class="inp_sty10" readonly/><a href="javascript:searchAddress('rec_zip','rec_addr1','rec_addr2','rec_addr_jibun');" class="btn1">우편번호 찾기</a></td>
			</tr>
			<tr>
				<td colspan="3" class="bo_tn pd_tn"><input type="text" id="rec_addr1" name="rec_addr1" value="<?=$rs['RECIPIENT_ADDR1_DEC']?>" class="inp_sty80" readonly/></td>
			</tr>
			<tr>
				<td colspan="3" class="bo_tn pd_tn">
					<input type="text" id="rec_addr2" name="rec_addr2" value="<?=$rs['RECIPIENT_ADDR2_DEC']?>" class="inp_sty80" />
					<input type="hidden" id="rec_addr_jibun" name="rec_addr_jibun" value="<?=$rs['RECIPIENT_ADDR_JIBUN_DEC']?>" class="inp_sty80" />
				</td>
			</tr>
		</tbody>
	</table>
	</form>
	
	<div class="btn_list">
		<a href="javascript:sendRecInfo();" class="btn2">저장</a>
	</div>		

</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>			