<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[Story]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 메인 관리 &gt; Story</div>
		</div>

		<div class="sub_title">
			<span class="important">*</span>은 필수 입력사항입니다.
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="10%" /><col width="10%" /><col width="70%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>구분</th>
					<th colspan="2">메인 비주얼 이미지</th>
				</tr>
			</thead>
			<tbody>
				<!-- 1 -->
				<tr>
					<td rowspan="5">1</td>
					<td rowspan="2"><span class="important">*</span>섬네일</td>
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
				</tr>

				<tr>
					<td>모바일앱용<br /><span class="red">(000*000)</span></td>
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
					<td colspan="2"><span class="important">*</span>Story 게시물</td>
					<td class="ag_l">
						<a href="" class="alink">“DEAR MAISON”의 작가 Maison</a> <a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
						<p class="mg_t10"><input type="text" class="inp_sty40" /><a href="" class="btn2">찾아보기</a></p>
					</td>
				</tr>

				<tr>
					<td colspan="2"><span class="important">*</span>요약글</td>
					<td>
						<textarea id="" rows="5" cols="5" class="textarea1">최대 300자 이내</textarea>
					</td>
				</tr>

				<tr>
					<td colspan="2">관련 Item</td>
					<td>
						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">크리스마스한정 블랙 레더 클러치</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" />
							</dd>
							<dd><input type="text" class="inp_sty40" value="Item 이름 (Item 코드)" /><a href="" class="btn2">찾아보기</a></dd>
						</dl>
					</td>
				</tr>
				<!-- //1 -->
			</tbody>
		</table>

		<div class="btn_list ov_fl">
			<a href="" class="btn1 fl_l">미리보기</a>

			<a href="" class="btn1">이전으로 되돌리기</a>
			<a href="" class="btn3">저장</a>
		</div>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>