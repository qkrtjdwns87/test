<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[Story]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; Story</div>
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
					<th>PC웹에서 공유</th>
					<td colspan="5">카카오톡<span>0</span> / 페이스북<span>0</span> / 트위터<span>0</span> / 인스타그램<span>0</span> / 카카오 스토리<span>0</span> / 라인<span>0</span></td>
				</tr>
				<tr>
					<th>모바일앱에서 공유</th>
					<td colspan="5">카카오톡<span>0</span> / 페이스북<span>0</span> / 트위터<span>0</span> / 인스타그램<span>0</span> / 카카오 스토리<span>0</span> / 라인<span>0</span></td>
				</tr>
			</tbody>
		</table>

		<table class="write2 mg_t10">
			<thead>
				<tr>
					<th>PC웹 상세내용</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<textarea id="" rows="5" cols="5" class="textarea1"></textarea>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write2 mg_t10">
			<colgroup><col width="15%" /><col width="75%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">모바일앱 상세내용</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>스타일선택<br /><span class="red">(이미지 가로 OOO pixel)</span></th>
					<td class="ag_l">
						<ul class="board_img_radio">
							<li>
								<label><input type="radio" id="" class="inp_radio" /><img src="/images/adm/board_01.png" alt="이미지전체" /></label>
								<label><input type="radio" id="" class="inp_radio" /><img src="/images/adm/board_02.png" alt="상단이미지 하단텍스트" /></label>
								<label><input type="radio" id="" class="inp_radio" /><img src="/images/adm/board_03.png" alt="텍스트전체" /></label>
								<label><input type="radio" id="" class="inp_radio" /><img src="/images/adm/board_04.png" alt="관련 Craft Shop" /></label>
							</li>
						</ul>
					</td>
					<td rowspan="3">
						<a href="" class="btn2 mg_b10">추가</a>
						<a href="" class="btn2">삭제</a>
					</td>
				</tr>
				<tr>
					<th rowspan="2">내용</th>
					<td class="ag_l">
						<a href="" class="alink">1233-small.jpg</a> <a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기"></a><br />
						<input type="file" class="inp_file mg_t10" />
					</td>
				</tr>
				<tr>
					<td class="ag_l">
						<p class="mg_b10"><span class="red">18</span>/1,000(한글기준 500자 제한)</p>
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