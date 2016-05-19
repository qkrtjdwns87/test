<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[시즌추천검색어]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 시즌추천검색어</div>
		</div>

		<div class="sub_title">
			- Item 및 브랜드 검색 시 사용되는 추천 검색어관리입니다.
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="70%" /><col width="20%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>추천검색어</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td class="ag_l"><input type="text" class="inp_sty80" /></td>
					<td>
						<a href="#"><img src="/images/adm/btn_up.gif" alt="위" /></a>
						<a href="#"><img src="/images/adm/btn_down.gif" alt="아래" /></a>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1">이전으로 되돌리기</a>
			<a href="" class="btn3">저장</a>
		</div>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>