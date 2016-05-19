<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[기획전 관리]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 기획전 관리</div>
		</div>
		

		<table class="write1">
			<colgroup><col width="15%" /><col width="85%" /></colgroup>
			<tbody>
				<tr>
					<th>제목</th>
					<td><input type="text" id="" class="inp_sty60" /></td>
				</tr>
				<tr>
					<th>진행기간</th>
					<td>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
					</td>
				</tr>
				<tr>
					<th class="ag_c va_m"><div>썸네일 이미지01<br /> PC 웹용</div><span class="red">(000 x 000)</span></th>
					<td>
						<input type="file" name="" class="inp_file" value="찾아보기">
					</td>
				</tr>
				<tr>
					<th class="ag_c va_m"><div>기획전 상단<br /> 꾸미기 이미지<br /> PC 웹용</div><span class="red">(000 x 000)</span></th>
					<td>
						<input type="file" name="" class="inp_file" value="찾아보기">
					</td>
				</tr>
				<tr>
					<th class="ag_c va_m"><div>썸네일 이미지02<br /> 모바일앱용</div><span class="red">(000 x 000)</span></th>
					<td>
						<input type="file" name="" class="inp_file" value="찾아보기">
					</td>
				</tr>
				<tr>
					<th>게시여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>게시</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>미게시</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="mg_t20 mg_b10">
			<a href="" class="btn1">신상품순으로 정렬</a>
			<a href="" class="btn1">판매량순으로 정렬</a>
			<a href="" class="btn1">Flag건순으로 정렬</a>
			<span class="ex">* 광고 Item은 노출순서 지정과 상관없이 최상단에 노출됩니다.</span>
		</div>

		<table class="write2">
			<colgroup><col width="10%" /><col width="80%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>Item</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
				<!-- 1 -->
				<tr>
					<td>1</td>
					<td class="fl_n cboth ag_l">
						<div class="search_box"><input type="text" id="" class="inp_sty40" value="Item 이름 (Item 코드)"> <input type="file" name="" id="" value="찾아보기"></div>
					</td>
					<td>
						<a href="#"><img src="/images/adm/btn_up.gif" alt="위" /></a>
						<a href="#"><img src="/images/adm/btn_down.gif" alt="아래" /></a>
					</td>
				</tr>
				<!-- //1 -->
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">미리보기</a>
			<a href="" class="btn1 fl_r">Item 추가</a>
		</div>

		<div class="btn_list cboth">
			<a href="" class="btn1">취소</a>
			<a href="" class="btn3">저장</a>
		</div>

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
	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>