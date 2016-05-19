<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>

<!-- container -->
<div id="container">
	<div id="content">

		
		<p>(btn1)</p>
		<a href="" class="btn1">CIRCUS 바로가기</a>
		<br /><br />

		<p>(btn2)</p>
		<a href="" class="btn2">메시지</a>
		<br /><br />

		<p>(btn2 활성화)</p>
		<a href="" class="btn2 on">메시지 on</a>
		<br /><br />

		<p>(btn3)</p>
		<a href="" class="btn3">저장</a>
		<br /><br />

		<p>(달력버튼)</p>
		<a href="" class="calendar"></a>
		<br /><br />

		<p>(폰트컬러)</p>
		<span class="blue">파란색</span> <br />
		<span class="red">붉은색</span>
		<br /><br />

		<p>(좌우정렬)</p>
		<p class="ag_l">왼쪽</p> <br />
		<p class="ag_r">오른쪽</p>
		<br /><br />
	
		<div class="title">
			<h2>[전체 Craft Shop 현황]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 전체 Craft Shop 현황</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>Shop 상태</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>운영 중</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>임시휴업</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>노출중단</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>운영중지</span></label>
					</td>
				</tr>
				<tr>
					<th>Craft Shop명</th>
					<td><input type="text" id="" class="inp_sty90" /></td>
					<th>Craft Shop코드</th>
					<td><input type="text" id="" class="inp_sty90" placeholder="코드 8자리 입력" maxlength="8" /></td>
				</tr>
				<tr>
					<th>계정(이메일)</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /> *예시) abc@abc.co.kr</td>
				</tr>
				<tr>
					<th>작가명</th>
					<td colspan="3"><input type="text" id="" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th>승인일</th>
					<td colspan="3">
						<input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a><span class="to">~</span><input type="text" id="" class="inp_sty10" /><a href="" class="calendar"></a>
						<a href="" class="btn2 on">오늘</a><a href="" class="btn2">1개월</a><a href="" class="btn2">6개월</a><a href="" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>뱃지</th>
					<td colspan="3">
						<label><input type="radio" id="" name="" class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>오늘의 작가</span></label>
						<label><input type="radio" id="" name="" class="inp_radio" /><span>인기작가</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn1">초기화</a>
			<a href="" class="btn1">검색</a>
		</div>
		
		<br /><br /><br />
		<table class="write2">
			<colgroup><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>Shop 코드</th>
					<th>Shop명</th>
					<th>작가</th>
					<th>계정(이메일)</th>
					<th>대표연락처</th>
					<th>등록item수</th>
					<th>승인일</th>
					<th>뱃지</th>
					<th>상태</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>101</td>
					<td>AC1202456</td>
					<td>POFF</td>
					<td>문소리</td>
					<td>sori@naver.com</td>
					<td>010-1234-5678</td>
					<td>36</td>
					<td>2016-01-10</td>
					<td>오늘의 작가</td>
					<td>운영 중</td>
					<td><a href="" class="btn1">메시지</a><a href="" class="btn1">SMS</a></td>
				</tr>
			</tbody>
		</table>

		<a href="" class="btn1 mg_t10">엑셀다운로드</a>

		<!-- paging -->
		<div class="pagination">
			<a href="#" class="prev"><img src="/images/adm/btn_paging_prev.gif" alt="전페이지로" /></a>
			<a href="#"><span class="on">1</span></a>
			<a href="#"><span>2</span></a>
			<a href="#"><span>3</span></a>
			<a href="#"><span>4</span></a>
			<a href="#"><span>5</span></a>
			<a href="#"><span>6</span></a>
			<a href="#"><span>7</span></a>
			<a href="#"><span>8</span></a>
			<a href="#"><span>9</span></a>
			<a href="#"><span>10</span></a>
			<a href="#" class="next"><img src="/images/adm/btn_paging_next.gif" alt="다음으로" /></a>
		</div>
		<!--// paging -->
		
		<br /><br /><br />
		<table class="write1">
			<colgroup><col width="10%" /><col width="40%" /><col width="10%" /><col width="40%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">활동정보</th>
					<th class="ag_r">
						<span>2016-01-10 11:30 현재</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>등록 Item / Flag수</th>
					<td>54개 / 56건</td>
					<th>Shop Flag 수</th>
					<td>7건</td>
				</tr>
				<tr>
					<th>PC웹에서 공유된 건</th>
					<td>0건</td>
					<th>모바일앱에서 공유된 건</th>
					<td>0건</td>
				</tr>
			</tbody>
		</table>


		<br /><br /><br />
		<table class="write2">
			<colgroup><col width="10%" /></colgroup>
			<tbody>
				<tr>
					<th>썸네일1</th>
					<td>

						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">1233-small.jpg</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a>
							</dd>
							<dd><input type="file" name="" class="inp_file" value="파일찾기" /></dd>
						</dl>

					</td>
				</tr>

				<tr>
					<th>썸네일2</th>
					<td>

						<dl class="dl_img">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="" class="alink">1233-small.jpg</a> <span>(AVC124578)</span><a href="#" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a>
							</dd>
							<dd><a href="" class="alink"><img src="/images/adm/ico_shop.gif" alt="ico_shop" class="icn_shop" />poff</a></dd>
						</dl>

					</td>
				</tr>

				<tr>
					<th>썸네일3</th>
					<td>
						<dl class="dl_img1">
							<dt><img src="/images/adm/@thumb.gif" width="100" height="100" alt="" /></dt>
							<dd><a href="" class="alink">크리스마스한정 블랙 클러치</a></dd>
							<dd>색상: 녹색</dd>
							<dd>재질: 가죽</dd>
							<dd>퀼트(+1,000원)</dd>
							<dd>문양: 나무</dd>
							<dd>주머니: 있음</dd>
							<dd>선물포장: 없음</dd>
						</dl>
					</td>
				</tr>

				<tr>
					<th>위아래</th>
					<td>
						<a href="#"><img src="/images/adm/btn_up.gif" alt="위" /></a>
						<a href="#"><img src="/images/adm/btn_down.gif" alt="아래" /></a>
					</td>
				</tr>

			</tbody>
		</table>
		
		

		<br /><br /><br />
		<!-- tip -->
		<div class="help_tip">
			<dl>
				<dt><img src="/images/adm/icn_q1.png" alt="느낌표" class="icn_q1" />도움말</dt>
				<dd>- 본 자료는 전반적인 영업현황을 기술적으로 나타내는 것으로, 통계 데이터의 집계에는 일부 지연, 누락 또는 오차가 발생할 수 있습니다. 본 자료는 참고용이며, 그 외의 용도로 사용할 수 없습니다.</dd>
				<dd>- 미입금상태 전환, 환불철회 등으로 인해 오차가 발생할 수 있습니다</dd>
				<dd>- 매출내역은 배송완료일을 기준으로 산정됩니다.</dd>
				<dd>- 00시 이후 전일 데이터의 보정 작업이 매일 진행됩니다.</dd>
				<dd>- 결제금액은 카드 및 휴대폰결제완료, 입금확인이 된 내역입니다.</dd>
				<dd>- 환불합계는 커드 및 휴대폰결제 취소완료, 환불액 입금완료된 내역입니다.</dd>
			</dl>
		</div>
		<!-- //tip -->
		


		<br /><br /><br />

		<!-- comment -->
		<div class="comment">
			<div class="sub_title">
				<span class="fl_l font15 bold"><a href="" class="alink">poff</a> 님과의 대화</span>
				<a href="" class="btn1 fl_r">삭제</a>
				<a href="" class="btn1 fl_r">목록</a>
			</div>

			<p class="cboth ag_c"><a href="" class="btn2">이전 대화 보기 ▲</a></p>
			<dl class="comment_dl">
				<dt><img src="/images/adm/shop_img.gif" width="80" alt="shop 이미지" /></dt>
				<dd class="day">2016-01-10 12:22:11</dd>
				<dd>주문량이 너무 많아서 임시휴업을 하려고 하는데요.다른 방법은 없을까요? <br />다른 방법을 좀 알려주십시오.휴업은 피하고 싶어요.<br />주문량이 너무 많아서 임시휴업을 하려고 하는데요.다른 방법은 없을까요? <br />다른 방법을 좀 알려주십시오.휴업은 피하고 싶어요.</dd>
			</dl>
			<dl class="comment_dl">
				<dt><img src="/images/adm/circus_img.gif" width="80" alt="circus 이미지" /></dt>
				<dd class="day">2016-01-10 12:22:11</dd>
				<dd>주문량이 너무 많아서 임시휴업을 하려고 하는데요.다른 방법은 없을까요? <br />다른 방법을 좀 알려주십시오.휴업은 피하고 싶어요.<br />주문량이 너무 많아서 임시휴업을 하려고 하는데요.다른 방법은 없을까요? <br />다른 방법을 좀 알려주십시오.휴업은 피하고 싶어요.</dd>
			</dl>
			<dl class="comment_dl">
				<dt><img src="/images/adm/shop_img.gif" width="80" alt="shop 이미지" /></dt>
				<dd class="day">2016-01-10 12:22:11</dd>
				<dd>주문량이 너무 많아서 임시휴업을 하려고 하는데요.다른 방법은 없을까요? <br />다른 방법을 좀 알려주십시오.휴업은 피하고 싶어요.<br />주문량이 너무 많아서 임시휴업을 하려고 하는데요.다른 방법은 없을까요? <br />다른 방법을 좀 알려주십시오.휴업은 피하고 싶어요.</dd>
			</dl>
		</div>
		<!-- //comment -->

		<br /><br /><br />
		<!-- reply -->
		<div class="reply">
			<ul>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
				<li>
					<span class="name">- 남일우 <span class="day">2016-01-10 11:20:32</span></span>
					<span class="txt">아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.아무때나 상관없다고 합니다.</span>
				</li>
			</ul>
		</div>
		<!-- //reply -->

		<!-- 팝업 -->
		<p><a href="javascript:;" onclick="$('#layer_pop').show();"><span class="blue">팝업</span></a></p>
		<div id="layer_pop" class="pop">
			<div class="bg"></div>
			<div class="popup_box">
				<div class="top">
					<a href="javascript:;" onclick="$('#layer_pop').hide();"><img src="/images/adm/layer_btn_close.gif" alt="close" /></a>
				</div>
				
				<div class="iframe"><iframe src="/html/manage/popup/pop_shop01.php" width="800" height="520" frameborder="0" scrolling="yes"></iframe></div>
			</div>
		</div>
		<!-- //팝업 -->




	</div>
</div>
<!--// container -->

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>