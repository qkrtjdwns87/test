<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[Craft Shop별 매출통계]</h2>
			<div class="location">Home &gt; 통계/랭킹 &gt; 매출통계 &gt; Craft Shop별 매출통계</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>Craft Shop</th>
					<!-- 검색전 -->
					<td style="display:none;">
						<input type="text" id="" class="inp_sty40" value="Craft Shop명 (Craft Shop코드)" /><a href="" class="btn1">찾아보기</a>
					</td>

					<!-- 검색후 -->
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">poff</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
							</dd>
						</dl>
						<p class="mg_t10"><input type="text" id="" class="inp_sty40" value="Craft Shop명 (Craft Shop코드)" /><a href="" class="btn1">찾아보기</a></p>
					</td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td>
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
		
		<div class="sub_title">
			<span class="bold fl_l">2016-01-11 ~ 2016-02-11</span>
			<span class="fl_r">(금액단위: 원)</span>
		</div>

		<table class="write2 cboth">
			<colgroup><col width="17" /><col width="16" /><col width="17" /><col width="17" /><col width="17" /><col width="16" /></colgroup>
			<thead>
				<tr>
					<th rowspan="2">매출일자</th>
					<th rowspan="2">신규주문건수</th>
					<th rowspan="2">실 결제금액</th>
					<th colspan="3">결제수단별 주문건수/실 결제금액</th>
				</tr>
				<tr>
					<th>카드<br />(건수/실결제금액</th>
					<th>무통장입금<br />(건수/실결제금액</th>
					<th>휴대폰결제<br />(건수/실결제금액</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>2016-01-20</td>
					<td>1,196</td>
					<td>1,452,000</td>
					<td>100 / 1,000,000</td>
					<td>200 / 500,000</td>
					<td>30 / 20,000</td>
				</tr>
				<tr>
					<td colspan="6">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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