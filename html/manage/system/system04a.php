<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[베스트셀러]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 노출순서관리 &gt; 베스트셀러</div>
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>Craft Shop</th>
					<td class="ag_l"><input type="text" id="" class="inp_sty30" value="Cfrat Shop명 (shop 코드)" /><a href="" class="btn2">찾아보기</a></td>
				</tr>
				<tr>
					<th>Item</th>
					<td class="ag_l"><input type="text" id="" class="inp_sty30" value="Item 이름 (Item 코드)" /><a href="" class="btn2">찾아보기</a></td>
				</tr>
				<tr>
					<th>검색어</th>
					<td class="ag_l">
						<select id="" class="inp_select">
							<option value="" selected="selected">제목</option>
							<option value="">내용</option>
						</select>
						<input type="text" id="" class="inp_sty50"  />
					</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn3">검색</a>
		</div>

		<div class="btn_list ag_l ov_fl">
			<span class="tdline">체크한 Item을 </span>
			<a href="" class="btn1">맨 위로</a> 
			<a href="" class="btn1">위로</a> 
			<a href="" class="btn1">아래로</a>
			<a href="" class="btn1">맨 끝으로</a>
			<span class="tdline">이동</span>

			<a href="" class="btn1 fl_r">판매순으로 정렬</a>
		</div>

		<div class="sub_title"><span class="fl_l">총 101개</span><span class="fl_r color_day">2016-01-10 12:30 현재</span></div>
	
		<table class="write2 cboth">
			<colgroup><col width="5%" /><col width="10%" /><col width="35%" /><col width="15%" /><col width="10%" /><col width="10%" /><col width="15%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" class="inp_check" /></th>
					<th>Item 코드</th>
					<th>Item 이름</th>
					<th>Craft Shop</th>
					<th>작가</th>
					<th>승인일</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" class="inp_check" /></td>
					<td>AC1202456</td>
					<td class="ag_l"><a href="" class="alink">크리스마스한정 블랙 클러치</a></td>
					<td>poff</td>
					<td>문소리</td>
					<td>2016-01-20</td>
					<td>
						<a href="#"><img src="/images/adm/btn_up.gif" alt="위" /></a>
						<a href="#"><img src="/images/adm/btn_down.gif" alt="아래" /></a>
					</td>
				</tr>
				<tr>
					<td colspan="7">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			</tbody>
		</table>

		<!-- paging -->
		<div class="pagination">
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

		<div class="btn_list">
			<a href="" class="btn1">이전으로 되돌리기</a>
			<a href="" class="btn3">저장</a>
		</div>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>