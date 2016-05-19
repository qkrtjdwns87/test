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
							<option value="" selected="selected">서비스 이용약관</option>
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>제목</th>
					<td colspan="3"><input type="text" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>작성자</th>
					<td>홍길동</td>
					<th>등록일자</th>
					<td>2016-01-12 11:11</td>
				</tr>
				<tr>
					<th>시행일</th>
					<td colspan="3"><input type="text" class="inp_sty20" /><a href="" class="calendar"></a></td>
				</tr>
				<tr>
					<th>내용</th>
					<td colspan="3"><textarea class="textarea1" rows="5" cols="5"></textarea></td>
				</tr>
				<tr>
					<th>사용여부</th>
					<td colspan="3">사용중</td>
				</tr>
			</tbody>
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