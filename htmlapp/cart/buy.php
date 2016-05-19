<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
</head>
<body>
<div id="wrap">
	<div id="buy_container">
		<!-- 구매정보 -->
		<section id="buy_total_detail">
			<dl>
				<dt>Winter Holiday Clutch <span>외 3개</span></dt>
				<dd class="photo"><img src="/images/app/cart/test.jpg" width="280" height="190" alt="" /></dd>
				<dd class="total_price">총 결제금액 <span><strong>76,000</strong>원</span></dd>
			</dl>
		</section>

		<section id="buy_detail_info">
			<dl>
				<dt>주문자 정보</dt>
				<dd><input type="text" class="inp_login_style2" placeholder="주문자명" /></dd>
				<dd><input type="text" class="inp_login_style2" placeholder="주문자 휴대폰 번호" /></dd>
				<dd><input type="text" class="inp_login_style2" placeholder="주문자 이메일" /></dd>
			</dl>
			<p class="text">입력하신 휴대폰번호와 이메일로 결제 및 구매정보를 알려드립니다.</p>

			<dl class="shipping_info">
				<dt>배송지 정보</dt>
				<dd class="delivery">
					<input type="radio" name="delivery" id="delivery1" /><label for="delivery1">최근배송지</label>
					<input type="radio" name="delivery" id="delivery2" /><label for="delivery2">신규배송지</label>
				</dd>
				<dd>
					<select name="" id="">
						<option value="">서울시 서초구 서초3동 1507-39</option>
					</select>
				</dd>
				<dd><input type="text" class="inp_login_style2" placeholder="이름" /></dd>
				<dd><input type="text" class="inp_login_style2" placeholder="휴대폰 번호" /></dd>
				<dd class="post_code"><input type="text" class="inp_login_style2" placeholder="배송지 우편번호" /><a href="" class="btn">검색</a></dd>
				<dd><input type="text" class="inp_login_style2" placeholder="배송지" /></dd>
				<dd><input type="text" class="inp_login_style2" placeholder="상세주소" /></dd>
			</dl>
			
			<dl class="pay_info">
				<dt>결제 정보</dt>
				<dd class="payment">
					<input type="radio" name="payment" id="payment1" /><label for="payment1">신용카드</label>
					<input type="radio" name="payment" id="payment2" /><label for="payment2">무통장 입금</label>
					<input type="radio" name="payment" id="payment3" /><label for="payment3">휴대폰 소액결제</label>
				</dd>
			</dl>

			<div class="privacy">
				<ul>
					<li>
						<label for="privacy1"><input type="checkbox" class="inp_checkbox1" id="privacy1" name="" />CIRCUS 이용약관 동의 (필수)</label>
						<a href="">전문보기</a>
					</li>
					<li>
						<label for="privacy2"><input type="checkbox" class="inp_checkbox1" id="privacy2" name="" />개인정보 수집 및 이용 동의 (필수)</label>
						<a href="">전문보기</a>
					</li>
				</ul>
			</div>
		</section>
	</div>
	
	<!-- 메뉴바 -->
	<div class="buy_box">
		<ul class="btn2">
			<!-- [D] dim 일 경우 class dim 추가 -->
			<li><a href="" class="normal">이전으로</a></li>
			<li><a href="" class="emphasis dim">결제 진행</a></li>
		</ul>
	</div>
	<!-- //메뉴바 -->

</div>
<script src="js/ui.js"></script>
</body>
</html>