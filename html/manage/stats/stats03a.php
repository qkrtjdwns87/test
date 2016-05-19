<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[연령대별 선호Item]</h2>
			<div class="location">Home &gt; 통계/랭킹 &gt; Item랭킹 &gt; 연령대별 선호Item</div>
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
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매수량</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매건수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>판매총액</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>조회수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>Flag수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>장바구니 담긴수</span></label>
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
				<col width="5%" /><col width="14%" /><col width="5%" /><col width="14%" /><col width="5%" /><col width="14%" /><col width="5%" /><col width="14%" /><col width="5%" /><col width="14%" /><col width="5%" />
			</colgroup>
			<thead>
				<tr>
					<th rowspan="2">순위</th>
					<th colspan="2">10대</th>
					<th colspan="2">20대</th>
					<th colspan="2">30대</th>
					<th colspan="2">40대</th>
					<th colspan="2">50대 이상</th>
				</tr>
				<tr>
					<th>Item이름(코드)</th>
					<th>판매수량</th>
					<th>Item이름(코드)</th>
					<th>판매수량</th>
					<th>Item이름(코드)</th>
					<th>판매수량</th>
					<th>Item이름(코드)</th>
					<th>판매수량</th>
					<th>Item이름(코드)</th>
					<th>판매수량</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
					<td>20</td>
					<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
					<td>20</td>
					<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
					<td>20</td>
					<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
					<td>20</td>
					<td class="ag_l"><a href="" class="alink">크리스마스 한정 블랙 클러치</a></td>
					<td>20</td>
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