<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[1:1문의]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 1:1문의</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col width="40%" /><col width="10%" /><col width="40%" /></colgroup>
			<tr>
				<th>문의분류</th>
				<td colspan="3">
					<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>서비스 이용</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>결제/정산</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>주문/배송</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>광고/홍보</span></label>
					<label><input type="radio" id="" name="" class="inp_radio" /><span>기타</span></label>
				</td>
			</tr>
			<tr>
				<th>제목</th>
				<td colspan="3"><input type="text" id="" class="inp_sty90" /></td>
			</tr>
			<tr>
				<th>작성자</th>
				<td>홍길동</td>
				<th>등록일시</th>
				<td>2016-01-20 12:34</td>
			</tr>
			<tr>
				<th>상태</th>
				<td colspan="3">문의접수</td>
			</tr>
			<tr>
				<th>내용</th>
				<td colspan="3"><textarea id="" rows="5" cols="5" class="textarea1"></textarea></td>
			</tr>
		</table>
	
		<table class="write1 mg_t10">
			<colgroup><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2">답변</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>내용</th>
					<td><textarea id="" rows="5" cols="5" class="textarea1"></textarea></td>
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