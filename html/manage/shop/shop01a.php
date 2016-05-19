<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 Craft Shop 현황]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 전체 Craft Shop 현황</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="10%" /><col width="35%" /><col width="10%" /><col width="35%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="5">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>판매자 구분</th>
					<td colspan="3">개인판매자</td>
					<td rowspan="5"><img src="/images/adm/shop.jpg" width="150" height="150" alt="" /></td>
				</tr>
				<tr>
					<th>Shop 코드</th>
					<td colspan="3">개인판매자</td>
				</tr>
				<tr>
					<th>Shop 명</th>
					<td colspan="3">ROFF</td>
				</tr>
				<tr>
					<th>계정(이메일)</th>
					<td colspan="3">abc@abc.co.kr</td>
				</tr>
				<tr>
					<th>작가</th>
					<td colspan="3">문소리 <a href="" class="btn2">메시지</a><a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호</th>
					<td colspan="4">
						<a href="" class="btn2">대표 이메일로 임시 비밀번호 발송</a><a href="" class="btn2">작가 휴대폰으로 임시 비밀번호 발송</a>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 이메일</th>
					<td colspan="4"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 휴대폰 번호</th>
					<td colspan="4"><input type="text" id="" class="inp_sty40" /> <span class="ex">*숫자만 입력</span></td>
				</tr>
				<tr>
					<th>계약시작일</th>
					<td>2016-01-10</td>
					<th>계약종료일</th>
					<td colspan="2">2017-01-10</td>
				</tr>
				<tr>
					<th>승인일자</th>
					<td>2016-01-10 15:10:20</td>
					<th>승인처리자</th>
					<td colspan="2">홍길동</td>
				</tr>
				<tr>
					<th>CIRCUS 담당자</th>
					<td colspan="4">
						홍길동 / 02-456-789 / 010-1234-5678 <a href="" class="btn1">변경</a>
					</td>
				</tr>
				<tr>
					<th rowspan="2">Shop 상태</th>
					<td colspan="4">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>운영 중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>임시휴업</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>노출중단</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>운영중지</span></label>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>배치</th>
					<td colspan="4">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>오늘의 작가</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>인기작가</span></label>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">사업자 정보</th>
					<th class="ag_r">
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>사업자번호</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) 111-11-11111</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자형태</th>
					<td colspan="3">개인사업자</td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
					<th><span class="important">*</span>대표자명</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
					<th><span class="important">*</span>종목</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc#abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 전화</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>사업장 소재지</th>
					<td colspan="3"><input type="text" id="" class="inp_sty10" /><a href="" class="btn1">우편번호 찾기</a></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>통신판매업 번호</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">기본 배송 정보 및 정책</th>
					<th class="ag_r">
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>기본 택배사 설정</th>
					<td colspan="3">
						<select id="">
							<option value="" selected="selected">배송업체선택 (default)</option>
							<option value="" >CJ 대한통운</option>
							<option value="" >우체국택배</option>
							<option value="" >한진택배</option>
							<option value="" >현대택배</option>
							<option value="" >로젠택배</option>
							<option value="" >KG로지스</option>
							<option value="" >CVSnet 편의점택배</option>
							<option value="" >KGB택배</option>
							<option value="" >경동택배</option>
							<option value="" >대신택배</option>
							<option value="" >일양로지스</option>
							<option value="" >합동택배</option>
							<option value="" >GTX로지스</option>
							<option value="" >건영택배</option>
							<option value="" >천일택배</option>
							<option value="" >한의사랑택배</option>
							<option value="" >한덱스</option>
						</select>
						<span class="ex">* Shop에서 이용하는 택배 업체 선택</span>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>반품 택배사 설정</th>
					<td colspan="3">Shop 기본 택배사 이용</td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>배송비 정책</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>선불</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>착불</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>무료</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>조건부</span></label>
						<input type="text" id="" class="inp_sty10" /> 원 이상 구매 시 무료</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">※ 무료 선택 시에도 도서산간 추가비용, 지역별 차등배송비는 추가됩니다.</td>
				</tr>
				<tr>
					<th><span class="important">*</span>배송비</th>
					<td colspan="3"><input type="text" id="" class="inp_sty10" /> <span class="ex">* 선불, 착불, 반품 배송비 모두 동일하게 책정</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>도서산간 배송가능 여부</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>가능</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>불가능</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>도서산간 추가비용 사용 여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용안함</span></label>
					</td>
					<th>도서산간 추가비용 금액</th>
					<td><input type="text" id="" class="inp_sty30" /> 원</td>
				</tr>
				<tr>
					<th><span class="important">*</span>지역별 차등배송비 사용 여부</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용안함</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>지역별 배송비용</th>
					<td colspan="3">
						<p>※ 지역명 입력 시 콤마(,)로 구분하고, ‘시’ ‘도’ 등은 빼고 입력 (예사. 제주,울릉, 거제)<br /><br /></p>
						<input type="text" id="" class="inp_sty10 mg_r10" placeholder="배송지역 입력" /></span> <input type="text" id="" class="inp_sty10" /> 원 <a href="" class="btn1">삭제</a> <br /><br />
						<input type="text" id="" class="inp_sty10 mg_r10" placeholder="배송지역 입력" /></span> <input type="text" id="" class="inp_sty10" /> 원 <a href="" class="btn1">삭제</a> <br /><br />
						<input type="text" id="" class="inp_sty10 mg_r10" placeholder="배송지역 입력" /></span> <input type="text" id="" class="inp_sty10" /> 원 <a href="" class="btn1">추가</a>
					</td>
				</tr>
				<tr>
					<th rowspan="4"><span class="important">*</span>반품지 연락처 및 주소</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /> <span class="ex">* 숫자만 입력</span>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">
						<p><label><input type="checkbox" id="" name="" class="inp_check" /><span>사업장 소재지 주소와 동일</span></label></p><br />
						<input type="text" id="" class="inp_sty20" /><a href="" class="btn1">우편번호 찾기</a>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>교환 및 환불 정책</th>
					<td colspan="3" class="bo_tn pd_tn">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체 Item 공통 정책 적용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>Item 개별 정책 적용</span></label>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>Shop 자체 정책 사용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>CIRCUS 기본정책 사용</span></label>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">
						<textarea id="" rows="10" cols="100">게시판영역</textarea>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">정산 정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>정산주기</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>월정산</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>일정산</span></label>
					</td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>정산주기</th>
					<td colspan="3">
						<span>은행명</span>
						<select id="">
							<option value="" selected="selected">은행명</option>
							<option value=""></option>
						</select>
						<span class="mg_l10">예금주명</span>
						<input type="text" id="" class="inp_sty10" />
					</td>
				</tr>
				<tr>
					<td colspan="3"><span class="mg_r10">계좌번호</span><input type="text" id="" class="inp_sty10" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">판매 내역</th>
					<th class="ag_r">
						<span>2016-01-10 11:30 현재</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 판매금액</th>
					<td colspan="3">1,050,000원</td>
				</tr>
				<tr>
					<th>총 건수</th>
					<td>7건</td>
					<th>매출순위</th>
					<td>542위</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">활동정보</th>
					<th class="ag_r">
						<span>2016-01-10 11:30 현재</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>등록 Item / Flag수</th>
					<td>54개 / 56건</td>
					<th>Shop Flag 수</th>
					<td>7건</td>
				</tr>
				<tr>
					<th>PC웹에서 공유된 건</th>
					<td>0건</td>
					<th>모바일앱에서 공유된 건</th>
					<td>0건</td>
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