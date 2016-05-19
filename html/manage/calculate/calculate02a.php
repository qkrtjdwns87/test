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
			<thead>
				<tr>
					<th colspan="4">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Shop 코드</th>
					<td>AC123456789</td>
					<th>Shop 명</th>
					<td>POFF</td>
				</tr>
				<tr>
					<th>Shop 상태</th>
					<td colspan="3">운영중</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">정산기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>정산일</th>
					<td>2015-01-11</td>
					<th>정산기간</th>
					<td>2015-01-01~2015-02-02</td>
				</tr>
				<tr>
					<th>정산주기</th>
					<td>월정산</td>
					<th>입금계좌</th>
					<td>국민은행 123456789 <br />(예금주:poff)</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">정산상세정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 결제금액</th>
					<td>2,000,500원</td>
					<th>수수료 합계</th>
					<td>200,050원</td>
				</tr>
				<tr>
					<th>정산가감액</th>
					<td>0원</td>
					<th>정산금액</th>
					<td class="red"><strong>1,800,000원</strong></td>
				</tr>
				<tr>
					<th>정산상태</th>
					<td colspan="3">승인완료 <span class="ex">* 정산상태의 변경은 목록에서 가능합니다.</span></td>
				</tr>
			</tbody>
		</table>
		
		<div class="sub_title">총 101건</div>
		<table class="write2">
			<colgroup><col width="15%" /></colgroup>
			<thead>
				<tr>
					<th>주문번호</th>
					<th>주문 Item</th>
					<th>Craft Shop(코드)</th>
					<th>주문자(연락처)</th>
					<th>총 결제 금액(원)</th>
					<th>결제 수단</th>
					<th>결제 금액<br />결제 예정일</th>
					<th>결제 금액<br />정산 완료일</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>201512312</td>
					<td>크리스마스한정 블랙 클러치외 2개</td>
					<td>poff(AC457896)</td>
					<td>문소리(010-1234-5678)</td>
					<td>32,000</td>
					<td>신용카드</td>
					<td>2016-01-10</td>
					<td>2016-02-10</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1 fl_l">엑셀다운로드</a>
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