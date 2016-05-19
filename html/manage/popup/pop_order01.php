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
				<td class="on"><a href="">주문 정보</a></td>
				<td><a href="">결제 정보</a></td>
				<td><a href="">주문자 정보</a></td>
				<td><a href="">수령인 정보</a></td>
				<td><a href="">관리자 메모</a></td>
				<td><a href="">변경내역</a></td>
			</tr>
		<tbody>
	</table>
		
	<div class="sub_title mg_t10">
		<strong class="fl_l font15">주문정보</strong>
		<div class="fl_r">
			<span class="tdline">주문상태를</span>
			<select class="inp_select" id="">
				<option value="" selected="selected">상태값 선택</option>
				<option value=""></option>
			</select> 
			<span class="tdline">으로</span>
			<a href="" class="btn1">변경</a><span class="tdline">또는</span><a href="" class="btn1">이전상태로 되돌리기</a>
		</div>
	</div>

	<table class="write2">
		<thead>
			<tr>
				<th>주문Item / 옵션</th>
				<th>수량</th>
				<th>구매금액(원)</th>
				<th>배송비(원)</th>
				<th>Craft Shop(코드)</th>
				<th>배송정보(발송일)</th>
				<th>주문상태</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<dl class="dl_img1">
						<dt><img src="..//images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
						<dd><a href="" class="alink">크리스마스한정 블랙 클러치</a></dd>
						<dd>색상: 녹색</dd>
						<dd>재질: 가죽</dd>
						<dd>퀼트(+1,000원)</dd>
						<dd>문양: 나무</dd>
						<dd>주머니: 있음</dd>
						<dd>선물포장: 없음</dd>
					</dl>
				</td>
				<td>2</td>
				<td>32,000</td>
				<td>2,500</td>
				<td>Poff(AC4567896)</td>
				<td>
					<p>로젠택배<br />457896456212<br />(YYYY-MM-DD) </p>
					<a href="" class="btn2">등록</a>
				</td>
				<td>취소신청<br /><br />
					<a href="" class="btn2">취소신청사유</a><br /><br />
					<a href="" class="btn2">불가사유등록</a>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="fl_r mg_t10">
		<span class="tdline">주문상태를</span>
		<select class="inp_select" id="">
			<option value="" selected="selected">상태값 선택</option>
			<option value=""></option>
		</select> 
		<span class="tdline">으로</span>
		<a href="" class="btn1">변경</a><span class="tdline">또는</span><a href="" class="btn1">이전상태로 되돌리기</a>
	</div>



</div>
<!-- //popup -->

</body>
</html>