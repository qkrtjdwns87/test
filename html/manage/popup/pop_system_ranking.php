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
		<h3>[인기 급상승 Item 검색]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="10%" /></colgroup>
		<tbody>
			<tr>
				<th>기준</th>
				<td>
					<label><input type="radio" class="inp_radio" />판매순위 급상승</label>
					<label><input type="radio" class="inp_radio" />Flag순위 급상승</label>
				</td>
			</tr>
			<tr>
				<th>기간</th>
				<td>
					<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="btn_list">
		<a href="" class="btn1">초기화</a>
		<a href="" class="btn1">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_r color_day">2016-01-10 12:30 현재</span></div>
	
	<table class="write2">
		<colgroup><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="30%" /><col width="10%" /><col width="10%" /><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>순위</th>
				<th>상승순위</th>
				<th>현 판매순위</th>
				<th>Item 코드</th>
				<th>Item 이름</th>
				<th>Craft Shop</th>
				<th>승인일</th>
				<th>담기</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>1</td>
				<td><span class="icn_up">102</span></td>
				<td>25</td>
				<td>AC1202456</td>
				<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
				<td>poff</td>
				<td>2016-01-20</td>
				<td><a href="" class="btn2">담기</a></td>
			</tr>
			<tr>
				<td>2</td>
				<td><span class="icn_down">102</span></td>
				<td>25</td>
				<td>AC1202456</td>
				<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
				<td>poff</td>
				<td>2016-01-20</td>
				<td><a href="" class="btn2">담기</a></td>
			</tr>
			<tr>
				<td>3</td>
				<td><span class="icn_middle"></span></td>
				<td>25</td>
				<td>AC1202456</td>
				<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
				<td>poff</td>
				<td>2016-01-20</td>
				<td><a href="" class="btn2">담기</a></td>
			</tr>
			<tr>
				<td colspan="8">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
			</tr>
		</tbody>
	</table>

	<!-- paging -->
	<div class="pagination">
		<a href="#" class="prev"><img src="..//images/adm/btn_paging_prev.gif" alt="전페이지로" /></a>
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
		<a href="#" class="next"><img src="..//images/adm/btn_paging_next.gif" alt="다음으로" /></a>
	</div>
	<!--// paging -->
	
	<!-- cart -->
	<div class="cart">
		<p class="title">담긴 Item</p>
		<ul>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href="" class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href=""class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href=""class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href=""class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
		</ul>
		<div class="btn_list">
			<a href="" class="btn2">모두삭제</a>
			<a href="" class="btn2">모두선택</a>
		</div>
	</div>
	<!-- //cart -->

</div>
<!-- //popup -->

</body>
</html>