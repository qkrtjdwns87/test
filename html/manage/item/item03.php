<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[기획전관리]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 기획전관리</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>진행상태</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>진행중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>진행예정</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>종료</span></label>
					</td>
					<th>게시여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>게시</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>미게시</span></label>
					</td>
				</tr>
				<tr>
					<th>진행기간</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>검색어</th>
					<td colspan="3">
						<select class="inp_select" id="">
							<option value="">전체 카테고리</option>
						</select>
						<input type="text" id="" class="inp_sty30" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn2">검색</a>
		</div>
		
		<div class="sub_title2">총 101개</div> 
		<table class="write2">
			<colgroup><col width="3%" /><col width="5%" /><col width="7%" /><col width="20%" /><col width="10%" /><col width="17%" /><col width="7%" /><col width="10%" /><col width="13%" /><col width="9%" /></colgroup>
			<thead>
				<tr>
					<th><label class="mgn"><input type="checkbox" id="" name="" class="inp_check mgn"><span class="blind">선택</span></label></th>
					<th>No</th>
					<th>상태</th>
					<th>제 목</th>
					<th>Item 개수</th>
					<th>진행기간</th>
					<th>게시여부</th>
					<th>작성자</th>
					<th>등록일</th>
					<th>조회수</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><label class="mgn"><input type="checkbox" id="" name="" class="inp_check mgn"><span class="blind">선택</span></label></td>
					<td>101</td>
					<td>진행중</td>
					<td class="ag_l"><a href="" class="alink">서커스 출석체크</a></td>
					<td>15</td>
					<td><span>YYYY-MM-DD</span> ~ <div>YYYY-MM-DD</div></td>
					<td>게시</td>
					<td>홍길동</td>
					<td>YYYY-MM-DD</td>
					<td>54</td>
				</tr>
				
				<!-- 검색결과 없는 경우 -->
				<tr>
					<td colspan="10" class="ag_c pd_t20 pd_b20">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다.</td>
				</tr>
				<!-- //검색결과 없는 경우 -->
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">선택삭제</a>
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