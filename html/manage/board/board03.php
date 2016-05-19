<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[이벤트]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 이벤트</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>진행상태</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />진행중</label>
						<label><input type="radio" class="inp_radio" />종료</label>
					</td>
					<th>게시여부</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />게시</label>
						<label><input type="radio" class="inp_radio" />미게시</label>
					</td>
				</tr>
				<tr>
					<th>프로모션기간</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
					</td>
				</tr>
				<tr>
					<th>검색어</th>
					<td colspan="3">
						<select id="" class="inp_select">
							<option value="" selected="selected">제목</option>
							<option value=""></option>
						</select>
						<input type="text" id=""  class="inp_sty60" />
					</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 101개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="45%" /><col width="10%" /><col width="5%" /><col width="10%" /><col width="5%" /><col width="5%" /></colgroup>
			<thead>
				<tr>
					<th></th>
					<th>No</th>
					<th>상태</th>
					<th>제목</th>
					<th>이벤트 기간</th>
					<th>게시여부</th>
					<th>작성자</th>
					<th>등록일</th>
					<th>조회수</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td>101</td>
					<td>진행중</td>
					<td class="ag_l"><a href="" class="alink">서커스 출석체크</a></td>
					<td>2016-01-01~<br />2016-02-01</td>
					<td>게시</td>
					<td>홍길동</td>
					<td>2016-02-02</td>
					<td>54</td>
				</tr>
				<tr>
					<td colspan="9">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">선택삭제</a>
			<a href="" class="btn1">신규등록</a>
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