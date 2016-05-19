<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[버전관리]</h2>
			<div class="location">Home &gt; 앱관리 &gt; 버전관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%"/></colgroup>
			<tbody>
				<tr>
					<th>OS별</th>
					<td>
						<label><input type="radio" class="inp_radio" />전체</label>
						<label><input type="radio" class="inp_radio" />Android</label>
						<label><input type="radio" class="inp_radio" />ios</label>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 101개</div>
		<table class="write2">
			<colgroup><col width="16%" /><col width="16%" /><col width="20%" /><col width="16%" /><col width="16%" /><col width="16%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>OS</th>
					<th>업데이트 대상 마켓</th>
					<th>유형</th>
					<th>업데이트 버전명</th>
					<th>등록일</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>101</td>
					<td>Android</td>
					<td>play 스토어</td>
					<td>일반</td>
					<td>V1.01.01</td>
					<td>2016-05-10</td>
				</tr>
				<tr>
					<td>100</td>
					<td>ios</td>
					<td>App Store</td>
					<td>강제</td>
					<td>V2.00.00</td>
					<td>2016-05-10</td>
				</tr>
				<tr>
					<td colspan="6">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">신규등록</a>
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