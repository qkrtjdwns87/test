<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체회원현황]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 전체회원현황</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col width="35%" /><col width="12%" /><col width="35%" /><col width="6%" /></colgroup>
			<thead>
				<tr>
					<th colspan="5">개인정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>계정(이메일)</th>
					<td colspan="3">abc@abc.com</td>
					<td rowspan="3"><img src="/images/adm/shop.jpg" width="100" height="100" alt="" /></td>
				</tr>
				<tr>
					<th>이름</th>
					<td colspan="3">김나나 <a href="" class="btn2">메시지</a><a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호</th>
					<td colspan="3">
						<a href="" class="btn2">이메일로 임시 비밀번호 발송</a><a href="" class="btn2">휴대폰으로 임시 비밀번호 발송</a>
					</td>
				</tr>
				<tr>
					<th>생년월일</th>
					<td>
						<select id="">
							<option value="" selected="selected">년</option>
							<option value="">2000</option>
						</select>
						<select id="">
							<option value="" selected="selected">월</option>
							<option value="">12</option>
						</select>
						<select id="">
							<option value="" selected="selected">일</option>
							<option value="">12</option>
						</select>
					</td>
					<th>성별</th>
					<td colspan="2">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>남성</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>여성</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>정보수신 이메일</th>
					<td colspan="4"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>휴대폰 번호</th>
					<td colspan="4"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>가입방법</th>
					<td>일반회원</td>
					<th>가입일시</th>
					<td colspan="2">2017-01-10 11:33:55</td>
				</tr>
				<tr>
					<th>승인일시</th>
					<td>2016-01-10 15:10:20</td>
					<th>패널티적용일시</th>
					<td colspan="2">2016-08-13 15:10:20</td>
				</tr>
				<tr>
					<th rowspan="2">회원상태</th>
					<td colspan="4">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>정상</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>승인대기</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>패널티</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>휴먼</span></label>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="bo_tn pd_tn">
						<textarea id="" rows="5" cols="5" class="textarea1">메모(최대 500자)</textarea>
						<p><span class="ex">패널티 회원은 모든 댓글(한줄 남기기) 서비스를 이용할 수 없습니다.</span></p>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">방문정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>최근 로그인 일시</th>
					<td>2016-01-20 11:22:11</td>
					<th>최근 방문 IP/UUID</th>
					<td>101.254.118.31</td>
				</tr>
				<tr>
					<th>미로그인</th>
					<td colspan="3">3일</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">정보 수신 동의</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>이메일 수신</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신허용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신안함</span></label>
					</td>
					<th>최근 이메일 수신정보 변경</th>
					<td>2016-01-20 11:22:55</td>
				</tr>
				<tr>
					<th>SMS 수신</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신허용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수신안함</span></label>
					</td>
					<th>최근 SMS 수신정보 변경</th>
					<td>2016-01-20 11:22:55</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">구매내역</th>
					<th class="ag_r">2016-01-30 11:44 현재 <a href="" class="btn1">자세히 보기</a></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 결제금액</th>
					<td colspan="3"><strong>1,050,000원</strong></td>
				</tr>
				<tr>
					<th>총 건수</th>
					<td colspan="3"><strong>7건</strong></td>
				</tr>
				<tr>
					<th>최근 배송지</th>
					<td colspan="3">42533 서울시 서초구 서초3동 359-19 서진빌딩 4층</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">활동정보</th>
					<th class="ag_r">2016-01-30 11:44 현재</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Item Flag</th>
					<td>54개</td>
					<th>Craft Shop Flag</th>
					<td>7개</td>
				</tr>
				<tr>
					<th>댓글(한줄 남기기)</th>
					<td>10건</td>
					<th>메시지 발송</th>
					<td>3건</td>
				</tr>
				<tr>
					<th>Follower</th>
					<td>2명</td>
					<th>Following</th>
					<td>3명</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn3">저장</a>
			<a href="" class="btn1">목록</a>
		</div>

		<div>
			<textarea id="" rows="5" cols="5" class="textarea1">최대 200자</textarea>
		</div>

		<div class="btn_list">
			<a href="" class="btn3">등록</a>
		</div>

		<div class="reply">
			<ul>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
			</ul>
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