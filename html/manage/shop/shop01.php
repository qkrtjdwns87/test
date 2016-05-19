<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 Craft Shop 현황]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 전체 Craft Shop 현황</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>Shop 상태</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>운영 중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>임시휴업</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>노출중단</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>운영중지</span></label>
					</td>
				</tr>
				<tr>
					<th>Craft Shop명</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th>Craft Shop코드</th>
					<td><input type="text" id="" class="inp_sty90" placeholder="코드 8자리 입력" maxlength="8" /></td>
				</tr>
				<tr>
					<th>계정(이메일)</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>작가명</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th>승인일</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>배지</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>오늘의 작가</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>인기작가</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 1개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="8%" /><col width="10%" /><col width="10%" /><col width="7%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>Shop 코드</th>
					<th>Shop명</th>
					<th>작가</th>
					<th>계정(이메일)</th>
					<th>대표연락처</th>
					<th>등록item수</th>
					<th>승인일</th>
					<th>배지</th>
					<th>상태</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>101</td>
					<td>AC1202456</td>
					<td>POFF</td>
					<td>문소리</td>
					<td>sori@naver.com</td>
					<td>010-1234-5678</td>
					<td>36</td>
					<td>2016-01-10</td>
					<td>오늘의 작가</td>
					<td>운영 중</td>
					<td><a href="" class="btn2">메시지</a><a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<td colspan="11">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">엑셀다운로드</a>
		</div>

		<!-- paging -->
		<div class="pagination cboth">
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