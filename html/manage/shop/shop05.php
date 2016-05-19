<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[1:1문의]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 1:1문의</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>답변상태</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>문의접수</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>답변완료</span></label>
					</td>
					<th>문의분류</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>서비스 이용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>결제/정산</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>주문/배송</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>광고/홍보</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>기타</span></label>
					</td>
				</tr>
				<tr>
					<th>승인요청일</th>
					<td>
						<select id="">
							<option value="" selected="selected">등록일</option>
							<option value=""></option>
						</select>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
					</td>
					<th>검색어</th>
					<td>
						<select id="">
							<option value="" selected="selected">제목</option>
							<option value="">본문</option>
						</select>
						<input type="text" id="" class="inp_sty60" />
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
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="38%" /><col width="7%" /><col width="7%" /><col width="8%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th></th>
					<th>No</th>
					<th>문의분류</th>
					<th>제목</th>
					<th>작성자</th>
					<th>답변자</th>
					<th>상태</th>
					<th>등록일</th>
					<th>답변일</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" id="" class="inp_check"/></td>
					<td>101</td>
					<td>서비스 이용</td>
					<td class="ag_l"><a href="">회원가입 시 오류가 났는데...</a></td>
					<td>김남주</td>
					<td>홍길동</td>
					<td>답변완료</td>
					<td>2016-01-05</td>
					<td>2016-02-05</td>
				</tr>
				<tr>
					<td colspan="9">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">선택삭제</a>
		</div>

		<!-- paging -->
		<div class="pagination">
			<a href="#" class="prev"><img src="..//images/adm/btn_paging_prev.gif" alt="전페이지로" /></a>
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