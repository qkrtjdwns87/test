<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CIRCUS ADMIN</title>
<link rel="stylesheet" type="text/css" href="/css/adm/common.css" />
<link rel="stylesheet" type="text/css" href="/css/adm/style.css" />
</head>
<body>

<!-- popup -->
<div id="popup">
	
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
		<tbody>
			<tr>
				<th>주문번호</th>
				<td>201512312</td>
				<th>주문일시</th>
				<td>2016-02-05 22:11</td>
			</tr>
		</tbody>
	</table>
	
	<table class="write2 mg_t10">
		<thead>
			<tr>
				<th  colspan="6">주문상세정보
					<a href="" class="btn1 fl_r">주문서 인쇄</a><a href="" class="btn1 fl_r">거래증빙 인쇄</a>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr class="tab1">
				<td><a href="">주문 정보</a></td>
				<td class="on"><a href="">결제 정보</a></td>
				<td><a href="">주문자 정보</a></td>
				<td><a href="">수령인 정보</a></td>
				<td><a href="">관리자 메모</a></td>
				<td><a href="">변경내역</a></td>
			</tr>
		<tbody>
	</table>
		
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
		<thead>
			<tr>
				<th colspan="4">결제정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>총 구매금액</th>
				<td>52,000원</td>
				<th>배송비</th>
				<td>2,500원</td>
			</tr>
			<tr>
				<th>총 실결제금액</th>
				<td colspan="3"><span class="red">54,500</span>원</td>
			</tr>
		</tbody>
	</table>
	
	<table class="write1 mg_t10">
		<colgroup><col width="15%" /></colgroup>
		<thead>
			<tr>
				<th colspan="2">결제처리</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>결제확인일시</th>
				<td>2016-01-20 11:22(수동)</td>
			</tr>
			<tr>
				<th rowspan="3">상세정보</th>
				<td><span class="tdline">입금인</span> <input type="text" id="" class="inp_sty20" /></td>
			</tr>
			<tr>
				<td class="bo_tn"><span class="tdline">입금계좌</span>
					<select class="inp_select" id="">
						<option value="" selected="selected">은행선택</option>
						<option value="">HSBC은행</option>
					</select>
					<input type="text" id="" class="inp_sty40" />
				</td>
			</tr>
			<tr>
				<td class="bo_tn"><span class="tdline">입금일</span>
					<input type="text" id="" class="inp_sty20" /><a href="" class="calendar"></a>
				</td>
			</tr>
			<tr>
				<th rowspan="2">거래증빙신청</th>
				<td><span class="tdline">현금영수증</span>
					<label><input type="radio" id="" class="inp_radio"/>신청</label>
					<label><input type="radio" id="" class="inp_radio"/>미신청</label>
				</td>
			</tr>
			<tr>
				<td class="bg_tn"><span class="tdline">세금계산서</span>
					<label><input type="radio" id="" class="inp_radio"/>신청</label>
					<label><input type="radio" id="" class="inp_radio"/>미신청</label>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="write1 mg_t10">
		<colgroup><col width="15%" /></colgroup>
		<thead>
			<tr>
				<th colspan="2">환불정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th rowspan="2">환불방법</th>
				<td class="bo_tn"><span class="tdline">환불계좌</span>
					<select class="inp_select" id="">
						<option value="" selected="selected">은행선택</option>
						<option value="">HSBC은행</option>
					</select>
					<input type="text" id="" class="inp_sty40" />
				</td>
			</tr>
			<tr>
				<td class="bo_tn"><span class="tdline">예금주</span> <input type="text" id="" class="inp_sty20" /></td>
			</tr>
			<tr>
				<th>환불금액</th>
				<td><span class="tdline">입금일</span>
					<input type="text" id="" class="inp_sty10" /><span class="tdline">원</span></a>
				</td>
			</tr>
		</tbody>
	</table>

</div>
<!-- //popup -->

</body>
</html>