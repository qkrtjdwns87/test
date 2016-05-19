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
	
	<div class="title">
		<h3>[회원 검색]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>회원명</th>
				<td><input type="" class="inp_sty40" /></td>
			</tr>
			<tr>
				<th>계정(이메일)</th>
				<td><input type="" class="inp_sty40" /><span class="ex">* 예시) abc@abc.co.kr</span></td>
			</tr>
			<tr>
				<th>휴대폰</th>
				<td><input type="" class="inp_sty40" /><span class="ex">* 숫자만 입력해 주십시오.</span></td>
			</tr>
			<tr>
				<th>상태</th>
				<td>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>정상</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>보호자 동의 대기</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>패널티 회원</span></label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="btn_list">
		<a href="" class="btn1">초기화</a>
		<a href="" class="btn1">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_l">총 10명</span><span class="fl_r color_day">2016-01-10 12:30 현재</span></div>
	
	<table class="write2">
		<colgroup><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>회원명</th>
				<th>계정(이메일)</th>
				<th>휴대폰</th>
				<th>가입일</th>
				<th>상태</th>
				<th>선택</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>101</td>
				<td><a href="">김나나</a></td>
				<td>popmember@naver.com</td>
				<td>010-1234-5678</td>
				<td>2016-01-30</td>
				<td>정상</td>
				<td><a href="" class="btn2">선택</a></td>
			</tr>
			<tr>
				<td colspan="7">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
			</tr>
		</tbody>
	</table>

	<!-- paging -->
	<div class="pagination">
		<a href="#" class="prev"><img src="/images/adm/btn_paging_prev.gif" alt="전페이지로" /></a>
		<a href="#"><span class="on">1</span></a>
		<a href="#"><span>2</span></a>
		<a href="#"><span>3</span></a>
		<a href="#"><span>4</span></a>
		<a href="#"><span>5</span></a>
		<a href="#"><span>6</span></a>
		<a href="#"><span>7</span></a>
		<a href="#"><span>8</span></a>
		<a href="#"><span>9</span></a>
		<a href="#"><span>10</span></a>
		<a href="#" class="next"><img src="/images/adm/btn_paging_next.gif" alt="다음으로" /></a>
	</div>
	<!--// paging -->
</div>
<!-- //popup -->

</body>
</html>