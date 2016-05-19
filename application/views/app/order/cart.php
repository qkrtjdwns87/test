<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$shopCnt = count($recordSet);
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	

		var shopCnt = <?=$shopCnt?>;
		
		function minusItem(i, t){
			var qty = parseInt($('#quantDisp_'+i+'_'+t).text());
			var qty = parseInt($('#quantDisp_'+i+'_'+t).text());
			var maxBuyCnt = parseInt($('#maxbuycnt_'+i+'_'+t).val());
			var stockCnt = parseInt($('#stockcnt_'+i+'_'+t).val());
			var price = parseInt($('#price_'+i+'_'+t).val());
			var optprice = parseInt($('#optprice_'+i+'_'+t).val());
			var stockfree = $('#stockfree_'+i+'_'+t).val();			
			if (qty == 1){return;}
			qty--;
			var amount = (qty * price) + (qty * optprice);
			$('#quantDisp_'+i+'_'+t).text(qty);
			$('#amtDisp_'+i+'_'+t).text(setComma(amount)+'원');
			$('#amount_'+i+'_'+t).val(amount);
			reCalcuCart();			
		}

		function plusItem(i, t){
			var qty = parseInt($('#quantDisp_'+i+'_'+t).text());
			var maxBuyCnt = parseInt($('#maxbuycnt_'+i+'_'+t).val());
			var stockCnt = parseInt($('#stockcnt_'+i+'_'+t).val());
			var price = parseInt($('#price_'+i+'_'+t).val());
			var optprice = parseInt($('#optprice_'+i+'_'+t).val());
			var stockfree = $('#stockfree_'+i+'_'+t).val();

			if (stockfree == 'N' && stockCnt == qty){
				alert('재고가 없습니다.');
				return;
			}

			if (maxBuyCnt > 0 && maxBuyCnt == qty){
				alert('최대 구매수량을 초과할 수 없습니다.\n최대 구매수량은 '+maxBuyCnt+'개 입니다.');
				return;				
			}
			qty++;
			var amount = (qty * price) + (qty * optprice);
			$('#quantDisp_'+i+'_'+t).text(qty);
			$('#quntity_'+i+'_'+t).val(qty);
			$('#amtDisp_'+i+'_'+t).text(setComma(amount)+'원');
			$('#amount_'+i+'_'+t).val(amount);
			reCalcuCart();
		}

		function reCalcuCart(){
			var tot_damount = 0;
			var tot_samount = 0;
			for(var i=0; i<shopCnt; i++){
				var amount = 0;
				if ($('#dispdelyn_'+i).val() != 'Y'){
					tot_damount += parseInt($('#delivprice_'+i).val());
				}
				for(var t=0; t<$('#shopDisp_'+i+' dl').length; t++){
					if ($('#dispdelyn_'+i+'_'+t).val() != 'Y'){
						amount += parseInt($('#amount_'+i+'_'+t).val());
					}
				}
				tot_samount += amount;  		
				$('#samountDisp_'+i).empty().html('<strong>'+setComma(amount)+'원</strong>');		
			}
			$('#totsamtDisp').text(setComma(tot_samount)+'원');
			$('#totDelivDisp').text(setComma(tot_damount)+'원');
			$('#totamtDisp').text(setComma(tot_samount+tot_damount)+'원');
		}	

		function delItem(i, t){
			if ($('#shopDisp_'+i+' dl:not(:hidden)').length == 1){
				$('#shopDisp_'+i).hide();
				$('#dispdelyn_'+i).val('Y');
				$('#dispdelyn_'+i+'_'+t).val('Y');
			}else{
				$('#itemDisp_'+i+'_'+t).hide();		
				$('#dispdelyn_'+i+'_'+t).val('Y');		
			}

			if ($('.cart_list:not(:hidden)').length == 0){ //모두 삭제되는 경우
				$('.cart').hide();
				$('.buy_box').hide();
				$('#error_popup').show();
			}			
			reCalcuCart();
			//return;
		    var param = '?cs=cart&crno='+$('#cartno_'+i).val()+'&critno='+$('#cartitemno_'+i+'_'+t).val();
		    $.ajax({
		        url: '/app/order_a/cartdelete'+param,
		        type: 'POST',
		        contentType: 'application/json; charset=utf-8',
		        dataType: 'text',
		        success: function (response) {
		        	var data = eval(response);
		        	//alert(data);
		        }
		    });			
		}	

		function checkDel(){
			for(var i=0; i<shopCnt; i++){
				if ($('input:checkbox[id=cart_chk_'+i+']').is(':checked') == true){ //상위 체크된 경우
					for(var t=0; t<$('#shopDisp_'+i+' dl').length; t++){
						delItem(i, t);
					}					
				}else{
					for(var t=0; t<$('#shopDisp_'+i+' dl').length; t++){
						if ($('input:checkbox[id=cart_choice_'+i+'_'+t+']').is(':checked') == true){
							delItem(i, t);
						}
					}					
				}	
			}
		}

		function checkCartChecked(i, t){
			/*
			var isChecked = false;
			for(var t=0; t<$('#shopDisp_'+i+' dl').length; t++){
				if ($('input:checkbox[id=cart_choice_'+i+'_'+t+']').is(':checked') == true){
					isChecked = true;
					break;
				}
			}

			if (isChecked){
				$("input[id=cart_chk_"+i+"]").prop("checked",true);
			}else{
				$("input[id=cart_chk_"+i+"]").prop("checked",false);				
			}
			*/
		}

		function orderPart(){
			var cartVal = getCheckboxSelectedValue2('cart_chk_');
			var itemVal = getCheckboxSelectedValue2('cart_choice_');
			if (cartVal == '' && itemVal == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
			sendOrder();
		}

		function orderAll(){
			$("input[id^=cart_chk_]").prop("checked",true);
			$("input[id^=cart_choice_]").prop("checked",true);
			var cartVal = getCheckboxSelectedValue2('cart_chk_');
			var itemVal = getCheckboxSelectedValue2('cart_choice_');
			if (cartVal == '' && itemVal == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}					
			sendOrder();
		}

		function sendOrder(){
			document.form.target = 'hfrm';
			document.form.action = "/app/order_a/cartorder";
			document.form.submit();	
		}
	</script>
</head>
<body>
<div id="wrap">
	<form name="form" method="post">
	<!-- 장바구니_목록없음 -->
	<section id="error_popup" <?if ($recordSet){?>style="display:none;"<?}?>>
		<strong>장바구니가 비어있습니다.</strong>
		<!-- <p class="type2">아직 마음에 드는 ITEM을 못 만나셨나요?</p> -->

		<div class="btn_list">
			<a href="javascript:app_moveToBestItem();" class="btn_red">제품 보러가기</a>
			<!-- <a href="javascript:app_moveToBestItem();" class="btn_black">Best Item보러가기</a> -->
			<!-- <a href="javascript:app_moveToSearch();" class="btn_red">Item & Craft Shop <br />키워드로 검색하기</a> -->
		</div>
	</section>
	
	<div id="buy_container" class="cart" <?if (!$recordSet){?>style="display:none;"<?}?>>
		<!-- 장바구니 -->
		<section id="cart_detail">
	    <?
	    	$itemCnt = 0;
	    	$i = 0;
	    	$totAmount = 0; //전체 구매금액
	    	$totPrice = 0; //순수금액 합계
	    	$totQuantity = 0;
	    	$totOptionPrice = 0;
	    	$totShopAmount = 0; //샵별 합산 금액
	    	$totDeliveryPrice = 0; //전체 배송 금액
	    	$defaultImg = '';
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
	    		$deliverPrice = $rs['DELIVERY_PRICE'];
		?>		
			<div id="shopDisp_<?=$i?>" class="cart_list">
				<ul>
					<li>
						<p class="title"><input type="checkbox" id="cart_chk_<?=$i?>" name="cart[<?=$i?>][checkyn]" value="Y" onclick="javascript:AllCheckBoxCheck2('cart_choice_<?=$i?>',this.id);" /><label for="cart_chk"><?=$rs['SHOP_NAME']?></label></p>
				<?
					$t = 0;
					$shopAmount = 0; //샵별 합계총금액
					$shopPrice = 0; //샵별순수 합계금액
					$shopOptPrice = 0; //샵별옵션 합계금액
					$shopQuantity = 0;
					$fileName = '';
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
							$isSoldOut = 'Y';
							$stockCnt = 0;
						}
						else 
						{
							$isSoldOut = 'N';
							$stockCnt = ($stockFreeYn == 'Y') ? 100000 : $irs['STOCK_COUNT'];
						}
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
				?>	
						<dl id="itemDisp_<?=$i?>_<?=$t?>">
							<dt><?=$irs['ITEM_NAME']?> <a href="javascript:delItem('<?=$i?>', '<?=$t?>');" class="btn_del">삭제</a></dt>
							<dd class="photo">
								<input type="checkbox" id="cart_choice_<?=$i?>_<?=$t?>" name="cart[<?=$i?>][item][<?=$t?>][checkyn]" value="Y" onclick="javascript:checkCartChecked('<?=$i?>', '<?=$t?>');" />
								<div class="thumb"><label for="cart_choice1"><img src="<?=$fileName?>" alt="" /></label></div>
							</dd>
							<?if ($irs['OPTION_YN'] == 'Y'){?>
							<dd class="s_name"><?=$optTitle?></dd>
							<?}?>
							<dd class="quantity">
								<a href="javascript:minusItem('<?=$i?>', '<?=$t?>');"><img src="/images/app/cart/btn_minus.png" alt="빼기" /></a>
								<p id="quantDisp_<?=$i?>_<?=$t?>" class="num"><?=$quantity?></p>
								<a href="javascript:plusItem('<?=$i?>', '<?=$t?>');"><img src="/images/app/cart/btn_plus.png" alt="더하기" /></a>
							</dd>
							<dd id="amtDisp_<?=$i?>_<?=$t?>" class="price"><?=number_format($amount)?><span>원</span></dd>
							<input type="hidden" id="cartitemno_<?=$i?>_<?=$t?>" name="cart[<?=$i?>][item][<?=$t?>][no]" value="<?=$irs['NUM']?>"/>
							<input type="hidden" id="dispdelyn_<?=$i?>_<?=$t?>" name="cart[<?=$i?>][item][<?=$t?>][delyn]" value="N"/>
							<input type="hidden" id="quntity_<?=$i?>_<?=$t?>" name="cart[<?=$i?>][item][<?=$t?>][quantity]" value="<?=$quantity?>"/>
							
							<input type="hidden" id="price_<?=$i?>_<?=$t?>" value="<?=$price?>"/>
							<input type="hidden" id="optprice_<?=$i?>_<?=$t?>" value="<?=$optPrice?>"/>
							<input type="hidden" id="amount_<?=$i?>_<?=$t?>" value="<?=$amount?>"/>							
							<input type="hidden" id="soldoutyn_<?=$i?>_<?=$t?>" value="<?=$isSoldOut?>"/>
							<input type="hidden" id="stockcnt_<?=$i?>_<?=$t?>" value="<?=$stockCnt?>"/>
							<input type="hidden" id="stockfree_<?=$i?>_<?=$t?>" value="<?=$stockFreeYn?>"/>
							<input type="hidden" id="maxbuycnt_<?=$i?>_<?=$t?>" value="<?=$maxBuyCount?>"/>
							
						</dl>
				<?
						$shopAmount = $shopAmount + $amount;					
						$t++;
					endforeach;
					
					$itemCnt = $itemCnt + $t;
					$totDeliveryPrice = $totDeliveryPrice + $deliverPrice;
					$totShopAmount = $totShopAmount + $shopAmount;						
				?>
					</li>
					<li id="priceboxDisp_<?=$i?>" class="price_box">
						<span class="price1">배송비 <?=number_format($deliverPrice)?>원</span>
						<span class="price2"><span class="icn">&gt;&gt;</span>구매금액 <span id="samountDisp_<?=$i?>" class="won"><strong><?=number_format($shopAmount)?></strong>원</span></span>
						<p>주문 요청사항</p>
						<textarea id="ordcontent_<?=$i?>" name="cart[<?=$i?>][content]" rows="5" cols="5" placeholder="주문요청사항"></textarea>
						<input type="hidden" id="cartno_<?=$i?>" name="cart[<?=$i?>][no]" value="<?=$rs['NUM']?>"/>
						<input type="hidden" id="dispdelyn_<?=$i?>" name="cart[<?=$i?>][delyn]" value="N"/>
						<input type="hidden" id="delivprice_<?=$i?>" value="<?=$deliverPrice?>"/>
						<input type="hidden" id="samount_<?=$i?>" value="<?=$totShopAmount?>"/>
					</li>
				</ul>
			</div>
		<?
				$i++;
			endforeach;
			
			$totAmount = $totShopAmount + $totDeliveryPrice;		
		?>	

			<div id="cart_detail_price">
				<dl class="info_style2">
					<dt>구매금액</dt>
					<dd id="totsamtDisp"><?=number_format($totShopAmount)?>원</dd>
					<dt>배송비</dt>
					<dd id="totDelivDisp"><?=number_format($totDeliveryPrice)?>원</dd>
					<dt class="end">총 결제금액</dt>
					<dd class="end"><span id="totamtDisp" class="red"><?=number_format($totAmount)?>원</span></dd>
				</dl>
			</div>
		</section>
	</div>
	</form>

	<!-- 메뉴바 -->
	<div class="buy_box" <?if (!$recordSet){?>style="display:none;"<?}?>>
		<ul class="btn3 cart">
			<li><a href="javascript:checkDel();" class="normal">선택 삭제</a></li>
			<li><a href="javascript:orderPart();" class="normal">선택 구매</a></li>
			<li><a href="javascript:orderAll();" class="emphasis">전체 구매</a></li>
		</ul>
	</div>
	<!-- //메뉴바 -->
</div>
<script src="/js/app/ui.js"></script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		