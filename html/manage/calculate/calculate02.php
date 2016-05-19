<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[정산현황]</h2>
			<div class="location">Home &gt; 정산관리 &gt; 정산현황</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>정산기간</th>
					<td>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
					</td>
					<th>정산상태</th>
					<td>
						<label><input type="radio" id="" class="inp_radio" />전체</label>
						<label><input type="radio" id="" class="inp_radio" />승인완료</label>
						<label><input type="radio" id="" class="inp_radio" />승인요청</label>
						<label><input type="radio" id="" class="inp_radio" />승인거부</label>
					</td>
				</tr>
				<tr>
					<th>정산주기</th>
					<td>
						<label><input type="radio" id="" class="inp_radio" />전체</label>
						<label><input type="radio" id="" class="inp_radio" />월정산</label>
						<label><input type="radio" id="" class="inp_radio" />주정산</label>
						<label><input type="radio" id="" class="inp_radio" />일정산</label>
					</td>
					<th>Craft Shop</th>
					<td><input type="text" id="" class="inp_sty40" /><a href="" class="btn1">찾아보기</a></td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">
			<strong class="fl_l font15"><span class="blue">poff</span> Craft Shop</strong>
			<span class="fl_r">2016-01-10 11:30 현재</span>
		</div>

		<table class="write2" class="cboth">
			<colgroup><col width="5%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="5%" /></colgroup>
			<thead>
				<tr>
					<th rowspan="2"></th>
					<th rowspan="2">정산일</th>
					<th rowspan="2">Craft Shop<br />코드</th>
					<th colspan="3">정산정보</th>
					<th colspan="3">정산금액 상세</th>
					<th rowspan="2">정산금액</th>
					<th rowspan="2">정산상태</th>
				</tr>
				<tr>
					<th>정산기간</th>
					<th>정산주기</th>
					<th>입금계좌</th>
					<th>총 결제금액</th>
					<th>수수료 합계</th>
					<th>정산가감액</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td><a href="" class="alink">2015-11-11</a></td>
					<td>Roff(AC457896)</td>
					<td>2015-11-11</td>
					<td>월정산</td>
					<td>국민은행<br />12345678910<br />(예금주:poff)</td>
					<td>2,000,500</td>
					<td>207,500</td>
					<td>0</td>
					<td>1,800,000</td>
					<td>승인완료</td>
				</tr>
				
				<tr>
					<td colspan="11">정산 내역이 없습니다.</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">선택 내역 엑셀다운로드</a>
			<a href="" class="btn1 fl_l">전체 내역 엑셀다운로드</a>

			<a href="" class="btn1 fl_r">지급대기 처리</a>
			<a href="" class="btn1 fl_r">승인완료 처리</a>
			<a href="" class="btn1 fl_r">승인요청 처리</a>
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
				<dd>- 정산기간 중 배송완료 가 된 주문을 대상으로 합니다.</dd>
				<dd>- 정산일을 클릭하시면 상세내역을 확인하실 수 있습니다.</dd>
				<dd>- 정산은 배송완료 후 +2영업일에 정산됩니다.</dd>
				<dd>- 정산금액이 마이너스 금액이면, 입금 받으실 금액은 ‘0원이 되고, 정산하지 못한 금액은 익월정산에서 ‘정산가가감액’으로 처리됩니다.</dd>
				<dd>- Craft Shop에서 ‘정산완료’ 처리가 된 건은 ‘지급대기’ 목록에서 확인 가능합니다.</dd>
				<dd>- 배송완료일 기준으로 수수료의 세금계산서 내역이 생성됩니다.</dd>
			</dl>
		</div>

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>







</body>
</html>