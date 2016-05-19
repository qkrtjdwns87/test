<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[탈퇴관리]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 탈퇴관리</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>탈퇴유형</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>일반탈퇴</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>강제탈퇴</span></label>
					</td>
					<th>계정(이메일)</th>
					<td><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>탈퇴사유</th>
					<td colspan="3">
						<select id="">
							<option value="" selected="selected">개인정보 유출이 염려된다</option>
							<option value="">이용빈도가 낮다</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 101명</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="25%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="35%" /></colgroup>
			<thead>
				<tr>
					<th></th>
					<th>No</th>
					<th>계정(이메일)</th>
					<th>가입일</th>
					<th>탈퇴일 <a href="#" class="tooltip" data-tooltip="- 일반탈퇴 : 회원이 직접 탈퇴양식을 작성하여 탈퇴
- 강제탈퇴 : 관리자에 의해 삭제처리 된 회원">?</a></th>
					<th>탈퇴유형</th>
					<th>탈퇴사유</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td>101</td>
					<td>sorisorisori@naver.com</td>
					<td>1990-05-10</td>
					<td>2016-01-30</td>
					<td>일반탈퇴</td>
					<td>자유 이용하지 않음</td>
				</tr>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td>101</td>
					<td>sorisorisori@naver.com</td>
					<td>1990-05-10</td>
					<td>2016-01-30</td>
					<td>강제탈퇴</td>
					<td>자유 이용하지 않음</td>
				</tr>
				<tr>
					<td colspan="7">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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