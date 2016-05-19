<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[취소관리]</h2>
			<div class="location">Home &gt; 주문관리 &gt; 취소관리</div>
		</div>
		

		<table class="write2">
			<colgroup><col width="25%" /><col width="25%" /><col width="25%" /><col width="25%" /></colgroup>
			<thead>
				<tr>
					<th>오늘 신규 취소신청</th>
					<th>취소신청</th>
					<th>취소불가</th>
					<th>취소완료</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href=""><span class="blue bold">6</span>건</a></td>
					<td><a href=""><span class="blue bold">1</span>건</a></td>
					<td><a href=""><span class="blue bold">2</span>건</a></td>
					<td><a href=""><span class="blue bold">1</span>건 </a></td>
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
					<th>주문상태</th>
					<td colspan="3">
						<label><input type="checkbox" id="" name="" class="inp_check" />전체</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />취소신청</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />취소불가</label>
						<label><input type="checkbox" id="" name="" class="inp_check" />취소완료</label>
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
			<colgroup><col width="5%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="7%" /><col width="11%" /><col width="7%" /><col width="7%" /></colgroup>
			<thead>
				<tr>
					<th></th>
					<th>주문번호</th>
					<th>주문일시</th>
					<th>주문Item</th>
					<th>Craft Shop(코드)</th>
					<th>주문자(연락처)</th>
					<th>구매금액(원)</th>
					<th>배송비(원)</th>
					<th>총 실결제금액(원)</th>
					<th>결제수단</th>
					<th>주문상태</th>
					<th>취소사유</th>
					<th>취소신청일</th>
					<th>취소완료/불가처리일</th>
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
					<td>취소신청</td>
					<td>소비자변심</td>
					<td>2016-01-20</td>
					<td>2016-01-23</td>
				</tr>
				<tr>
					<td colspan="14">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">선택 주문 엑셀 다운로드</a>
			<a href="" class="btn1 fl_l">전체 주문 엑셀 다운로드</a>

			<span class="tdline">선택한 주문</span>
			<a href="" class="btn1 fl_r">환불승인 처리</a>
			<a href="" class="btn1 fl_r">취소불가 처리</a>
			<a href="" class="btn1 fl_r">취소완료 처리</a>
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

		<div class="help_tip">
			<dl>
				<dt><img src="/images/adm/icn_q1.png" alt="느낌표" class="icn_q1" />도움말</dt>
				<dd>- '입금전 취소' 된 주문과 배송전에 '취소'된 주문 내역입니다.</dd>
				<dd>- 구매자가 취소신청한 주문을 취소불가 또는 취소완료 처리할 수 있습니다.  </dd>
				<dd>- 구매취소는 주문한 전체상품에 대한 취소만 가능합니다.</dd>
				<dd>- 취소불가된 주문 건은 주문상세정보 팝업에서 주문상태를 수정해 주십시오.</dd>
				<dd>- 취소완료 처리 후 카드 및 휴대폰결제 취소는 별도로 처리해 주셔야 합니다.</dd>
				<dd>- 무통장입금 확인 후에는 취소완료처리 후 환불승인 처리를 해 주신 후 환불을 해 주십시오.</dd>
				<dd>- 환불승인 처리된 주문 건은 ‘환불관리’ 목록에서 확인하실 수 있습니다.</dd>
			</dl>
		</div>

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>







</body>
</html>