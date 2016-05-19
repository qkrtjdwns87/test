<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 Item 현황]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 승인현황</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col width="10%" /><col width="28%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="5">Item 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Item 코드</th>
					<td colspan="4">AC1202456</td>
				</tr>
				<tr>
					<th><span class="important">*</span>Item 명</th>
					<td colspan="4"><input type="text" id="" class="inp_sty70" /></td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>Item 카테고리</th>
					<td class="bg_c1">CIRCUS</td>
					<td colspan="3">
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Accessories</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Bags &amp; Purses</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Beauty</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Clothing</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Home &amp; Living</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Jewelry</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Shoes</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Gift</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Designers</span></label>
					</td>
				</tr>
				<tr>
					<td class="bg_c1">Craft Shop</td>
					<td colspan="3">
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Silver925</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Fashion</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Ring</span></label>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>Earring</span></label>
					</td>
				</tr>
				<tr>
					<th>태그</th>
					<td colspan="4">
						<input type="text" id="" class="inp_sty70" />
						<ul class="mg_t10">
							<li class="lh_16">※ Item 검색에 활용할 단어를 등록해 주십시오.</li>
							<li class="lh_16">※ 콤마(,)로 구분해 주십시오.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th>독점판매 여부</th>
					<td colspan="2">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>YES</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>NO</span></label>
					</td>
					<th>촬영신청 여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>YES</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>NO</span></label>
					</td>
				</tr>
				<tr>
					<th>승인요청일시</th>
					<td colspan="2">YYYY-MM-DD HH:MM:SS</td>
					<th>최근 상태변경일시</th>
					<td>YYYY-MM-DD HH:MM:SS</td>
				</tr>
				<tr>
					<th>승인상태</th>
					<td colspan="4">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>승인요청</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>승인심사중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>승인거부</span></label>
						<a href="" class="btn2 va_m">진행내역 자세히 보기</a>
					</td>
				</tr>
				<tr>
					<th>거부사유</th>
					<td colspan="4">
						<textarea class="textarea1" maxlength="500">최대 500자 입력</textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">Item 상세정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>Item 옵션</th>
					<td colspan="3">
						<textarea class="textarea1">&bullet; 재질  : Sterig SILVER (92.5%) 
