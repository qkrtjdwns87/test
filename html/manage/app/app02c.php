<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[푸시관리]</h2>
			<div class="location">Home &gt; 앱관리 &gt; 푸시관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%"/></colgroup>
			<body>
				<tr>
					<th rowspan="2">상태</th>
					<td>
						<label><input type="radio" class="inp_radio" />즉시발송</label>
						<label><input type="radio" class="inp_radio" />발송예정</label>
					</td>
				<tr>
					<td class="bo_tn">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar mg_r10"></a>
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
					<th>유형</th>
					<td>
						<label><input type="radio" class="inp_radio" />일반푸시</label>
						<label><input type="radio" class="inp_radio" />리치푸시</label>
					</td>
				</tr>
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
					<td><input type="text" id="" class="inp_sty40" /></td>
				</tr>
		
				<tr>
					<th>내용</th>
					<td>
						<input type="text" id="" class="inp_sty40" /> 
						<span class="tdline">( <span class="red">90</span> / 120byte )</span>
					</td>
				</tr>
				<tr>
					<th>딥링크 URL</th>
					<td><input type="text" id="" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th rowspan="2">발송대상</th>
					<td>
						<label><input type="radio" class="inp_radio" />조건선택</label>
						<label><input type="radio" class="inp_radio" />파일업로드</label>
					</td>
				</tr>
				<tr>
					<!-- 파일업로드시 -->
					<td >
						<span><em class="alink">1233-small.jpg</em> <a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a></span>
						<div class="search_box"><input type="file" name="" id="" value="파일찾기" /></div>
					</td>

					<!-- 조건선택시 -->
					<td style="display:none">
						<ul class="choice_type1">
							<li>
								<span class="bold">OS</span>
								<label><input type="radio" class="inp_radio" />전체</label>
								<label><input type="radio" class="inp_radio" />Android</label>
								<label><input type="radio" class="inp_radio" />ios</label>
							</li>
							<li>
								<span class="bold">성별</span>
								<label><input type="radio" class="inp_radio" />전체</label>
								<label><input type="radio" class="inp_radio" />남성</label>
								<label><input type="radio" class="inp_radio" />여성</label>
							</li>
							<li>
								<span class="bold">연령대</span>
								<label><input type="checkbox" class="inp_check" />전체</label>
								<label><input type="checkbox" class="inp_check" />10대</label>
								<label><input type="checkbox" class="inp_check" />20대</label>
								<label><input type="checkbox" class="inp_check" />30대</label>
								<label><input type="checkbox" class="inp_check" />40대</label>
								<label><input type="checkbox" class="inp_check" />50대이상</label>
							</li>
						</ul>
					</td>
				</tr>

			</body>
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