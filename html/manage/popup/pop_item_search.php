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
		<h3>[Item 검색]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>Item 카테고리</th>
				<td>
					<select id="">
						<option value="" selected="selected">전체 카테고리</option>
						<option value=""></option>
					</select>
				</td>
			</tr>
			<tr>
				<th rowspan="2">Item</th>
				<td>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>이름</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>코드</span></label>
				</td>
			</tr>
			<tr>
				<td class="bo_tn pd_tn"><input type="text" id="" class="inp_sty90" /></td></td>
			</tr>
			
			<tr>
				<th rowspan="2">Craft Shop</th>
				<td>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>이름</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>코드</span></label>
				</td>
			</tr>
			<tr>
				<td class="bo_tn pd_tn"><input type="text" id="" class="inp_sty90" /></td></td>
			</tr>
			<tr>
				<th>작가</th>
				<td><input type="text" id="" class="inp_sty90" /></td>
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
				<th>Item 코드</th>
				<th>Item 이름</th>
				<th>Craft Shop</th>
				<th>작가</th>
				<th>승인일</th>
				<th>선택</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>101</td>
				<td>AC1202456</td>
				<td><a href="">크리스마스 한정 블랙 클러치</a></td>
				<td>ROFF</td>
				<td>문소리</td>
				<td>2016-01-20</td>
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