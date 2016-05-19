<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[인기 Shop 랭킹]</h2>
			<div class="location">Home &gt; 통계/랭킹 &gt; > Craft Shop 랭킹 &gt; 인기 Shop 랭킹</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>기간선택</th>
					<td>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>순위 기준</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매총액</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매건수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>조회수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>Flag수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>댓글수</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">
			<span class="bold fl_l">2016-01-11 ~ 2016-02-11</span>
			<span class="fl_r">(금액단위: 원)</span>
		</div>

		<table class="write2 cboth">
			<colgroup>
				<col width="5%" /><col width="10%" /><col width="25%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="5%" /><col width="5%" /><col width="5%" /><col width="5%" />
			</colgroup>
			<thead>
				<tr>
					<th>순위</th>
					<th>Shop 코드</th>
					<th>Shop 명</th>
					<th>작가</th>
					<th>등록Item수</th>
					<th>승인일</th>
					<th>판매총액</th>
					<th>판매건수</th>
					<th>조회수</th>
					<th>Flag수</th>
					<th>댓글수</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>AC1202456</td>
					<td class="ag_l"><a href="" class="alink">poff</a></td>
					<td>문소리</td>
					<td>36</td>
					<td>2016-01-20</td>
					<td>400,000</td>
					<td>5</td>
					<td>22</td>
					<td>20</td>
					<td>15</td>
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