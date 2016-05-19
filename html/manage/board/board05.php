<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체메시지현황]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 전체메시지현황</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>확인여부</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />미확인</label>
						<label><input type="radio" class="inp_radio" />확인</label>
					</td>
					<th>검색어</th>
					<td><input type="text" class="inp_sty60" value="내용에서 검색" /></td>
				</tr>
				<tr>
					<th>Craft Shop</th>
					<td><input type="text" class="inp_sty60" value="Cfrat Shop명 (shop 코드)" /><a href="" class="btn1">찾아보기</a></td>
					<th>회원</th>
					<td><input type="text" class="inp_sty60" value="회원명(이메일)" /><a href="" class="btn1">찾아보기</a></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>

		<div class="sub_title">총 101개</div>

		<table class="write2" class="cboth">
			<colgroup><col width="5%" /><col width="5%" /><col width="8%" /><col width="18%" /><col width="18%" /><col width="38%" /><col width="8%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" class="inp_check" /></th>
					<th>No</th>
					<th>발송일시</th>
					<th>송신</th>
					<th>수신</th>
					<th>내용</th>
					<th>수신자<br />확인여부</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" class="inp_check" /></td>
					<td>101</td>
					<td>2016-01-20<br />12:11:35</td>
					<td><a href="" class="alink">poff</a><br />(AC1202456)</td>
					<td><a href="" class="alink">yellowbanan</a><br />(banana@naver.com)</td>
					<td class="ag_l"><a href="" class="alink">주문량이 너무 많아서 잠시 휴업을 할까하는데요..</a></td>
					<td><span class="bold red">미확인</span></td>
				</tr>
				<tr>
					<td><input type="checkbox" class="inp_check" /></td>
					<td>101</td>
					<td>2016-01-20<br />12:11:35</td>
					<td><a href="" class="alink">poff</a><br />(AC1202456)</td>
					<td><a href="" class="alink">yellowbanan</a><br />(banana@naver.com)</td>
					<td class="ag_l"><a href="" class="alink">주문량이 너무 많아서 잠시 휴업을 할까하는데요..</a></td>
					<td><span class="bold">확인</span></td>
				</tr>
				<tr>
					<td colspan="7">검색된 결과가 없습니다.<br />검색조건을 바꾸어 검색해 주시기 바랍니다.</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">엑셀다운로드</a>
			<a href="" class="btn1 fl_r">선택삭제</a>
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