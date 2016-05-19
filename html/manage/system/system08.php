<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[금칙어 관리]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 금칙어 관리</div>
		</div>

		<div class="sub_title">
			- 금칙어 관리는 ‘CIRCUS’ 웹사이트 및 모바일앱에서 등록을 불허하는 단어 관리 메뉴입니다.<br />
			- 아래의 금칙어 입력란에 등록 불허 단어를 입력하시면 고객이 ‘CIRCUS’ 웹사이트 및 모바일앱을 통해 메시지쓰기, 게시판에 글쓰기, 댓글 쓰기 할 때 입력자체를 막아줍니다.<br />
			- 금칙어 입력의 개수는 제한이 없으며, 여러 단어를 입력하실 때에는 콤마(,)로 구분하여 주십시오.
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>금칙어</th>
					<td>
						<textarea id="" rows="" cols="" class="textarea1"></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">취소</a>
			<a href="" class="btn3">저장</a>
		</div>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>