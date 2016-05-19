<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[공지사항]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 공지사항</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col width="24%" /><col width="10%" /><col width="23%" /><col width="10%" /><col width="23%" /></colgroup>
			<tr>
				<th>제목</th>
				<td colspan="5"><input type="text" id="" class="inp_sty90" /></td>
			</tr>
			<tr>
				<th>작성자</th>
				<td>홍길동</td>
				<th>등록일시</th>
				<td>2016-01-20 12:34</td>
				<th>조회수</th>
				<td>54</td>
			</tr>
			<tr>
				<th>내용</th>
				<td colspan="5"><textarea id="" rows="5" cols="5" class="textarea1"></textarea></td>
			</tr>
		</table>
		<div class="btn_list">
			<a href="" class="btn3">저장</a>
			<a href="" class="btn1">삭제</a>
			<a href="" class="btn1">목록</a>
		</div>

		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<tr>
				<th>제목</th>
				<td colspan="5"><input type="text" id="" class="inp_sty90" /></td>
			</tr>
			<tr>
				<th>내용</th>
				<td colspan="5"><textarea id="" rows="5" cols="5" class="textarea1"></textarea></td>
			</tr>
		</table>
		<div class="btn_list">
			<a href="" class="btn1">취소</a>
			<a href="" class="btn3">등록</a>
		</div>
		
	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>