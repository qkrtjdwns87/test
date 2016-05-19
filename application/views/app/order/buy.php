<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$shopCnt = count($recordSet);
   	$itemCnt = 0;
   	$i = 0;
   	$j = 1; //아이템 반복되는 만큼의 증가변수
   	$totAmount = 0; //전체 구매금액
   	$totPrice = 0; //순수금액 합계
   	$totQuantity = 0;
   	$totOptionPrice = 0;
   	$totShopAmount = 0; //샵별 합산 금액
   	$totDeliveryPrice = 0; //전체 배송 금액
	$defaultImg = '';
	$fileName = '';
	$goodInfo = ''; //에스크로 상품정보 작성
	foreach ($recordSet as $rs):
		$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
		$deliverPrice = $rs['DELIVERY_PRICE'];	
		
		$t = 0;
		$shopAmount = 0; //샵별 합계총금액
		$shopPrice = 0; //샵별순수 합계금액
		$shopOptPrice = 0; //샵별옵션 합계금액
		$shopQuantity = 0;
		foreach ($rs['cartItemSet'] as $irs):
			$price = ($irs['DISCOUNT_YN'] == 'Y') ? $irs['DISCOUNT_PRICE'] : $irs['ITEM_PRICE'];
			$quantity = $irs['QUANTITY']; //구매수량
			$arrOpt = (!empty($irs['ITEMOPTION_INFO'])) ? explode('-', $irs['ITEMOPTION_INFO']) : array();
			$optAmount = $optPrice = 0;
			$optTitle = '';
			foreach ($arrOpt as $ot) //옵션선택사항
			{
				$arrOptInfo = explode('|', $ot);
				$optTitle .= $arrOptInfo[0].':'.$arrOptInfo[2].'<br />';
				$optPrice = $optPrice + $arrOptInfo[3]; //옵션가격
				$optAmount = $optAmount + ($arrOptInfo[3] * $quantity);
			}			
			$amount = ($price * $quantity) + $optAmount;
			$shopPrice = $shopPrice + $price;
			$shopOptPrice = $shopOptPrice + $optPrice;
			$shopQuantity = $shopQuantity + $quantity;
			$totOptionPrice = $totOptionPrice + $optPrice;
			$totQuantity = $totQuantity + $quantity;
			$totPrice = $totPrice + $price;	
			$maxBuyCount = $irs['MAXBUY_COUNT'];
			$stockFreeYn = $irs['STOCKFREE_YN'];
			if (($stockFreeYn == 'N' && $irs['STOCKFREE_YN'] < $quantity) || $irs['ITEMSTATECODE_NUM'] == 8070)
			{
				//여기서 품절이 나는 경우 중지
				$isSoldOut = 'Y';
				$stockCnt = 0;
				$this->common->message('품절된 아이템이 있습니다.장바구니로 돌아갑니다.', '', '');
				break;
			}
			else
			{
				$isSoldOut = 'N';
				$stockCnt = ($stockFreeYn == 'Y') ? 100000 : $irs['STOCK_COUNT'];
			}
			
			if ($i == 0 && $t == 0)
			{
				$img = '';
				$arrFile = explode('|', $irs['FIRST_FILE_INFO']);
				if (count($arrFile) > 0)
				{
					if ($arrFile[4] == 'Y')	//썸네일생성 여부
					{
						$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
					}
					else
					{
						$img = $arrFile[2].$arrFile[3];
					}
				}
				$fileName = (!empty($img)) ? $img : $defaultImg;
				$itemName = $irs['ITEM_NAME'];
			}

			//에스크로 상품정보
			$goodInfo .= ($j > 1) ? unichr(30) : '';
			$goodInfo .= 'seq='.$j.unichr(31).'ordr_numb='.$orderCode.'_'.$irs['CARTORD_NUM'].unichr(31);
			$goodInfo .= 'good_name='.$irs['ITEM_NAME'].unichr(31).'good_cntx='.$quantity.unichr(31).'good_amtx='.$amount;
			
			$shopAmount = $shopAmount + $amount;
			$t++;
			$j++;
		endforeach;
		
		$itemCnt = $itemCnt + $t;
		$totDeliveryPrice = $totDeliveryPrice + $deliverPrice;
		$totShopAmount = $totShopAmount + $shopAmount;	
		$i++;
	endforeach;
	$totAmount = $totShopAmount + $totDeliveryPrice;
	
	$_SESSION['order_amount'] = $totAmount; //결제금액 위변조 여부 검증
	
	$goodName = $itemName;
	$goodName .= ($itemCnt > 1) ? '외 '.($itemCnt -1).'개' : '';
	$itemName .= ($itemCnt > 1) ? '<span>외 '.($itemCnt -1).'개</span>' : '';
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	/*********************************************************************************
	 *
	 * KCP (PG)
	 * 
	 *********************************************************************************/
	/* ============================================================================== */
	/* =   PAGE : 결제 요청 PAGE                                                    = */
	/* = -------------------------------------------------------------------------- = */
	/* =   이 페이지는 Payplus Plug-in을 통해서 결제자가 결제 요청을 하는 페이지    = */
	/* =   입니다. 아래의 ※ 필수, ※ 옵션 부분과 매뉴얼을 참조하셔서 연동을        = */
	/* =   진행하여 주시기 바랍니다.                                                = */
	/* = -------------------------------------------------------------------------- = */
	/* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
	/* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do			        = */
	/* = -------------------------------------------------------------------------- = */
	/* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
	/* ============================================================================== */
	require_once $_SERVER["DOCUMENT_ROOT"].'/pg/cfg/site_conf_inc.php';	
	/* kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 */
	$req_tx          = $this->input->post_get("req_tx", FALSE); // 요청 종류
	$res_cd          = $this->input->post_get("res_cd", FALSE); // 응답 코드
	$tran_cd         = $this->input->post_get("tran_cd", FALSE); // 트랜잭션 코드
	$ordr_idxx       = $this->input->post_get("ordr_idxx", FALSE); // 쇼핑몰 주문번호
	$good_name       = $this->input->post_get("good_name", FALSE); // 상품명
	$good_mny        = $this->input->post_get("good_mny" , FALSE); // 결제 총금액
	$buyr_name       = $this->input->post_get("buyr_name", FALSE); // 주문자명
	$buyr_tel1       = $this->input->post_get("buyr_tel1", FALSE); // 주문자 전화번호
	$buyr_tel2       = $this->input->post_get("buyr_tel2", FALSE); // 주문자 핸드폰 번호
	$buyr_mail       = $this->input->post_get("buyr_mail", FALSE); // 주문자 E-mail 주소
	$use_pay_method  = $this->input->post_get("use_pay_method", FALSE); // 결제 방법
	$ipgm_date       = $this->input->post_get("ipgm_date", FALSE); // 가상계좌 마감시간
	$enc_info        = $this->input->post_get("enc_info", FALSE); // 암호화 정보
	$enc_data        = $this->input->post_get("enc_data", FALSE); // 암호화 데이터
	$van_code        = $this->input->post_get("van_code", FALSE);
	$cash_yn         = $this->input->post_get("cash_yn", FALSE);
	$cash_tr_code    = $this->input->post_get("cash_tr_code", FALSE);;
	
	$rcvr_name = $this->input->post_get("rcvr_name", FALSE); //수취인 이름
	$rcvr_tel1 = $this->input->post_get("tel1", FALSE); //수취인 전화번호
	$rcvr_tel2 = $this->input->post_get("rcvr_tel2", FALSE); //수취인 휴대폰번호
	$rcvr_mail = $this->input->post_get("rcvr_mail", FALSE); //수취인 E-Mail
	$rcvr_zipx = $this->input->post_get("rcvr_zipx", FALSE); //수취인 우편번호
	$rcvr_add1 = $this->input->post_get("rcvr_add1", FALSE); //수취인 주소 -->
	$rcvr_add2 = $this->input->post_get("rcvr_add2", FALSE); //수취인 상세주소
	
	/* 기타 파라메터 추가 부분 - Start - */
	$param_opt_1    = $this->input->post_get("param_opt_1", FALSE); // 기타 파라메터 추가 부분
	$param_opt_2    = $this->input->post_get("param_opt_2", FALSE); // 기타 파라메터 추가 부분
	$param_opt_3    = $this->input->post_get("param_opt_3", FALSE); // 기타 파라메터 추가 부분
	/* 기타 파라메터 추가 부분 - End -   */
	$tablet_size     = "1.0"; // 화면 사이즈 고정
	$url = $siteDomain.$currentUrl;	//"http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	
	/**
	 * Return unicode char by its code
	 *
	 * @param int $u
	 * @return char
	 */
	function unichr($u) {
		return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
	<script src="/js/jquery.base64.min.js"></script>
	<!-- 거래등록 하는 kcp 서버와 통신을 위한 스크립트-->
	<script type="text/javascript" src="/pg/mobile/js/approval_key.js"></script>	
	<script type="text/javascript">
	    $(document).ready(function () {

	    });
	    	
		var isMobile = {
    		Android: function() {
    		return navigator.userAgent.match(/Android/i);
    		},
    		BlackBerry: function() {
    		return navigator.userAgent.match(/BlackBerry/i);
    		},
    		iOS: function() {
    		return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    		},
    		Opera: function() {
    		return navigator.userAgent.match(/Opera Mini/i);
    		},
    		Windows: function() {
    		return navigator.userAgent.match(/IEMobile/i);
    		},
    		any: function() {
    		return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    		}
    	};

		/* 에스크로 장바구니 상품 상세 정보 생성 예제 */
		function create_goodInfo()
		{
			/*
			var chr30 = String.fromCharCode(30);
			var chr31 = String.fromCharCode(31);
			var good_info = "seq=1" + chr31 + "ordr_numb=20060310_0001" + chr31 + "good_name=양말" + chr31 + "good_cntx=2" + chr31 + "good_amtx=1000" + chr30 +
			"seq=2" + chr31 + "ordr_numb=20060310_0002" + chr31 + "good_name=신발" + chr31 + "good_cntx=1" + chr31 + "good_amtx=1500" + chr30 +
			"seq=3" + chr31 + "ordr_numb=20060310_0003" + chr31 + "good_name=바지" + chr31 + "good_cntx=1" + chr31 + "good_amtx=1000";
			alert(good_info);
			alert('<?=$goodInfo?>');
			*/
			document.order_info.good_info.value = '<?=$goodInfo?>';
		}
		  
    	function sendOrder(){
			if (trim($('#buyr_tel2').val()) == ''){
				alert('주문자 휴대폰번호를 입력하세요.');
				return;
			}	

			if (!IsNumber(trim($('#buyr_tel2').val()))){
				alert('주문자 휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}				

			if ($('#buyr_tel2').val().substr(0, 2) != '01'){
				alert('올바른 주문자 휴대폰번호를 입력하세요.');
				return;
			}	

			if (trim($('#buyr_tel2').val()).length < 10 || trim($('#buyr_tel2').val()).length > 11){
				alert('올바른 주문자 휴대폰번호를 입력하세요.');
				return;
			}	

			if (trim($('#buyr_mail').val()) == ''){
				alert('이메일 주소를 입력하세요.');
				return;
			}
						
			if (!IsEmail($('#buyr_mail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}			

			if (trim($('#rcvr_tel2').val()) == ''){
				alert('배송지 휴대폰번호를 입력하세요.');
				return;
			}	

			if (!IsNumber(trim($('#rcvr_tel2').val()))){
				alert('배송지 휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}				

			if ($('#rcvr_tel2').val().substr(0, 2) != '01'){
				alert('올바른 배송지 휴대폰번호를 입력하세요.');
				return;
			}	

			if (trim($('#rcvr_tel2').val()).length < 10 || trim($('#rcvr_tel2').val()).length > 11){
				alert('올바른 배송지 휴대폰번호를 입력하세요.');
				return;
			}	

			if (trim($('#rcvr_zipx').val()) == ''){
				alert('배송지 주소를 입력하세요.');
				return;
			}

			if (trim($('#rcvr_add1').val()) == ''){
				alert('배송지 주소를 입력하세요.');
				return;
			}

			if (trim($('#rcvr_add2').val()) == ''){
				alert('배송지 주소를 입력하세요.');
				return;
			}

            if ($("input:checkbox[id='privacy1']").is(":checked") == false){
                alert('CIRCUS이용약관 동의가 필요합니다.');
                return;
            }

            if ($("input:checkbox[id='privacy2']").is(":checked") == false){
                alert('개인정보 수집 및 이용 동의가 필요합니다.');
                return;
            }			

			var sel = $(':radio[name="ActionResult"]:checked').val();
			if (sel == '' || sel == undefined){
				alert('결제수단을 선택해 주세요.');
				return;
			}	
			
			kcp_AJAX();		        	
    	}

    	/* kcp web 결제창 호츨 (변경불가) */
    	function call_pay_form()
    	{
    		var v_frm = document.order_info;

    		document.getElementById("wrap").style.display = "none";
    		document.getElementById("layer_all").style.display  = "block";

    		v_frm.target = "frm_all";
    		// 인코딩 방식에 따른 변경 -- Start  
    		if(v_frm.encoding_trans == undefined)  { 
    			v_frm.action = PayUrl;
    		}
    		else
    		{
    			if(v_frm.encoding_trans.value == "UTF-8")  { 
    				v_frm.action = PayUrl.substring(0,PayUrl.lastIndexOf("/")) + "/jsp/encodingFilter/encodingFilter.jsp";  
    				v_frm.PayUrl.value = PayUrl; 
    			}
    			else
    			{
    				v_frm.action = PayUrl;
    			}
    		}
    		// 인코딩 방식에 따른 변경 -- End
    		if (v_frm.Ret_URL.value == "")
    		{
    			/* Ret_URL값은 현 페이지의 URL 입니다. */
    			alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
    			return false;
    		}
    		else
    		{
        		$('#pay_button').addClass('dim');
    			v_frm.submit();
    		}
    	}

    	/* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
    	function chk_pay()
    	{
    		self.name = "tar_opener";
    		var pay_form = document.pay_form;

    		if (pay_form.res_cd.value == "3001" )
    		{
    			alert("사용자가 취소하였습니다.");
    			pay_form.res_cd.value = "";
    		}
    		else if (pay_form.res_cd.value == "3000" )
    		{
    			alert("30만원 이상 결제를 할 수 없습니다.");
    			pay_form.res_cd.value = "";
    		}

    		document.getElementById("wrap").style.display = "block";
    		document.getElementById("layer_all").style.display  = "none";

    		if (pay_form.enc_info.value)
    		pay_form.submit();
    	}	

    	function jsf__chk_type()
    	{
    		if ( document.order_info.ActionResult.value == "card" )
    		{
    			document.order_info.pay_method.value = "CARD";
    			document.order_info.pay_mod.value = "N";
    		}
    		else if ( document.order_info.ActionResult.value == "acnt" )
    		{
    			document.order_info.pay_method.value = "BANK";
    			document.order_info.pay_mod.value = "O";
    		}
    		else if ( document.order_info.ActionResult.value == "vcnt" )
    		{
    			document.order_info.pay_method.value = "VCNT";
    			document.order_info.pay_mod.value = "O";
    		}
    		else if ( document.order_info.ActionResult.value == "mobx" )
    		{
    			document.order_info.pay_method.value = "MOBX";
    			document.order_info.pay_mod.value = "N";
    		}
    		else if ( document.order_info.ActionResult.value == "ocb" )
    		{
    			document.order_info.pay_method.value = "TPNT";
    			document.order_info.van_code.value = "SCSK";
    			document.order_info.pay_mod.value = "N";
    		}
    		else if ( document.order_info.ActionResult.value == "tpnt" )
    		{
    			document.order_info.pay_method.value = "TPNT";
    			document.order_info.van_code.value = "SCWB";
    			document.order_info.pay_mod.value = "N";
    		}
    		else if ( document.order_info.ActionResult.value == "scbl" )
    		{
    			document.order_info.pay_method.value = "GIFT";
    			document.order_info.van_code.value = "SCBL";
    			document.order_info.pay_mod.value = "N";
    		}
    		else if ( document.order_info.ActionResult.value == "sccl" )
    		{
    			document.order_info.pay_method.value = "GIFT";
    			document.order_info.van_code.value = "SCCL";
    			document.order_info.pay_mod.value = "N";
    		}
    		else if ( document.order_info.ActionResult.value == "schm" )
    		{
    			document.order_info.pay_method.value = "GIFT";
    			document.order_info.van_code.value = "SCHM";
    			document.order_info.pay_mod.value = "N";
    		}
    	}

    	function chgRecentAddr(e){
    		var currentAddr = $(e).val();
    		if (currentAddr != '' && currentAddr != undefined){
        		var arrAddr = currentAddr.split('|');
        		$('#rcvr_name').val(arrAddr[0]);
        		$('#rcvr_tel2').val(arrAddr[1]);
        		$('#rcvr_zipx').val(arrAddr[2]);
        		$('#rcvr_add1').val(arrAddr[3]);
        		$('#rcvr_add2').val(arrAddr[4]);
        		$('#param_opt_1').val(arrAddr[5]);
    		}
    	}

    	function chgAddr(e){
        	var currentSel = $(e).val();
        	if (currentSel == 'recent'){
            	$('#recentaddrDisp').show();
        	}else{
            	$("#recentaddr option:eq(0)").attr("selected", "selected");
        		$('#recentaddrDisp').hide();
        		$('#rcvr_name').val('');
        		$('#rcvr_tel2').val('');
        		$('#rcvr_zipx').val('');
        		$('#rcvr_add1').val('');
        		$('#rcvr_add2').val('');
        		$('#param_opt_1').val('');        		
        	}
    	}
	</script>
</head>
<body onload="jsf__chk_type();chk_pay();create_goodInfo();">
<div id="wrap">
	<form name="order_info" method="post">
	<input type="hidden" name="ordr_idxx" value="<?=$orderCode?>"/>
	<input type="hidden" name="good_name" value="<?=$goodName?>"/>
	<input type="hidden" name="good_mny" value="<?=$totAmount?>"/>
	
	<div id="buy_container">
		<!-- 구매정보 -->
		<section id="buy_total_detail">
			<dl>
				<dt><?=$itemName?></dt>
				<dd class="photo"><img src="<?=$fileName?>" width="280" height="190" alt="" /></dd>
				<dd class="total_price">총 결제금액 <span><strong><?=number_format($totAmount)?></strong>원</span></dd>
			</dl>
		</section>

		<section id="buy_detail_info">
			<dl>
				<dt>주문자 정보</dt>
				<dd><input type="text" id="buyr_name" name="buyr_name" value="<?=$userInfo['USER_NAME']?>" class="inp_login_style2" placeholder="주문자명" /></dd>
				<dd><input type="text" id="buyr_tel2" name="buyr_tel2" value="<?=str_replace('-', '', $userInfo['USER_MOBILE_DEC'])?>" class="inp_login_style2" placeholder="주문자 휴대폰 번호" /></dd>
				<dd><input type="text" id="buyr_mail" name="buyr_mail" value="<?=$userInfo['USER_EMAIL_DEC']?>" class="inp_login_style2" placeholder="주문자 이메일" /></dd>
			</dl>
			<p class="text">입력하신 휴대폰번호와 이메일로 결제 및 구매정보를 알려드립니다.</p>

			<dl class="shipping_info">
				<dt>배송지 정보</dt>
				<dd class="delivery">
					<input type="radio" name="delivery" id="delivery1" value="recent" onclick="javascript:chgAddr(this);"/><label for="delivery1">최근배송지</label>
					<input type="radio" name="delivery" id="delivery2" value="new" checked="checked" onclick="javascript:chgAddr(this);"/><label for="delivery2">신규배송지</label>
				</dd>
				<dd id="recentaddrDisp" style="display:none;">
					<select name="recentaddr" id="recentaddr" onchange="javascript:chgRecentAddr(this);">
						<option value="">최근 배송지 선택</option>
					<?
						foreach ($deliveryInfo as $rs):
					?>
						<option value="<?=$rs['RECIPIENT_NAME']?>|<?=str_replace('-', '', $rs['RECIPIENT_MOBILE_DEC'])?>|<?=$rs['RECIPIENT_ZIP_DEC']?>|<?=$rs['RECIPIENT_ADDR1_DEC']?>|<?=$rs['RECIPIENT_ADDR2_DEC']?>|<?=$rs['RECIPIENT_ADDR_JIBUN_DEC']?>"><?=$rs['RECIPIENT_ADDR1_DEC']?> <?=$rs['RECIPIENT_ADDR2_DEC']?></option>
					<?
						endforeach;
					?>
					</select>
				</dd>
				<dd><input type="text" id="rcvr_name" name="rcvr_name" value="<?=$userInfo['USER_NAME']?>" class="inp_login_style2" placeholder="이름" /></dd>
				<dd><input type="text" id="rcvr_tel2" name="rcvr_tel2" value="<?=str_replace('-', '', $userInfo['USER_MOBILE_DEC'])?>" class="inp_login_style2" placeholder="휴대폰 번호" /></dd>
				<!-- <dd class="post_code"><input type="text" id="rcvr_zipx" name="rcvr_zipx" class="inp_login_style1" placeholder="배송지 우편번호" readonly /><a href="javascript:searchAddressLayer('rcvr_zipx','rcvr_add1','rcvr_add2','param_opt_1');" class="btn">검색</a></dd> -->
				<dd class="post_code"><input type="text" id="rcvr_zipx" name="rcvr_zipx" class="inp_login_style1" placeholder="배송지 우편번호" readonly /><a href="javascript:app_showAddressWindow('우편번호검색', '<?=$siteDomain?>/app/order_a/addrsearch');" onclick="ios_showAddressWindow('우편번호검색', '<?=$siteDomain?>/app/order_a/addrsearch');" class="btn">검색</a></dd>
				
				
				<dd><input type="text" id="rcvr_add1" name="rcvr_add1" class="inp_login_style2" placeholder="배송지" readonly/></dd>
				<dd>
					<input type="text" id="rcvr_add2" name="rcvr_add2" class="inp_login_style2" placeholder="상세주소" />
					<input type="hidden" id="param_opt_1" name="param_opt_1" value=""/>
				</dd>
			</dl>
			
			<dl class="pay_info">
				<dt>결제 정보</dt>
				<dd class="payment">
					<input type="radio" name="ActionResult" id="payment1" onclick="jsf__chk_type();" value="card" checked="checked" /><label for="payment1">신용카드</label>
					<!-- 카드결제만 먼저 운영					 					
					<input type="radio" name="ActionResult" id="payment2" onclick="jsf__chk_type();" value="acnt" /><label for="payment2">무통장 입금</label>
					<input type="radio" name="ActionResult" id="payment3" onclick="jsf__chk_type();" value="mobx" /><label for="payment3">휴대폰 소액결제</label>
					 -->
				</dd>
			</dl>

			<div class="privacy">
				<ul>
					<li>
						<label for="privacy1"><input type="checkbox" class="inp_checkbox1" id="privacy1" name="" />CIRCUS 이용약관 동의 (필수)</label>
						<a href="#">전문보기</a>
					</li>
					<li>
						<label for="privacy2"><input type="checkbox" class="inp_checkbox1" id="privacy2" name="" />개인정보 수집 및 이용 동의 (필수)</label>
						<a href="#">전문보기</a>
					</li>
				</ul>
			</div>
		</section>
	</div>
	<input type="hidden" name="encoding_trans" value="UTF-8" />
	<input type="hidden" name="AppUrl" value="circuspay://card_pay" />
	<input type="hidden" name="PayUrl" />
	
	<!-- 공통정보 -->
	<input type="hidden" name="req_tx"          value="pay">                           <!-- 요청 구분 -->
	<input type="hidden" name="shop_name"       value="<?= $g_conf_site_name ?>">      <!-- 사이트 이름 --> 
	<input type="hidden" name="site_cd"         value="<?= $g_conf_site_cd   ?>">      <!-- 사이트 코드 -->
	<input type="hidden" name="currency"        value="410"/>                          <!-- 통화 코드 -->
	<input type="hidden" name="eng_flag"        value="N"/>                            <!-- 한 / 영 -->
	<!-- 결제등록 키 -->
	<input type="hidden" name="approval_key"    id="approval">
	<!-- 인증시 필요한 파라미터(변경불가)-->
	<input type="hidden" name="pay_method"      value="">
	<input type="hidden" name="van_code"        value="<?=$van_code?>">
	<!-- 신용카드 설정 -->
	<input type="hidden" name="quotaopt"        value="12"/>                           <!-- 최대 할부개월수 -->
	<!-- 가상계좌 설정 -->
	<input type="hidden" name="ipgm_date"       value=""/>
	<!-- 가맹점에서 관리하는 고객 아이디 설정을 해야 합니다.(필수 설정) -->
	<input type="hidden" name="shop_user_id"    value=""/>
	<!-- 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력해야합니다.(필수 설정) -->
	<input type="hidden" name="pt_memcorp_cd"   value=""/>
	<!-- 현금영수증 설정 -->
	<input type="hidden" name="disp_tax_yn"     value="Y"/>
	<!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
	<input type="hidden" name="Ret_URL"         value="<?=$url?>">
	<!-- 화면 크기조정 -->
	<input type="hidden" name="tablet_size"     value="<?=$tablet_size?>">
	
	<!-- 추가 파라미터 ( 가맹점에서 별도의 값전달시 param_opt 를 사용하여 값 전달 ) -->
	<!-- <input type="hidden" name="param_opt_1"     value=""> -->
	<input type="hidden" name="param_opt_2"     value="">
	<input type="hidden" name="param_opt_3"     value="">
	
	<?
	    /* ============================================================================== */
	    /* =   에스크로결제 사용시 필수 정보                                            = */
	    /* = -------------------------------------------------------------------------- = */
	    /* =   결제에 필요한 주문 정보를 입력 및 설정합니다.                            = */
	    /* = -------------------------------------------------------------------------- = */
	?>
	  <!-- 에스크로 사용유무 에스크로 사용 업체(가상계좌, 계좌이체 해당)는 escw_used 를 Y로 세팅 해주시기 바랍니다.-->
	  <input type="hidden" name="escw_used" value="N">
	  <!-- 장바구니 상품 개수 -->
	  <input type='hidden' name='bask_cntx' value="<?=$itemCnt?>">
	  <!-- 장바구니 정보(상단 스크립트 참조) -->
	  <input type='hidden' name='good_info' value="">
	  <!-- 에스크로 결제처리모드 KCP 설정된 금액 결제(사용 : 설정된금액적용: 사용안함: -->
	  <input type="hidden" name='pay_mod'   value="">
	  <!-- 배송소요기간 -->
	  <input type="hidden" name='deli_term' value='03'>
	
	<?
	    /* = -------------------------------------------------------------------------- = */
	    /* =   에스크로결제 사용시 필수 정보  END                                       = */
	    /* ============================================================================== */
	?>
	<?
	    /* ============================================================================== */
	    /* =   옵션 정보                                                                = */
	    /* = -------------------------------------------------------------------------- = */
	    /* =   ※ 옵션 - 결제에 필요한 추가 옵션 정보를 입력 및 설정합니다.             = */
	    /* = -------------------------------------------------------------------------- = */
		/* 카드사 리스트 설정
		예) 비씨카드와 신한카드 사용 설정시
		<input type="hidden" name='used_card'    value="CCBC:CCLG">
	
	    /*  무이자 옵션
	            ※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다)                             - "" 로 설정
	            ※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다)                           - "N" 로 설정
	            ※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)   - "Y" 로 설정
	    <input type="hidden" name="kcp_noint"       value=""/> */
	
	    /*  무이자 설정
	            ※ 주의 1 : 할부는 결제금액이 50,000 원 이상일 경우에만 가능
	            ※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용
	            예) 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:04
	            BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
	    <input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"/> */
	
		/* KCP는 과세상품과 비과세상품을 동시에 판매하는 업체들의 결제관리에 대한 편의성을 제공해드리고자, 
		   복합과세 전용 사이트코드를 지원해 드리며 총 금액에 대해 복합과세 처리가 가능하도록 제공하고 있습니다
		   복합과세 전용 사이트 코드로 계약하신 가맹점에만 해당이 됩니다
	       상품별이 아니라 금액으로 구분하여 요청하셔야 합니다
		   총결제 금액은 과세금액 + 부과세 + 비과세금액의 합과 같아야 합니다. 
		   (good_mny = comm_tax_mny + comm_vat_mny + comm_free_mny)
		
		    <input type="hidden" name="tax_flag"       value="TG03">  <!-- 변경불가	   -->
		    <input type="hidden" name="comm_tax_mny"   value=""    >  <!-- 과세금액	   --> 
	        <input type="hidden" name="comm_vat_mny"   value=""    >  <!-- 부가세	   -->
		    <input type="hidden" name="comm_free_mny"  value=""    >  <!-- 비과세 금액 --> */
	    /* = -------------------------------------------------------------------------- = */
	    /* =   옵션 정보 END                                                            = */
	    /* ============================================================================== */
	?>	
	</form>
	
	<!-- 메뉴바 -->
	<div class="buy_box">
		<ul class="btn2">
			<!-- [D] dim 일 경우 class dim 추가 -->
			<li><a href="javascript:app_closeWindow();" class="normal">이전으로</a></li>
			<li id="display_pay_button"><a href="javascript:sendOrder();" id="pay_button" class="emphasis">결제 진행</a></li>
		</ul>
	</div>
	<!-- //메뉴바 -->

</div>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="daum_layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
	
<!-- 스마트폰에서 KCP 결제창을 레이어 형태로 구현-->
<div id="layer_all" style="position:absolute; left:0px; top:0px; width:100%;height:100%; z-index:1; display:none;">
    <table height="100%" width="100%" border="-" cellspacing="0" cellpadding="0" style="text-align:center">
        <tr height="100%" width="100%">
            <td>
                <iframe name="frm_all" frameborder="0" marginheight="0" marginwidth="0" border="0" width="100%" height="100%" scrolling="auto"></iframe>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
	// 우편번호 찾기 화면을 넣을 element
	var daum_layer = document.getElementById('daum_layer');

	function emulAcceptCharset(form) {
		if (form.canHaveHTML) { // detect IE
			document.charset = form.acceptCharset;
		}
		return true;
	}
	
	/***************************/
	/** iOS 용 주소창 띄우기 메소드 **/
	/***************************/
	function ios_showAddressWindow(title, url) {
	
		var isiPhone = navigator.userAgent.toLowerCase().indexOf("iphone");
		var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
		var isiPod = navigator.userAgent.toLowerCase().indexOf("ipod");
		
		if (isiPhone > -1 || isiPad > -1 || isiPod > -1) {
		
	        var IOSframe = document.createElement('iframe');
	        IOSframe.style.display = 'none';
	        IOSframe.src = 'jscall://showAddressWindow/' + title + '/' + encodeURIComponent(url);
	        document.documentElement.appendChild(IOSframe);	
		}
	}
</script>	
<form name="pay_form" method="post" action="/pg/mobile/pp_cli_hub.php" accept-charset="EUC-KR" onsubmit="return emulAcceptCharset(this)">
<input type="hidden" name="AppUrl" value="circuspay://card_pay" />
<input type="hidden" name="req_tx"         value="<?=$req_tx?>">               <!-- 요청 구분          -->
<input type="hidden" name="res_cd"         value="<?=$res_cd?>">               <!-- 결과 코드          -->
<input type="hidden" name="tran_cd"        value="<?=$tran_cd?>">              <!-- 트랜잭션 코드      -->
<input type="hidden" name="ordr_idxx"      value="<?=$ordr_idxx?>">            <!-- 주문번호           -->
<input type="hidden" name="good_mny"       value="<?=$good_mny?>">             <!-- 휴대폰 결제금액    -->
<input type="hidden" name="good_name"      value="<?=$good_name?>">            <!-- 상품명             -->
<input type="hidden" name="buyr_name"      value="<?=$buyr_name?>">            <!-- 주문자명           -->
<input type="hidden" name="buyr_tel1"      value="<?=$buyr_tel1?>">            <!-- 주문자 전화번호    -->
<input type="hidden" name="buyr_tel2"      value="<?=$buyr_tel2?>">            <!-- 주문자 휴대폰번호  -->
<input type="hidden" name="buyr_mail"      value="<?=$buyr_mail?>">            <!-- 주문자 E-mail      -->
<input type="hidden" name="cash_yn"		   value="<?=$cash_yn?>">              <!-- 현금영수증 등록여부-->
<input type="hidden" name="enc_info"       value="<?=$enc_info?>">
<input type="hidden" name="enc_data"       value="<?=$enc_data?>">
<input type="hidden" name="use_pay_method" value="<?=$use_pay_method?>">
<input type="hidden" name="cash_tr_code"   value="<?=$cash_tr_code?>">

<input type="hidden" name="rcvr_name"       value="<?=$rcvr_name?>">    <!-- 수취인 이름 -->
<input type="hidden" name="rcvr_tel1"       value="<?=$rcvr_tel1?>">    <!-- 수취인 전화번호 -->
<input type="hidden" name="rcvr_tel2"       value="<?=$rcvr_tel2?>">    <!-- 수취인 휴대폰번호 -->
<input type="hidden" name="rcvr_mail"       value="<?=$rcvr_mail?>">    <!-- 수취인 E-Mail -->
<input type="hidden" name="rcvr_zipx"       value="<?=$rcvr_zipx?>">    <!-- 수취인 우편번호 -->
<input type="hidden" name="rcvr_add1"       value="<?=$rcvr_add1?>">    <!-- 수취인 주소 -->
<input type="hidden" name="rcvr_add2"       value="<?=$rcvr_add2?>">    <!-- 수취인 상세주소 -->

<!-- 추가 파라미터 -->
<input type="hidden" name="param_opt_1"	   value="<?=$param_opt_1?>">
<input type="hidden" name="param_opt_2"	   value="<?=$param_opt_2?>">
<input type="hidden" name="param_opt_3"	   value="<?=$param_opt_3?>">
</form>
<script src="/js/app/ui.js"></script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		