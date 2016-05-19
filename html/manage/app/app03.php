<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[공지팝업관리]</h2>
			<div class="location">Home &gt; 앱관리 &gt; 공지팝업관리</div>
		</div>
		
		<div class="btn_list">
			<span class="fl_l pd_t20">총 101개</span>
			<select id="" class="inp_select ">
				<option value="" selected="selected">제목</option>
				<option value="">내용</option>
				<option value="">제목+내용</option>
			</select><input type="text" id="" class="inp_sty10 mg_l10" />
			<a href="" class="btn1">검색</a>
		</div>

		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="5%" /><col width="65%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" class="inp_check" /></th>
					<th>No</th>
					<th>구분</th>
					<th>제목</th>
					<th>작성자</th>
					<th>등록일</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" class="inp_check" /></td>
					<td>101</td>
					<td>일반</td>
					<td class="ag_l"><a href="" class="alink">주말 세일 특가</a></td>
					<td>작성자</td>
					<td>2016-05-10 11:22</td>
				</tr>
				<tr>
					<td colspan="6">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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