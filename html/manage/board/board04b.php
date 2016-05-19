<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[FAQ]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; FAQ</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th>분류</th>
					<td>
						<select id="" class="inp_select">
							<option value="" selected="selected">분류선택</option>
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>제목</th>
					<td><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>내용</th>
					<td class="ag_l">
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
				</tr>
			</tbody>
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