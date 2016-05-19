<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[푸시관리]</h2>
			<div class="location">Home &gt; 앱관리 &gt; 푸시관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%"/></colgroup>
			<tbody>
				<tr>
					<th>상태</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />발송완료</label>
						<label><input type="radio" class="inp_radio" />발송예정</label>
						<label><input type="radio" class="inp_radio" />발송취소</label>
					</td>
				</tr>
				<tr>
					<th>분류</th>
					<td>
						<select id="" class="inp_select">
							<option value="" selected="selected">전체 분류</option>
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>검색어</th>
					<td><input type="text" class="inp_sty40" /></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 101개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="5%" /><col width="15%" /><col width="35%" /><col width="10%" /><col width="5%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" class="inp_check" /></th>
					<th>No</th>
					<th>상태</th>
					<th>분류</th>
					<th>제목</th>
					<th>발송일시</th>
					<th>발송량</th>
					<th>성공</th>
					<th>실패</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" class="inp_check" /></td>
					<td>101</td>
					<td>발송예정</td>
					<td>할인/이벤트/상품정보</td>
					<td class="ag_l"><a href="" class="alink">가입만 하셔도 100%의 행운을 드립니다.</a></td>
					<td>2016-05-10 11:22</td>
					<td>12</td>
					<td>9(100%)</td>
					<td>0(0%)</td>
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