&bullet; 사이즈  : Free / Adjustable Small (9 ~ 13호)</textarea>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>Item 설명</th>
					<td colspan="3"><textarea class="textarea1">스털링실버에 황화칼륨(유화가리) 착색처리를 하게되면 시러 표면이 모두 검은 빛을 띄게 됩니다.그러한 실버의 표면을 고운 사포로 문질러주면 굴곡ㅇ 있는…</textarea></td>
				</tr>
				<tr>
					<th><span class="important">*</span>제작 및 예상도착일</th>
					<td colspan="3"><textarea class="textarea1">스털링실버에 황화칼륨(유화가리) 착색처리를 하게되면 시러 표면이 모두 검은 빛을 띄게 됩니다.그러한 실버의 표면을 고운 사포로 문질러주면 굴곡ㅇ 있는…</textarea></td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>교환 및 환불 정책</th>
					<td colspan="3">전체 Item 공통 정책 적용   /   Shop 자체 정책 사용</td>
				</tr>
				<tr>
					<td colspan="3"><textarea class="textarea1">스털링실버에 황화칼륨(유화가리) 착색처리를 하게되면 시러 표면이 모두 검은 빛을 띄게 됩니다.그러한 실버의 표면을 고운 사포로 문질러주면 굴곡ㅇ 있는…</textarea></td>
				</tr>
				<!-- 교환 및 환불 정책 ‘Item 개별 정책 적용 Shop일 경우 -->
				<tr>
					<th rowspan="3"><span class="important">*</span>교환 및 환불 정책</th>
					<td colspan="3">Item 개별 정책 적용</td>
				</tr>
				<tr>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>Shop 자체 정책 사용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>CIRCUS 기본정책 사용</span></label>
					</td>
				</tr>
				<tr>
					<td colspan="3"><textarea class="textarea1">스털링실버에 황화칼륨(유화가리) 착색처리를 하게되면 시러 표면이 모두 검은 빛을 띄게 됩니다.그러한 실버의 표면을 고운 사포로 문질러주면 굴곡ㅇ 있는…</textarea></td>
				</tr>
				<!-- //교환 및 환불 정책 ‘Item 개별 정책 적용 Shop일 경우 -->
			</tbody>
		</table>
		<table class="write1 mg_t10">
			<colgroup><col width="5%"><col width="10%"><col width="75%"><col width="10%"></colgroup>
			<thead>
				<tr>
					<th colspan="2">Item 이미지</th>
					<th colspan="2" class="ag_r">* 노출순서대로 차례로 등록해 주십시오. (<span class="red">최대 8</span>개)</th>
				</tr>
			</thead>
			<tbody>
				<!-- 1 -->
				<tr>
					<th rowspan="2" class="ag_c">1</th>
					<td class="ag_c va_m"><div>PC 웹용</div><span class="red">(000 x 000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">1233-small.jpg</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
							</dd>
							<dd><input type="file" name="" class="inp_file" value="파일찾기" /></dd>
						</dl>
					</td>
					<td class="ag_c va_m"><a href="" class="btn2">삭제</a></td>
				</tr>
				<tr>
					<td class="ag_c va_m"><div>모바일앱용</div><span class="red">(000 x 000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">1233-small.jpg</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
							</dd>
							<dd><input type="file" name="" class="inp_file" value="파일찾기" /></dd>
						</dl>
					</td>
					<td class="ag_c va_m"><a href="" class="btn2">삭제</a></td>
				</tr>
				<!-- //1 -->
				
			</tbody>
		</table>
		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="88%"></colgroup>
			<thead>
				<tr>
					<th colspan="2">판매정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>Item 판매단가</th>
					<td><input type="text" id="" class="inp_sty20 va_m" /> 원 <span class="dp1 mg_l20">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>할인여부 및<br /> 할인가격</th>
					<td>
						<label><input type="checkbox" id="" name="" class="inp_check" /><span>할인</span></label>
						<input type="text" id="" class="inp_sty20 va_m" /> 원 <span class="dp1 mg_l20">* 숫자만 입력</span>
					</td>
				</tr>
				<tr>
					<th>구매옵션</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용안함</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<!-- 옵션추가 -->
		<div class="mg_b10">
			<a href="" class="btn1 mg_t10">옵션추가</a>
			<div class=" fl_r mg_t10">※ 추가비용 0원으로 입력 시 표기되지 않습니다.</div>
			<!-- 1 -->
			<table class="write2 cboth mg_t10">
				<colgroup><col width="5%"><col width="20%"><col width="40%"><col width="20%"><col width="15%"></colgroup>
				<tbody>
					<tr>
						<th rowspan="3">1</th>
						<th>옵션명</th>
						<th>옵션구분 및 추가가격</th>
						<th>추가가격 (원)</th>
						<th><a href="" class="btn1">전체삭제</a></th>
					</tr>
					<tr>
						<td rowspan="2"><input type="text" id="" class="inp_sty80" /></td>
						<td><input type="text" id="" class="inp_sty50" /></td>
						<td><input type="text" id="" class="inp_sty60" /></td>
						<td><a href="" class="btn2">삭제</a></td>
					</tr>
					<tr>
						<td><input type="text" id="" class="inp_sty50" /></td>
						<td><input type="text" id="" class="inp_sty60" /></td>
						<td><a href="" class="btn2">삭제</a> <a href="" class="btn2">추가</a></td>
					</tr>
				</tbody>
			</table>
			<!-- //1 -->
			
			<!-- 2 -->
			<table class="write2 cboth mg_t10">
				<colgroup><col width="5%"><col width="20%"><col width="40%"><col width="20%"><col width="15%"></colgroup>
				<tbody>
					<tr>
						<th rowspan="2">2</th>
						<th>옵션명</th>
						<th>옵션구분 및 추가가격</th>
						<th>추가가격 (원)</th>
						<th><a href="" class="btn1">전체삭제</a></th>
					</tr>
					<tr>
						<td><input type="text" id="" class="inp_sty80" /></td>
						<td><input type="text" id="" class="inp_sty50" /></td>
						<td><input type="text" id="" class="inp_sty60" /></td>
						<td><a href="" class="btn2">추가</a></td>
					</tr>
				</tbody>
			</table>
			<!-- //2 -->
		</div>
		<!-- //옵션추가 -->

		<table class="write1">
			<colgroup><col width="12%"><col width="88%"></colgroup>
			<tbody>
				<tr>
					<th>1회 구매 시 <br /> 최대구매수량</th>
					<td><input type="text" id="" class="inp_sty10 va_m" /> 개 <span class="dp1 mg_l20">* 정수로, 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>재고수량</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>무제한</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>수량입력</span></label>
						<input type="text" id="" class="inp_sty10 va_m" /> 개
						<p class="mg_t10">※ 입력하신 재고수량이 모두 판매되면 자동으로 ‘품절‘ 표시가 됩니다.</p>
					</td>
				</tr>
				<tr>
					<th>결제/입금확인 후<br /> 구매취소 여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>조건부 가능</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>절대불가</span></label>
						<input type="text" id="" class="inp_sty60 va_m" placeholder="고객에게 안내될 불가사유를 입력해 주십시오." />
					</td>
				</tr>
				<tr>
					<th>Itme 제작 완료 후<br /> 환불신청 여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>조건부 가능</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>절대불가</span></label>
						<input type="text" id="" class="inp_sty60 va_m" placeholder="고객에게 안내될 불가사유를 입력해 주십시오." />
					</td>
				</tr>
				<tr>
					<th>Itme 제작 완료 후<br /> 교환요청 여부</th>
					<td>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>조건부 가능</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>절대불가</span></label>
						<input type="text" id="" class="inp_sty60 va_m" placeholder="고객에게 안내될 불가사유를 입력해 주십시오." />
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="38%"><col width="12%"><col width="38%"></colgroup>
			<thead>
				<tr>
					<th colspan="4">수수료 정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>수수료 운영형태</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체 수수료</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>Item 개별 수수료</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>판매 수수료</th>
					<td><input type="text" id="" class="inp_sty20 va_m" /> %</td>
					<th><span class="important">*</span>결제대행 수수료</th>
					<td><input type="text" id="" class="inp_sty20 va_m" /> %</td>
				</tr>
				<tr>
					<th><span class="important">*</span>수수료 부가세</th>
					<td colspan="3"><input type="text" id="" class="inp_sty10 va_m" /> %</td>
				</tr>
				<tr>
					<th><span class="important">*</span>수수료 적용일</th>
					<td colspan="3"><input type="text" id="" class="inp_sty10 va_m" /><a href="" class="calendar va_m"></a> <span class="dp1 mg_l20">※ 선택일자의 0시 부터 적용</span></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="38%"><col width="12%"><col width="38%"></colgroup>
			<thead>
				<tr>
					<th colspan="4">Craft Shop 정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Shop 코드</th>
					<td>AC124568</td>
					<th>판매자 구분</th>
					<td>개인판매자</td>
				</tr>
				<tr>
					<th>Shop 명</th>
					<td colspan="3"><a href="" class="alink" target="_blank">POFF</a></td>
				</tr>
				<tr>
					<th>작가</th>
					<td colspan="3">문소리 <a href="" class="btn2">메시지</a> <a href="" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<th>CIRCUS 담당자</th>
					<td colspan="3">홍길동 / 02-456-8975 (051) / 010-1234-5678</td>
				</tr>
				<tr>
					<th>Shop 상태</th>
					<td colspan="3">운영 중</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>