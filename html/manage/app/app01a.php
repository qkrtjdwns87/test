<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[버전관리]</h2>
			<div class="location">Home &gt; 앱관리 &gt; 버전관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%"/></colgroup>
			<body>
				<tr>
					<th>OS</th>
					<td>
						<label><input type="radio" class="inp_radio" />Android</label>
						<label><input type="radio" class="inp_radio" />ios</label>
					</td>
				</tr>
				<tr>
					<th>마켓</th>
					<td>
						<select id="" class="inp_select">
							<option value="" selected="selected">Play 스토어</option>
							<option value=""></option>
						</select>
					</td>
				</tr>
				<tr>
					<th>버전</th>
					<td><input type="text" class="inp_sty20" /></td>
				</tr>
				<tr>
					<th>등록일시</th>
					<td>2016-01-20 11:24</td>
				</tr>
				<tr>
					<th>내용</th>
					<td><textarea id="" rows="5" cols="5" class="textarea1"></textarea></td>
				</tr>
			</body>
		</table>

		<div class="btn_list">
			<a href="" class="btn3">저장</a>
			<a href="" class="btn1">목록</a>
		</div>
		

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>