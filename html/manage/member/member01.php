<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체회원현황]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 전체회원현황</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>회원상태</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>정상</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>승인대기</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>패널티</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>휴먼</span></label>
					</td>
					<th>가입방법</th>
					<td>
						<label><input type="checkbox" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="checkbox" id="" name="" class="inp_radio" /><span>일반회원</span></label>
						<label><input type="checkbox" id="" name="" class="inp_radio" /><span>페이스북</span></label>
						<label><input type="checkbox" id="" name="" class="inp_radio" /><span>트위터</span></label>
						<label><input type="checkbox" id="" name="" class="inp_radio" /><span>카카오톡</span></label>
						<label><input type="checkbox" id="" name="" class="inp_radio" /><span>네이버</span></label>
					</td>
				</tr>
				<tr>
					<th>계정(이메일)</th>
					<td><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
					<th>휴대폰</th>
					<td><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>이름</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
					<th>성별</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>남성</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>여성</span></label>
					</td>
				</tr>
				<tr>
					<th>이메일 수신</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신허용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신안함</span></label>
					</td>
					<th>SMS 수신</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신허용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신안함</span></label>
					</td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
					<th>미로그인 시간</th>
					<td><input type="text" id="" class="inp_sty5" /> 일 이상 미접속 회원 <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 1개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="5%" /><col width="15%" /></colgroup>
			<thead>
				<tr>
					<th></th>
					<th>No</th>
					<th>회원명</th>
					<th>계정(이메일)</th>
					<th>휴대폰</th>
					<th>생년월일</th>
					<th>성별</th>
					<th>가입방법</th>
					<th>가입일</th>
					<th>상태</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td>101</td>
					<td><a href="" class="alink">문소리</a></td>
					<td>sorisorisori@naver.com</td>
					<td>010-1234-5678</td>
					<td>1990-05-10</td>
					<td>여성</td>
					<td>일반회원</td>
					<td>2016-01-30</td>
					<td>정상</td>
					<td><a href="" class="btn2">메시지</a><a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td>101</td>
					<td><a href="" class="alink">문소리</a></td>
					<td>sorisorisori@naver.com</td>
					<td>010-1234-5678</td>
					<td>1990-05-10</td>
					<td>여성</td>
					<td>일반회원</td>
					<td>2016-01-30</td>
					<td><span class="red">패널티</span></td>
					<td><a href="" class="btn2">메시지</a><a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td>101</td>
					<td><a href="" class="alink">문소리</a></td>
					<td>sorisorisori@naver.com</td>
					<td>010-1234-5678</td>
					<td>1990-05-10</td>
					<td>여성</td>
					<td>일반회원</td>
					<td>2016-01-30</td>
					<td><span class="blue">승인대기</span></td>
					<td><a href="" class="btn2">메시지</a><a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<td colspan="11">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">엑셀다운로드</a>
			<a href="" class="btn1 fl_r">휴면계정처리</a>
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