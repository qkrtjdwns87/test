<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$sellerType = 0;
	$shopName = '';
	$shopUserName = '';
	$userEmail = '';
	$shopEmail = '';
	$shopMobile = '';
	$shopTel = '';
	$managerUserNum = '';
	$coNum = '';
	$coName = '';
	$coCeoName = '';
	$coBizType = '';
	$coBizEvent = '';
	$coCeoEmail = '';
	$coTel = '';
	$coZip = '';
	$coAddr1 = '';
	$coAddr2 = '';
	$coAddrJibun = '';
	$coMailOrderNo = '';
	$managerUserNum = 0;
	$managerUserName = '';
	$managerTel = '';
	$managerMobile = '';
	$chargeType = 'I';
	$fixedCharge = 0;
	
	if ($pageMethod == 'updateform')
	{
		$sellerType = $baseSet['SELLERTYPECODE_NUM'];
		$shopName = $baseSet['SHOP_NAME'];
		$shopUserName = $baseSet['SHOPUSER_NAME'];
		$userEmail = $baseSet['USER_EMAIL_DEC'];
		$shopEmail = $baseSet['SHOP_EMAIL_DEC'];
		$shopTel = $baseSet['SHOP_TEL_DEC'];		
		$shopMobile = $baseSet['SHOP_MOBILE_DEC'];
		$managerUserNum = $baseSet['MANAGEUSER_NUM'];
		$coNum = $infoSet['CO_NUM'];
		$coName = $infoSet['CO_NAME'];
		$coCeoName = $infoSet['CO_CEONAME'];
		$coBizType = $infoSet['CO_BIZTYPE'];
		$coBizEvent = $infoSet['CO_BIZEVENT'];
		$coCeoEmail = $infoSet['CO_CEOEMAIL_DEC'];
		$coTel = $infoSet['CO_TEL_DEC'];
		$coZip = $infoSet['CO_ZIP_DEC'];
		$coAddr1 = $infoSet['CO_ADDR1_DEC'];
		$coAddr2 = $infoSet['CO_ADDR2_DEC'];
		$coAddrJibun = $infoSet['CO_ADDR_JIBUN_DEC'];
		$coMailOrderNo = $infoSet['CO_MAILORDER_NO'];
		$managerUserNum = $baseSet['MANAGEUSER_NUM'];
		$managerUserName = $baseSet['MANAGERUSER_NAME'];
		$managerTel = $baseSet['MANAGER_TEL_DEC'];
		$managerMobile = $baseSet['MANAGER_MOBILE_DEC'];
		$chargeType = $baseSet['CHARGE_TYPE'];
		$fixedCharge = $baseSet['FIXED_CHARGE'];
	}
	$managerChangeYn = ($managerUserNum > 0) ? 'Y' : 'N';
	
	$resetUrl = ($pageMethod == 'updateform') ? '/manage/shop_m/updateform/sno/'.$sNum : '/manage/shop_m/writeform';
	$submitUrl = ($pageMethod == 'updateform') ? '/manage/shop_m/reupdate/sno/'.$sNum : '/manage/shop_m/write';
	$listUrl = '/manage/shop_m/list';
	$listUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	//$listUrl .= (!empty($currentParam)) ? '?cs=shop'.$currentParam : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<script src="/js/jquery.browser.js"></script>	
	<script src="/js/jquery.iframe-auto-height.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	        $("#copyinfo_check").click(function(){
		        if ($(this).is(":checked")){
		        	$('#co_ceoname').val($('#shopuser_name').val());
		        	$('#co_tel1').val($('#shop_tel1').val())
		        	$('#co_tel2').val($('#shop_tel2').val())
		        	$('#co_tel3').val($('#shop_tel3').val())
		        	$('#co_ceoemail').val($('#shop_email').val())
		        }else{
		        	$('#co_ceoname').val('');
		        	$('#co_tel1').val('');
		        	$('#co_tel2').val('');
		        	$('#co_tel3').val('');
		        	$('#co_ceoemail').val('');
		        }
	        });	   
	    });	

		function sendShop(){
			var sel = $(':radio[name="seller_type"]:checked').val();
			if (sel == '' || sel == undefined){
				alert('판매자 구분을 선택하세요.');
				return;				
			}
			
			if (trim($('#shop_name').val()) == ''){
				alert('Shop 이름를 입력하세요.');
				return;
			}

			if (trim($('#shopuser_name').val()) == ''){
				alert('작가 이름를 입력하세요.');
				return;
			}			

			if (trim($('#user_email').val()) == ''){
				alert('계정 이메일 주소를 입력하세요.');
				return;
			}
						
			if (!IsEmail($('#user_email').val())){
				alert('올바른 계정 이메일 주소를 입력하세요.');
				return;
			}			
			
			if (trim($('#shop_email').val()) == ''){
				alert('이메일 주소를 입력하세요.');
				return;
			}
						
			if (!IsEmail($('#shop_email').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}

			if (trim($('#shop_mobile1').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (trim($('#shop_mobile2').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (trim($('#shop_mobile3').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (!IsNumber(trim($('#shop_mobile1').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#shop_mobile2').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#shop_mobile3').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}			

			if (trim($('#shop_tel1').val()) == ''){
				alert('대표번호를 입력하세요.');
				return;
			}

			if (trim($('#shop_tel2').val()) == ''){
				alert('대표번호를 입력하세요.');
				return;
			}

			if (trim($('#shop_tel3').val()) == ''){
				alert('대표번호를 입력하세요.');
				return;
			}

			if (!IsNumber(trim($('#shop_tel1').val()))){
				alert('대표번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#shop_tel2').val()))){
				alert('대표번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#shop_tel3').val()))){
				alert('대표번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if ($('#manager_change_yn').val() == 'N' || $('#manager_change_uno').val() == '0'){
				alert('관리 담당자를 지정하세요.');
				return;
			}

			if (trim($('#co_name').val()) == ''){
				alert('사업자명을 입력하세요.');
				return;
			}			

			if (trim($('#co_ceoname').val()) == ''){
				alert('대표자명을 입력하세요.');
				return;
			}

			if (trim($('#co_biztype').val()) == ''){
				alert('업태를 입력하세요.');
				return;
			}

			if (trim($('#co_bizevent').val()) == ''){
				alert('종목을 입력하세요.');
				return;
			}			

			if (trim($('#co_ceoemail').val()) == ''){
				alert('대표 이메일을 입력하세요.');
				return;
			}			

			if (!IsEmail($('#co_ceoemail').val())){
				alert('올바른 대표 이메일 주소를 입력하세요.');
				return;
			}

			if (trim($('#co_tel1').val()) == ''){
				alert('대표 전화번호를 입력하세요.');
				return;
			}	

			if (trim($('#co_tel2').val()) == ''){
				alert('대표 전화번호를 입력하세요.');
				return;
			}	

			if (trim($('#co_tel3').val()) == ''){
				alert('대표 전화번호를 입력하세요.');
				return;
			}

			if (!IsNumber(trim($('#co_tel1').val()))){
				alert('대표번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#co_tel2').val()))){
				alert('대표번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#co_tel3').val()))){
				alert('대표번호는 숫자만 입력할 수 있습니다.');
				return;
			}			

			if (trim($('#co_zip').val()) == ''){
				alert('사업장 소재지를 입력하세요.');
				return;
			}	

			if (trim($('#co_addr1').val()) == ''){
				alert('사업장 소재지를 입력하세요.');
				return;
			}	

			if (trim($('#co_addr2').val()) == ''){
				alert('사업장 소재지를 입력하세요.');
				return;
			}

			if (trim($('#co_mailorderno').val()) == ''){
				alert('통신판매업 번호를 입력하세요.');
				return;
			}							

			/*
			if (trim($('#co_mailorderno').val()) != ''){
				if (!IsNumber($('#co_mailorderno').val())){
					alert('통신판매업 번호는 숫자로만 입력하세요.');
					return;
				}
			}
			*/
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();
		}    
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post">
	<div id="content">

		<div class="title">
			<h2>[신규신청]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 신규신청</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col /></colgroup>
			<thead>
				<tr>
					<th colspan="2">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>판매자 구분</th>
					<td>
					<?
						$i = 1;
						foreach ($sellTyCdSet as $crs):
							$sel_chk = ($crs['NUM'] == $sellerType) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="seller_type<?=$i?>" name="seller_type" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
							$i++;
						endforeach;					
					?>							
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 명</th>
					<td><input type="text" id="shop_name" name="shop_name" value="<?=$shopName?>" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가</th>
					<td><input type="text" id="shopuser_name" name="shopuser_name" value="<?=$shopUserName?>" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>계정신청 (이메일)</th>
					<td>
					<?
						if ($pageMethod == 'updateform')
						{
							echo $userEmail
					?>
						<input type="hidden" id="user_email" name="user_email" value="<?=$userEmail?>" />					
					<?
						}
						else 
						{
					?>
						<input type="text" id="user_email" name="user_email" value="<?=$userEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span>
					<?
						}
					?>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 이메일</th>
					<td><input type="text" id="shop_email" name="shop_email" value="<?=$shopEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 휴대폰 번호</th>
					<td>
					<?
						$arrMb = explode('-', $shopMobile);
						$mbNum1 = (isset($arrMb) && count($arrMb)>0) ? $arrMb[0] : '';
						$mbNum2 = (isset($arrMb) && count($arrMb)>1) ? $arrMb[1] : '';
						$mbNum3 = (isset($arrMb) && count($arrMb)>2) ? $arrMb[2] : '';					
					?>
						<input type="text" id="shop_mobile1" name="shop_mobile1" value="<?=$mbNum1?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="shop_mobile2" name="shop_mobile2" value="<?=$mbNum2?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="shop_mobile3" name="shop_mobile3" value="<?=$mbNum3?>" class="inp_sty40" style="width:80px;" maxlength="4" />
						<span class="ex">*숫자만 입력</span>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 대표 번호</th>
					<td>
					<?
						$arrTel = explode('-', $shopTel);
						$telNum1 = (isset($arrTel) && count($arrTel)>0) ? $arrTel[0] : '';
						$telNum2 = (isset($arrTel) && count($arrTel)>1) ? $arrTel[1] : '';
						$telNum3 = (isset($arrTel) && count($arrTel)>2) ? $arrTel[2] : '';					
					?>
						<input type="text" id="shop_tel1" name="shop_tel1" value="<?=$telNum1?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="shop_tel2" name="shop_tel2" value="<?=$telNum2?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="shop_tel3" name="shop_tel3" value="<?=$telNum3?>" class="inp_sty40" style="width:80px;" maxlength="4" />
						<span class="ex">*숫자만 입력</span>
					</td>
				</tr>				
				<tr>
					<th><span class="important">*</span>CIRCUS 관리 담당자</th>
					<td>
						<span id="managerDisp">이름:<?=$managerUserName?> / 전화번호:<?=$managerTel?> / 휴대폰 번호:<?=$managerMobile?></span> <a href="javascript:managerChangeSearch();" class="btn1">찾아보기</a>
						<input type="hidden" id="manager_change_yn" name="manager_change_yn" value="<?=$managerChangeYn?>"/>
						<input type="hidden" id="manager_change_uno" name="manager_change_uno" value="<?=$managerUserNum?>"/>
						<input type="hidden" id="manager_no_org" name="manager_no_org" value="<?=$managerUserNum?>"/>						
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">사업자 정보</th>
					<th class="ag_r">
						<label><input type="checkbox" id="copyinfo_check" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>사업자번호</th>
					<td colspan="3">
					<?
						$arrCoNum = explode('-', $coNum);
						$coNum1 = (isset($arrCoNum) && count($arrCoNum)>0) ? $arrCoNum[0] : '';
						$coNum2 = (isset($arrCoNum) && count($arrCoNum)>1) ? $arrCoNum[1] : '';
						$coNum3 = (isset($arrCoNum) && count($arrCoNum)>2) ? $arrCoNum[2] : '';
					?>
						<input type="text" id="co_num1" name="co_num1" value="<?=$coNum1?>" class="inp_sty40" style="width:60px;" maxlength="3"/>-
						<input type="text" id="co_num2" name="co_num2" value="<?=$coNum2?>" class="inp_sty40" style="width:50px;" maxlength="2"/>-
						<input type="text" id="co_num3" name="co_num3" value="<?=$coNum3?>" class="inp_sty40" style="width:100px;" maxlength="5"/> <span class="ex">* 예시) 111-11-11111</span>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td><input type="text" id="co_name" name="co_name" value="<?=$coName?>" class="inp_sty40" /></td>
					<th><span class="important">*</span>대표자명</th>
					<td><input type="text" id="co_ceoname" name="co_ceoname" value="<?=$coCeoName?>" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td><input type="text" id="co_biztype" name="co_biztype" value="<?=$coBizType?>" class="inp_sty40" /></td>
					<th><span class="important">*</span>종목</th>
					<td><input type="text" id="co_bizevent" name="co_bizevent" value="<?=$coBizEvent?>" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3"><input type="text" id="co_ceoemail" name="co_ceoemail" value="<?=$coCeoEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 전화</th>
					<td colspan="3">
					<?
						$arrTel = explode('-', $coTel);
						$tel1 = (isset($arrTel) && count($arrTel)>0) ? $arrTel[0] : '';
						$tel2 = (isset($arrTel) && count($arrTel)>1) ? $arrTel[1] : '';
						$tel3 = (isset($arrTel) && count($arrTel)>2) ? $arrTel[2] : '';
					?>					
						<input type="text" id="co_tel1" name="co_tel1" value="<?=$tel1?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="co_tel2" name="co_tel2" value="<?=$tel2?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="co_tel3" name="co_tel3" value="<?=$tel3?>" class="inp_sty40" style="width:80px;" maxlength="4" />
						<span class="ex">* 숫자만 입력</span>
					</td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>사업장 소재지</th>
					<td colspan="3"><input type="text" id="co_zip" name="co_zip" value="<?=$coZip?>" class="inp_sty10" readonly/><a href="javascript:searchAddress('co_zip','co_addr1','co_addr2','co_addr_jibun');" class="btn1">우편번호 찾기</a></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="co_addr1" name="co_addr1" value="<?=$coAddr1?>" class="inp_sty80" readonly/></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">
						<input type="text" id="co_addr2" name="co_addr2" value="<?=$coAddr2?>" class="inp_sty80"/>
						<input type="hidden" id="co_addr_jibun" name="co_addr_jibun" value="<?=$coAddrJibun?>"/>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>통신판매업 번호</th>
					<td colspan="3"><input type="text" id="co_mailorderno" name="co_mailorderno" value="<?=$coMailOrderNo?>" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="<?=$resetUrl?>" class="btn1">초기화</a>
			<a href="javascript:sendShop();" class="btn1">다음</a>			
		</div>
		
	</div>
	</form>
</div>
<!--// container -->
		
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		