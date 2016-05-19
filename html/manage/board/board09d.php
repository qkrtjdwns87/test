<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[약관관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 약관관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>구분</th>
					<td>
						<label><input type="radio" class="inp_radio" />필수</label>
						<label><input type="radio" class="inp_radio" />선택</label>
					</td>
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
					<td colspan="3"><input type="text" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>시행일</th>
					<td colspan="3"><input type="text" class="inp_sty20" /><a href="" class="calendar"></a></td>
				</tr>
				<tr>
					<th>내용</th>
					<td colspan="3"><textarea class="textarea1" rows="5" cols="5"></textarea></td>
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