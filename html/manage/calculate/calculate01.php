<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[일별매출 내역]</h2>
			<div class="location">Home &gt; 정산관리 &gt; 일별매출 내역</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th>기간선택</th>
					<td>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>Craft Shop</th>
					<td><input type="text" id="" class="inp_sty20" /><a href="" class="btn1">찾아보기</a></td>
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
			<colgroup><col width="12.5%" /></colgroup>
			<thead>
				<tr>
					<th rowspan="2">일자</th>
					<th colspan="5">결제완료 주문</th>
					<th rowspan="2">환불합계<a href="#" class="tooltip" data-tooltip="- 환불합계 tooltip"><img src="/images/adm/icn_q.png" alt="물음표" class="icn_q" /></a></th>
					<th rowspan="2">순매출<a href="#" class="tooltip" data-tooltip="- 순매출 tooltip"><img src="/images/adm/icn_q.png" alt="물음표" class="icn_q" /></a></th>
				</tr>
				<tr>
					<th>주문수</th>
					<th>Item 수</th>
					<th>구매금액</th>
					<th>배송비</th>
					<th>결제금액<a href="#" class="tooltip" data-tooltip="- 결제금액 tooltip"><img src="/images/adm/icn_q.png" alt="물음표" class="icn_q" /></a></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>2016-01-01</td>
					<td>6</td>
					<td>10</td>
					<td>205,000</td>
					<td>2,500</td>
					<td>207,500</td>
					<td>0</td>
					<td>207,500</td>
				</tr>
				<tr>
					<td>2016-01-01</td>
					<td>6</td>
					<td>10</td>
					<td>205,000</td>
					<td>2,500</td>
					<td>207,500</td>
					<td>0</td>
					<td>207,500</td>
				</tr>
				
				<tr>
					<td colspan="8">매출 내역이 없습니다.</td>
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
		
		<div class="help_tip">
			<dl>
				<dt><img src="/images/adm/icn_q1.png" alt="느낌표" class="icn_q1" />도움말</dt>
				<dd>- 본 자료는 전반적인 영업현황을 기술적으로 나타내는 것으로, 통계 데이터의 집계에는 일부 지연, 누락 또는 오차가 발생할 수 있습니다. 본 자료는 참고용이며, 그 외의 용도로 사용할 수 없습니다.</dd>
				<dd>- 미입금상태 전환, 환불철회 등으로 인해 오차가 발생할 수 있습니다</dd>
				<dd>- 매출내역은 배송완료일을 기준으로 산정됩니다.</dd>
				<dd>- 00시 이후 전일 데이터의 보정 작업이 매일 진행됩니다.</dd>
				<dd>- 결제금액은 카드 및 휴대폰결제완료, 입금확인이 된 내역입니다.</dd>
				<dd>- 환불합계는 커드 및 휴대폰결제 취소완료, 환불액 입금완료된 내역입니다.</dd>
			</dl>
		</div>

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>







</body>
</html>