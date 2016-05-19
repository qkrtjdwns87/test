<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[구매액 상위 회원]</h2>
			<div class="location">Home &gt; 통계/랭킹 &gt; 회원통계 &gt; 구매액 상위 회원</div>
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
			<span class="fl_r">(금액단위: 원)</span>
		</div>

		<table class="write2 cboth">
			<colgroup><col width="5%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="18%" /><col width="5%" /><col width="18%" /></colgroup>
			<thead>
				<tr>
					<th>순위</th>
					<th>회원명</th>
					<th>계정(이메일)</th>
					<th>휴대폰</th>
					<th>가입일</th>
					<th>상태</th>
					<th>기간 내 총 실결제금액</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td><a href="" class="alink">김나나</a></td>
					<td>abc@abc.co.kr</td>
					<td>010-1234-56788</td>
					<td>2016-01-20</td>
					<td>정상</td>
					<td>10,000,000</td>
				</tr>
				<tr>
					<td>2</td>
					<td><a href="" class="alink">김나나</a></td>
					<td>abc@abc.co.kr</td>
					<td>010-1234-56788</td>
					<td>2016-01-20</td>
					<td><span class="blue">승인대기</span></td>
					<td>7,000,000</td>
				</tr>
				<tr>
					<td>3</td>
					<td><a href="" class="alink">김나나</a></td>
					<td>abc@abc.co.kr</td>
					<td>010-1234-56788</td>
					<td>2016-01-20</td>
					<td><span class="red">패널티</span></td>
					<td>5,000,000</td>
				</tr>
				<tr>
					<td colspan="7">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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