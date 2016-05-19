<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[비밀번호 변경주기 관리]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 비밀번호 변경주기 관리</div>
		</div>

		<div class="sub_title">
			- 회원의 비밀번호는 주기적으로 변경해 주는 것이 좋습니다.
			- 입력하신 기간동안 비밀번호를 사용한 회원에게 비밀번호 변경권유 메시지가 노출됩니다. 
		</div>
		
		<table class="write2">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th>비밀번호 변경주기</th>
					<td class="ag_l"><input type="text" id="" class="inp_sty10" /><span class="tdline">개월</span></td>
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