<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[신규가입통계]</h2>
			<div class="location">Home &gt; 통계/랭킹 &gt; 회원통계 &gt; 신규가입통계</div>
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
					<th>조건</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>일별</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>월별</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>년도별</span></label>
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
			<span class="fl_r">(단위: 명)</span>
		</div>

		<table class="write2 cboth">
			<colgroup><col width="10%" /><col width="10%" /><col width="10%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /></colgroup>
			<thead>
				<tr>
					<th rowspan="2">일자</th>
					<th rowspan="2">신규가입</th>
					<th rowspan="2">남/녀비율</th>
					<th colspan="5">연령</th>
					<th colspan="5">가입방법</th>
				</tr>
				<tr>
					<th>10대</th>
					<th>20대</th>
					<th>30대</th>
					<th>40대</th>
					<th>50대<br />이상</th>
					<th>일반회원</th>
					<th>페이스북</th>
					<th>트위터</th>
					<th>네이버</th>
					<th>카카오</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>2016-01-11</td>
					<td>1,196</td>
					<td>196/1000</td>
					<td>5</td>
					<td>4</td>
					<td>3</td>
					<td>2</td>
					<td>1</td>
					<td>5</td>
					<td>4</td>
					<td>3</td>
					<td>2</td>
					<td>1</td>
				</tr>
				<tr>
					<td colspan="13">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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