<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[관리자관리]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 관리자관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col width="40%" /><col width="10%" /><col width="40%" /></colgroup>
			<tbody>
				<tr>
					<th>관리자구분</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />슈퍼마스터</label>
						<label><input type="radio" class="inp_radio" />마스터</label>
						<label><input type="radio" class="inp_radio" />Craft Shop 관리담당자</label>
					</td>
					<th>상태</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />이용중</label>
						<label><input type="radio" class="inp_radio" />이용정지</label>
					</td>
				</tr>
				<tr>
					<th>관리자명</th>
					<td><input type="text" class="inp_sty60" /></td>
					<th>관리자 계정(이메일)</th>
					<td><input type="text" class="inp_sty60" /> <span class="ex tdline">* 예시) abc@abc.co.kr</span></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn3">검색</a>
		</div>

		<div class="sub_title"><span>총 101개</span></div>
	
		<table class="write2 cboth">
			<colgroup><col width="10%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>구분</th>
					<th>관리자명</th>
					<th>관리자계정(이메일)</th>
					<th>소속</th>
					<th>상태</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>101</td>
					<td>슈퍼마스터</td>
					<td class="ag_l"><a href="" class="alink">홍길동</a></td>
					<td class="ag_l"><a href="" class="alink">homg@naver.com</a></td>
					<td>경영전략실 기획팀</td>
					<td>이용중</td>
				</tr>
				<tr>
					<td colspan="6">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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

		<div class="btn_list">
			<a href="" class="btn3">신규등록</a>
		</div>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>