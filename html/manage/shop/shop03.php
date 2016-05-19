<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[신규신청]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 신규신청</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>판매자 구분</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>개인판매자</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>개인사업자</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>법인사업자</span></label>
					</td>
				</tr>
				<tr>
					<th>Shop 명</th>
					<td colspan="3"><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>작가</th>
					<td colspan="3"><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>계정신청 (이메일)</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>작가 이메일</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>작가 휴대폰 번호</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>CIRCUS 관리 담당자</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <a href="" class="btn1">찾아보기</a></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">사업자 정보</th>
					<th class="ag_r">
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>사업자 번호</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시)111-11-11111</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자 형태</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>개인판매자</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>개인사업자</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>법인사업자</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th><span class="important">*</span>대표자명</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th><span class="important">*</span>종목</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 전화</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>사업장 소재지</th>
					<td colspan="3"><input type="text" id="" class="inp_sty10" /><a href="" class="btn1">우편번호 찾기</a></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="" class="inp_sty80" /></td>
				</tr>
				<tr>
					<th>통신판매업 번호</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">다음</a>
		</div>
		
	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>