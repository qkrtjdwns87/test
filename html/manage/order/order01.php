<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 주문현황]</h2>
			<div class="location">Home &gt; 주문관리 &gt; 전체 주문현황</div>
		</div>
		

		<table class="write2">
			<thead>
				<tr>
					<th>오늘 신규주문</th>
					<th>미확인</th>
					<th>입금대기</th>
					<th>입금확인</th>
					<th>결제완료</th>
					<th>취소신청</th>
					<th>환불신청</th>
					<th>환불보류</th>
					<th>반품신청</th>
					<th>반품보류</th>
					<th>교환요청</th>
					<th>교환보류</th>
					<th>환불승인</th>
					<th>배송중</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href=""><span class="blue bold">6</span>건</a></td>
					<td><a href=""><span class="blue bold">1</span>건</a></td>
					<td><a href=""><span class="blue bold">2</span>건</a></td>
					<td><a href=""><span class="blue bold">1</span>건 </a></td>
					<td><a href=""><span class="blue bold">2</span>건</a></td>
					<td><a href=""><span class="blue bold">0</span>건 </a></td>
					<td><a href=""><span class="blue bold">1</span>건</a></td>
					<td><a href=""><span class="blue bold">0</span>건</a></td>
					<td><a href=""><span class="blue bold">0</span>건</a></td>
					<td><a href=""><span class="blue bold">0</span>건</a></td>
					<td><a href=""><span class="blue bold">0</span>건</a></td>
					<td><a href=""><span class="blue bold">1</span>건</a></td>
					<td><a href=""><span class="blue bold">1</span>건</a></td>
					<td><a href=""><span class="blue bold">8</span>건</a></td>
				</tr>
			</tbody>
		</table>
		
		<table class="write1 mg_t10">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>검색어</th>
					<td colspan="3">
						<select class="inp_select" id="">
							<option value="" selected="selected">주문번호</option>
							<option value=""></option>
						</select>
						<input type="text" id="" class="inp_sty40" />
					</td>
				</tr>
				<tr>
					<th rowspan="3">주문상태 <br/ > <br/ > <label><input type="checkbox" id="" name="" class="inp_check" /> 전체 선택</label></th>
					<td colspan="3"><strong>결제</strong>
						<label><input type="checkbox" id="" name="" class="inp_check" />입금대기</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />입금확인(자동)</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />입금확인(수동)</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />결제완료</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />결제취소</label>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn">
						<strong>배송</strong>
						<label><input type="checkbox" id="" name="" class="inp_check" />Item준비중</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />배송중</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />배송완료</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />배송중(교환Item)</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />배송완료(교환Item)</label>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn">
						<label><input type="checkbox" id="" name="" class="inp_check" />구매 취소 전체</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />환불/반품 전체</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />교환 전체</label>
					</td>
				</tr>
				<tr>
					<th>Item명</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
					<th>Item코드</th>
					<td><input type="text" id="" class="inp_sty40"  placeholder="코드 8자리 입력" maxlength="8" /></td>
				</tr>
				<tr>
					<th>Craft Shop명</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
					<th>Craft Shop코드</th>
					<td><input type="text" id="" class="inp_sty40"  placeholder="코드 8자리 입력" maxlength="8" /></td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td colspan="3">
						<select class="inp_select" id="">
							<option value="" selected="selected">주문일</option>
							<option value=""></option>
						</select>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>결제수단</th>
					<td colspan="3">
						<label><input type="checkbox" id="" name="" class="inp_check" />전체</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />신용카드(카드)</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />무통장입금</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />휴대폰</label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 101건</div>
		<table class="write2">
			<colgroup><col width="5%" /></colgroup>
			<thead>
				<tr>
					<th rowspan="2"></th>
					<th rowspan="2">주문번호</th>
					<th rowspan="2">주문일시</th>
					<th rowspan="2">주문Item</th>
					<th rowspan="2">Craft Shop(코드)</th>
					<th rowspan="2">주문자(연락처)</th>
					<th rowspan="2">구매금액(원)</th>
					<th rowspan="2">배송비(원)</th>
					<th rowspan="2">총 실결제금액(원)</th>
					<th rowspan="2">결제수단</th>
					<th colspan="5">주문상태</th>
				</tr>
				<tr>
					<th>결제상태</th>
					<th>배송상태(발송일)</th>
					<th>구매취소</th>
					<th>환불/반품</th>
					<th>교환</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td><a href="" class="alink">201512312</a></td>
					<td>2016-01-20 11:22</td>
					<td><a href="" class="alink">크리스마스 한정블랙 클러치외 2개</a></td>
					<td>poff<br />(AC457896)</td>
					<td>문소리<br />(010-1234-5678)</td>
					<td>30,000</td>
					<td>2,500</td>
					<td>32,500</td>
					<td>신용카드</td>
					<td>입금대기</td>
					<td>Item준비중</td>
					<td>취소신청</td>
					<td>환불신청</td>
					<td>교환요청</td>
				</tr>
				<tr>
					<td colspan="15">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">선택 주문 엑셀 다운로드</a>
			<a href="" class="btn1 fl_l">전체 주문 엑셀 다운로드</a>
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