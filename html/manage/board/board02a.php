<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[새소식]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 새소식</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="19%" /><col width="15%" /><col width="18%" /><col width="15%" /><col width="18%" /></colgroup>
			<tbody>
				<tr>
					<th>제목</th>
					<td colspan="5"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>작성자</th>
					<td>홍길동</td>
					<th>등록일시</th>
					<td>2016-01-20 12:11</td>
					<th>조회수</th>
					<td>54</td>
				</tr>
				<tr>
					<th>내용</th>
					<td colspan="5" class="ag_l">
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
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