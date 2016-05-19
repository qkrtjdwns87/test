<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[공지팝업관리]</h2>
			<div class="location">Home &gt; 앱관리 &gt; 공지팝업관리</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="10%"/><col width="40%"/><col width="10%"/><col width="40%"/></colgroup>
			<body>
				<tr>
					<th rowspan="2"><span class="important">*</span>구분</th>
					<td rowspan="2">
						<label><input type="radio" class="inp_radio" />일반공지</label>
						<label><input type="radio" class="inp_radio" />긴급공지</label>
					</td>
					<th rowspan="2"><span class="important">*</span>공지일시</th>
					<td>
						<label><input type="radio" class="inp_radio" />즉시공지</label>
						<label><input type="radio" class="inp_radio" />공지예정</label>
					</td>
				</tr>
				<tr>
					<td class="bo_tn">
						<input type="text" id="" class="inp_sty20" /><a href="" class="calendar mg_r10"></a>
						<select id="" class="inp_select">
							<option value="" selected="selected">시</option>
							<option value=""></option>
						</select><span class="tdline">시</span>
						<select id="" class="inp_select">
							<option value="" selected="selected">분</option>
							<option value=""></option>
						</select><span class="tdline">분</span>
					</td>
				</tr>

				<tr>
					<th><span class="important">*</span>제목</th>
					<td colspan="3"><input type="text" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>작성자</th>
					<td>홍길동</td>
					<th>동륵일시</th>
					<td>2016-01-20 11:24</td>
				</tr>
				<tr>
					<th><span class="important">*</span>내용</th>
					<td colspan="3">
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
				</tr>
			</body>
		</table>

		<div class="btn_list">
			<a href="" class="btn3">저장</a>
			<a href="" class="btn1">삭제</a>
			<a href="" class="btn1">목록</a>
		</div>
		

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>