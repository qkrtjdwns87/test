<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		
	<div class="title">
			<h2>[전체 주문현황]</h2>
			<div class="location">Home &gt; 주문관리 &gt; 전체 주문현황</div>
		</div>
		
		<p>td에 ul.list 삽입</p>
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
					<td><input type="checkbox" id="" class="inp_check" /></td>
					<td><a href="" class="alink">201512312</a></td>
					<td>2016-01-20 11:22</td>
					<td>
						<ul class="list">
							<li><a href="" class="alink">크리스마스 한정블랙 클러치외 2개</a></li>
							<li><a href="" class="alink">크리스마스 한정블랙 클러치외 3개</a></li>
							<li><a href="" class="alink">크리스마스 한정블랙 클러치외 4개</a></li>
							<li><a href="" class="alink">크리스마스 한정블랙 클러치외 5개</a></li>
							<li><a href="" class="alink">크리스마스 한정블랙 클러치외 6개</a></li>
							<li><a href="" class="alink">크리스마스 한정블랙 클러치외 7개</a></li>
						</ul>
					</td>
					<td>
						<ul class="list">
							<li>poff<br />(AC457896)</li>
							<li>abc<br />(DC123456)</li>
							<li>qwe<br />(EC987654)</li>
						</ul>
					</td>
					<td>문소리<br />(010-1234-5678)</td>
					<td>
						<ul class="list">
							<li>30,000</li>
							<li>40,000</li>
							<li>45,000</li>
							<li>15,000</li>
							<li>5,000</li>
							<li>20,000</li>
						</ul>
					</td>
					<td>2,500</td>
					<td>32,500</td>
					<td>신용카드</td>
					<td>입금대기</td>
					<td>Item준비중</td>
					<td>취소신청</td>
					<td>환불신청</td>
					<td>교환요청</td>
				</tr>
			</tbody>
		</table>

		

<br /><br /><br /><br /><br /><br />
		<p>td에 .bd_n 삽입</p>
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
					<td rowspan="4"><input type="checkbox" id="" class="inp_check" /></td>
					<td rowspan="4"><a href="" class="alink">201512312</a></td>
					<td rowspan="4">2016-01-20 11:22</td>
					<td class="bd_n"><a href="" class="alink">크리스마스 한정블랙 클러치외 2개</a></td>
					<td rowspan="4">poff<br />(AC457896)</td>
					<td rowspan="4">문소리<br />(010-1234-5678)</td>
					<td rowspan="4">30,000</td>
					<td rowspan="4">2,500</td>
					<td rowspan="4">32,500</td>
					<td rowspan="4">신용카드</td>
					<td rowspan="4">입금대기</td>
					<td rowspan="4">Item준비중</td>
					<td rowspan="4">취소신청</td>
					<td rowspan="4">환불신청</td>
					<td rowspan="4">교환요청</td>
				</tr>
				<tr>
					<td class="bd_n"><a href="" class="alink">감귤쥬스</a></td>
				</tr>
				<tr>
					<td class="bd_n"><a href="" class="alink">오렌지쥬스</a></td>
				</tr>
				<tr>
					<td class="bd_n"><a href="" class="alink">포도쥬스</a></td>
				</tr>
			</tbody>
		</table>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>