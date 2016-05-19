<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[카테고리 관리]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 카테고리 관리</div>
		</div>
		
		<div class="fl_l cboth mg_b20" style="width:50%; padding-right:2%;">
			<div class="category" style="width:50%;">
				<div class="tit bo_rn">
					<span class="dp2 fl_l">1차 카테고리</span>
					<a href="" class="btn1 dp2 fl_r">추가</a>
				</div>
				<ul class="bo_rn">
					<li>
						<span>Accessories</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Bags &amp; Purses</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Beauty</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Clothing</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Home &amp; Living</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Jewelry</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Shoes</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Gift</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Designers</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>Special</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<!-- 카테고리명 입력 -->
					<li><input type="text" id="" class="inp_sty80 mg_t10" /></li>
					<!-- //카테고리명 입력 -->
				</ul>
			</div>

			<div class="category" style="width:50%;">
				<div class="tit">
					<span class="dp2 fl_l">2차 카테고리</span>
					<a href="" class="btn1 dp2 fl_r">추가</a>
				</div>
				<ul>
					<li>
						<span>001</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>002</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>003</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>004</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
					<li>
						<span>005</span>
						<div>
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						</div>
					</li>
				</ul>
			</div>
		</div>

		<table class="write1 fl_l" style="width:48%;">
			<colgroup><col width="30%" /><col width="20%" /><col width="50%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2">선택된 카테고리 정보</th>
					<th class="ag_r"><span class="important">*</span> 은 필수 입력사항입니다</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>현재 카테고리</th>
					<td colspan="2">Accessories</td>
				</tr>
				<tr>
					<th>카테고리 코드</th>
					<td colspan="2">001</td>
				</tr>
				<tr>
					<th><span class="important">*</span>카테고리명</th>
					<td colspan="2"><input type="text" id="" class="inp_sty30" /></td>
				</tr>
				<tr>
					<th>카테고리 설명</th>
					<td colspan="2"><input type="text" id="" class="inp_sty60" /></td>
				</tr>
				<tr>
					<th>카테고리의 Item 수</th>
					<td colspan="2">54 개</td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 Item 선택</th>
					<td colspan="2">
						<input type="file" name="" class="inp_file" value="찾아보기">
					</td>
				</tr>
				<tr>
					<th>고유주소</th>
					<td colspan="2">http://neomart.circus.com/product/list.html?cate_no=001</td>
				</tr>
				<tr>
					<th>사용여부</th>
					<td colspan="2">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>사용안함</span></label>
					</td>
				</tr>
			</tbody>
		</table>


	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>