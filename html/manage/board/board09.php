<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[약관관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 약관관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>구분</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />필수</label>
						<label><input type="radio" class="inp_radio" />선택</label>
					</td>
					<th>사용여부</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />사용중</label>
						<label><input type="radio" class="inp_radio" />미사용</label>
					</td>
				</tr>
				<tr>
					<th>분류</th>
					<td>
						<select id="" class="inp_select">
							<option value="" selected="selected">전체</option>
							<option value=""></option>
						</select>
					</td>
					<th>검색어</th>
					<td>
						<select id="" class="inp_select">
							<option value="" selected="selected">전체</option>
							<option value=""></option>
						</select>
						<input type="text" class="inp_sty60" />
					</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>

		<div class="sub_title">총 101개</div>

		<table class="write2" class="cboth">
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="50%" /><col width="5%" /><col width="10%" /><col width="10%" /><col width="5%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>구분</th>
					<th>분류</th>
					<th>제목</th>
					<th>작성자</th>
					<th>시행일</th>
					<th>등록일</th>
					<th>사용여부</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>101</td>
					<td>필수</td>
					<td>서비스 이용약관</td>
					<td class="ag_l"><a href="" class="alink">서비스 이용약관  V.2.1</a></td>
					<td>홍길동</td>
					<td>2016-01-20</td>
					<td>2016-02-20</td>
					<td>사용중</td>
				</tr>
				<tr>
					<td colspan="8">검색된 결과가 없습니다.<br />검색조건을 바꾸어 검색해 주시기 바랍니다.</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">신규등록</a>
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