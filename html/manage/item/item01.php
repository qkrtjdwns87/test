<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 Item 현황]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 전체 Item 현황</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>진열상태</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>진열 중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>진열 안함</span></label>
					</td>
					<th>판매상태</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매 중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>품절</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매중지</span></label>
					</td>
				</tr>
				<tr>
					<th>Item 카테고리</th>
					<td colspan="3">
						<select class="inp_select" id="">
							<option value="">전체 카테고리</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Item 명</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th>Item 코드</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>Craft Shop 명</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th>Craft Shop 코드</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>작가명</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th>태그</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>승인일</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn2">검색</a>
		</div>
		
		
		<div class="sub_title2 fl_l">총 101개</div> 

		<table class="write2">
			<colgroup><col width="3%" /><col width="5%" /><col width="9%" /><col width="31%" /><col width="11%" /><col width="7%" /><col width="10%" /><col width="10%" /><col width="7%" /><col width="7%" /></colgroup>
			<thead>
				<tr>
					<th><label class="mgn"><input type="checkbox" id="" name="" class="inp_check mgn"><span class="blind">선택</span></label></th>
					<th>No</th>
					<th>Item코드</th>
					<th>Item</th>
					<th>Craft Shop(코드)</th>
					<th>작가</th>
					<th>가격(원)</th>
					<th>승인일</th>
					<th>진열상태</th>
					<th>판매상태</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><label class="mgn"><input type="checkbox" id="" name="" class="inp_check mgn"><span class="blind">선택</span></label></td>
					<td>101</td>
					<td>AC1202456</td>
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd><a class="link1" href="">크리스마스한정 블랙 클러치</a></dd>
						</dl>
					</td>
					<td><a href="" class="alink">Poff</a><div>(AC457896)</div></td>
					<td>문소리</td>
					<td>30,000</td>
					<td>YYYY-MM-DD</td>
					<td>진열 중</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="10">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<a href="" class="btn1 mg_t10">엑셀다운로드</a>

		<div class="btn_list mg_t10">
			<span class="tdline">선택한 아이템을 </span>
			<a href="" class="btn1">진열중</a> 
			<a href="" class="btn1">진열안함</a> 
			<a href="" class="btn1">판매 중</a>
			<a href="" class="btn1">품절</a>
			<a href="" class="btn1">판매중지</a>
			<a href="" class="btn1">삭제</a>
			<span class="tdline">처리</span>
		</div>

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
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>