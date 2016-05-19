<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[댓글관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 댓글관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>Craft Shop</th>
					<td>
						<input type="text" id="" class="inp_sty40" value="Craft Shop (Shop 코드)" />
						<a href="" class="btn2">찾아보기</a>
					</td>
				</tr>
				<tr>
					<th>Item</th>
					<td>
						<input type="text" id="" class="inp_sty40" value="Item 이름 (Item 코드)" />
						<a href="" class="btn2">찾아보기</a>
					</td>
				</tr>
				<tr>
					<th>검색어</th>
					<td>
						<select id="" class="inp_select">
							<option value="" selected="selected">제목</option>
							<option value="">내용</option>
						</select><input type="text" class="mg_l10 inp_sty40" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<div class="sub_title">총 1개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="40%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" class="inp_check" /></th>
					<th>No</th>
					<th>등록일</th>
					<th>내용</th>
					<th>작성자</th>
					<th>IP</th>
					<th>댓글이 달린<br />Item/Craft Shop</th>
					<th>처리</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" class="inp_check" /></td>
					<td>101</td>
					<td>2016-01-20</td>
					<td class="ag_l">포장시까지 정성이 느껴져서 좋았어요.</td>
					<td><a href="" class="alink">홍길동</a></td>
					<td>101.241.50.481</td>
					<td class="ag_l"><a href="" class="alink">크리스마스 한정 클러치</a>/BLACK SHARK</td>
					<td>
						<a href="" class="btn2">스팸</a>
						<a href="" class="btn2">삭제</a>
					</td>
				</tr>
				<tr>
					<td colspan="8">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다.</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">선택삭제</a>
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