<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[신규신청]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 신규신청</div>
		</div>
		
		<div class="sub_title">신규신청이 완료되어 ‘승인대기’ 상태로 전환되면 수정 및 추가를 하실 수 없습니다. <br />정확하게 입력되었는지 다시 한번 내용을 확인해 후 ‘승인요청＇을 진행해 주십시오.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>판매자 구분</th>
					<td colspan="3">개인판매자</td>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 명</th>
					<td colspan="3">ROFF</td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가</th>
					<td colspan="3">문소리</td>
				</tr>
				<tr>
					<th><span class="important">*</span>계정신청 (이메일)</th>
					<td colspan="3">abc@abc.co.kr</td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 이메일</th>
					<td colspan="3">abc@abc.co.kr</td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 휴대폰 번호</th>
					<td colspan="3">010-1234-5678</td>
				</tr>
				<tr>
					<th>CIRCUS 관리 담당자</th>
					<td colspan="3">홍길동 / 02-123-4567 / 010-1234-5678</td>
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
					<td colspan="3">111-11-11111</td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자 형태</th>
					<td colspan="3">개인사업자</td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td>ROFF</td>
					<th><span class="important">*</span>대표자명</th>
					<td>문소리</td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td>도소매, 서비스</td>
					<th><span class="important">*</span>종목</th>
					<td>전자부품</td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3">abc@abc.co.kr</td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 전화</th>
					<td colspan="3">010-1234-5678</td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>사업장 소재지</th>
					<td colspan="3">123-45</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">지번주소</td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">도로명주소</td>
				</tr>
				<tr>
					<th>통신판매업 번호</th>
					<td colspan="3">111-11-11111</td>
				</tr>
			</tbody>
		</table>
		<div class="btn_list">
			<a href="" class="btn1">입력정보수정</a>
		</div>

		<div class="shop_comment">
			<h3>[신규 Craft Shop 승인요청]</h3>
			<p>필요한 정보와 서류는 모두 입력 및 전달하셨나요? <br />모두 처리가 완료되었다면 승인요청 메시지와 함께 승인요청을 진행해 주십시오.</p>
			<p><textarea id="" rows="5" cols="5" class="textarea1"></textarea></p>
			
			<div class="btn_list">
				<a href="" class="btn1">승인요청</a>
			</div>
		</div>

	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>