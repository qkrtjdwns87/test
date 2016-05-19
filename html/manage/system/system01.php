<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[메인비주얼]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 메인 관리 &gt; 메인비주얼</div>
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="10%" /><col width="70%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th colspan="2">메인 비주얼 이미지</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
				<!-- 1 -->
				<tr>
					<td rowspan="3">1</td>
					<td>PC 웹용<br /><span class="red">(000*000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">1233-small.jpg</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
							</dd>
							<dd><input type="file" name="" class="inp_file" value="파일찾기" /></dd>
						</dl>
					</td>
					<td rowspan="3">
						<a href="#"><img src="/images/adm/btn_up.gif" alt="위" /></a>
						<a href="#"><img src="/images/adm/btn_down.gif" alt="아래" /></a>
					</td>
				</tr>
				<tr>
					<td>모바일용<br /><span class="red">(000*000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">1233-small.jpg</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
							</dd>
							<dd><input type="file" name="" class="inp_file" value="파일찾기" /></dd>
						</dl>
					</td>
				</tr>
				<tr>
					<td>링크</td>
					<td class="ag_l">
						<input type="text" class="inp_sty60" />
						<label><input type="checkbox" class="inp_check" />새창으로</label>
					</td>
				</tr>
				<!-- //1 -->
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