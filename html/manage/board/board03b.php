<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[이벤트]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 이벤트</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th>진행상태</th>
					<td>진행중</td>
				</tr>
				<tr>
					<th>제목</th>
					<td><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>이벤트 기간</th>
					<td>
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<label><input type="checkbox" class="inp_check" />상시</label>
					</td>
				</tr>
				<tr>
					<th>이벤트 개요</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>상세내용01<br />(PC웹용)</th>
					<td class="ag_l">
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
				</tr>
				<tr>
					<th>상세내용02<br />(모바일용)</th>
					<td class="ag_l">
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
				</tr>
				<tr>
					<th>섬네일 이미지<br />(PC 웹용)<br /><span class="red">(000*000)</span></th>
					<td><input type="file" class="inp_file mg_t10" /></td>
				</tr>
				<tr>
					<th>게시여부</th>
					<td>
						<label><input type="radio" class="inp_radio" />게시</label>
						<label><input type="radio" class="inp_radio" />미게시</label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">취소</a>
			<a href="" class="btn1">등록</a>
		</div>

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>