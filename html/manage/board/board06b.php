<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[Craft Shop 대화]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; Craft Shop 대화</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>수신자</th>
					<td>
						<input type="text" id="" class="inp_sty40" value="Craft Shop명 (shop 코드)" />
						<a href="" class="btn2">찾아보기</a>
						<p class="ex">* 수신자는 1회에 최대 10명까지 지정 가능합니다</p><br />
						<label><input type="checkbox" class="inp_check" />전체 Craft Shop</label>
					</td>
				</tr>
				<tr>
					<th>내용</th>
					<td>
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
				</tr>
				<tr>
					<th>이미지첨부</th>
					<td><input type="file" class="inp_file mg_t10" /></td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">취소</a>
			<a href="" class="btn3">보내기</a>
		</div>

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>