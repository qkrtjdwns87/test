<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[관리자관리]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 관리자관리</div>
		</div>
		
		<div class="sub_title">
			<span class="important">*</span>은 필수 입력사항입니다.
		</div>

		<table class="write1">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th><span class="important">*</span>관리자구분</th>
					<td>
						<label><input type="radio" class="inp_radio" />슈퍼마스터</label>
						<label><input type="radio" class="inp_radio" />마스터</label>
						<label><input type="radio" class="inp_radio" />Craft Shop 관리담당자</label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>계정(이메일)</th>
					<td>
						<input type="text" class="inp_sty40" /><a href="" class="btn2">중복검사</a>
						<span class="ex tdline">* 예시) abc@abc.co.kr</span>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호</th>
					<td><input type="password" class="inp_sty20" maxlength="12" /> <span class="tdline ex">*영문+숫자의 조합으로 8~12자로 입력</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호 확인</th>
					<td><input type="password" class="inp_sty20" maxlength="12" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>이름</th>
					<td><input type="text" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>소속</th>
					<td><input type="text" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>휴대폰<br /><span class="ex tdline">(1개 이상 필수입력)</span></th>
					<td>
						<span class="tdline">사무실</span>
						<select id="" class="inp_select">
							<option value="" selected="selected">02</option>
							<option value=""></option>
						</select><span class="tdline">-</span>
						<input type="text" class="inp_sty5" maxlength="4" /><span class="tdline">-</span><input type="text" class="inp_sty5" maxlength="4" />
						<span class="mg_l10">내선 <input type="text" class="inp_sty5" maxlength="4" /></span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="tdline">휴대폰</span>
						<select id="" class="inp_select">
							<option value="" selected="selected">010</option>
							<option value=""></option>
						</select><span class="tdline">-</span>
						<input type="text" class="inp_sty5" maxlength="4" /><span class="tdline">-</span><input type="text" class="inp_sty5" maxlength="4" />
					</td>
				</tr>
				<tr>
					<th>메모</th>
					<td>
						<textarea id="" rows="5" cols="5" class="textarea1">최대 200자</textarea>
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