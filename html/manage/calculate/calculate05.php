<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[수수료관리]</h2>
			<div class="location">Home &gt; 정산관리 &gt; 수수료관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">기본수수료</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>판매 수수료</th>
					<td><input type="text" id="" class="inp_sty5" /><span class="tdline">%</span></td>
					<th><span class="important">*</span>결제대행 수수료</th>
					<td><input type="text" id="" class="inp_sty5" /><span class="tdline">%</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>수수료 부가세</th>
					<td><input type="text" id="" class="inp_sty5" /> <span class="tdline">%</span></td>
					<th><span class="important">*</span>수수료 적용일</th>
					<td><input type="text" id="" class="inp_sty30" /><a href="" class="calendar"></a><span class="ex">* 선택일자의 0시 부터 적용</span></td>
				</tr>
			</tbody>
		</table>
		
		<div class="help_tip">
			<dl>
				<dt><img src="/images/adm/icn_q1.png" alt="느낌표" class="icn_q1" />도움말</dt>
				<dd>- Craft Shop 정산 시 default 로 적용할 수수료 관리 페이지입니다.</dd>
				<dd>- 설정하신 수수료가 기본 수수료로 적용됩니다.</dd>
			</dl>
		</div>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>







</body>
</html>