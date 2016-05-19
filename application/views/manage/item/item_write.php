<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$itemName = '';
	$itemShopCode = '';
	$itemCode = '';
	$pictureYn = 'N';
	$solesaleYn = 'N';
	$optContent = '';
	$expContent = '';
	$makContent = '';
	$refContent = '';
	$itemPrice = 0;
	$discountYn = 'N';
	$discountPrice = 0;
	$orgShopNum = 0;
	$tag = '';
	$isRefContentView = FALSE;
	$optionYn = 'N';
	$optCnt = 1;
	$optSubCnt = 1;
	$optTitle = '';
	$optSubTitle = '';
	$optSubPrice = 0;
	$optSubDt = array();
	$refContent = '';
	$approvalDate = '';
	$approvalUserName = '';
	$shopRefContent = $shopPolicySet['REFPOLICY_CONTENT'];
	$mallRefContent = $stdShopPolSet['REFPOLICY_CONTENT'];
	$rePresentYn = 'N';
	$viewYn = 'Y';
	$itemStatCodeNum = 0;
	$itemStatCodeTitle = '';
	$itemStatMemo = '';
	$maxBuyCount = 0;
	$stockFreeYn = 'N';
	$stockCount =0;
	$payAfterCancelYn = 'N';
	$payafterCancelMemo = '';
	$madeAfterRefundYn = 'N';
	$madeAfterRefundMemo = '';
	$madeAfterChangeYn = 'N';
	$madeAfterChangeMemo = '';
	$chargeType = 'M';
	$chargeTypeUpdateDate = '';
	$apprFirstReqDate = '';
	$modiReason = '';
	$originalItemNum = 0;
	$adYn = 'N';
	$readonly = (!$isAdmin) ? 'readonly' : '';
	$styleCss = (!$isAdmin) ? 'style="border:none;"' : '';
	$style = (!$isAdmin) ? 'border:none;' : '';
	
	$itemCharge = $stdItemCharge = intval($stdPolSet['ITEM_CHARGE']);
	$payCharge = $stdPayCharge = intval($stdPolSet['PAY_CHARGE']);
	$taxCharge = $stdTaxCharge = intval($stdPolSet['TAX_CHARGE']);
	
	//샵정책
	$chargeType = $shopPolicySet['CHARGE_TYPE'];
	$fixedCharge = $shopPolicySet['FIXED_CHARGE'];
	if ($chargeType == 'F')
	{
		//고정입점비 지정된 경우 아이템 수수료 0%
		$itemCharge = 0;
	}
	
	$flist = array();
	for($i=0; $i<($fileCnt+1); $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
		$flist[$i]['file_path'] = '';
		$flist[$i]['file_tmpname'] = '';
		$flist[$i]['thumb_yn'] = 'N';
		$flist[$i]['thumb_file_path'] = '';	
	}
	
	if ($pageMethod == 'writeform')
	{
		$isRefContentView = FALSE;
		$refContent = '';
		$shopRefContent = $polSet['REFPOLICY_CONTENT'];
		$mallRefContent = $stdPolSet['REFPOLICY_CONTENT'];
		$refPolCodeNum = $polSet['REFPOLICYCODE_NUM'];
		
		$chargeType = 'M';
		
		//샵정책
		$shopChargeType = $shopPolicySet['CHARGE_TYPE'];
		$shopFixedCharge = $shopPolicySet['FIXED_CHARGE'];
		if ($shopChargeType == 'F')
		{
			//고정입점비 지정된 경우 아이템 수수료 0%
			$itemCharge = 0;
			$chargeType = 'F';
		}		
		
		if (empty($refPolCodeNum)) $refPolCodeNum = '12040'; //Mall 정책 사용
		if ($refPolCodeNum == '12020')
		{
			//아이템 개별
			$isRefContentView = TRUE;	//textarea 보이게
			$refContent = '';
		}
		
		if ($refPolCodeNum == '12030')
		{
			//shop 정책 사용
			$isRefContentView = FALSE;	//textarea 보이지 않게
			$refContent = $shopRefContent;
		}
		
		if ($refPolCodeNum == '12040')
		{
			//circus 정책 사용
			$isRefContentView = FALSE;	//textarea 보이지 않게
			$refContent = $mallRefContent;
		}
	}
	else if (in_array($pageMethod, array('updateform', 'apprupdateform', 'denyupdateform', 'copywriteform', 'copyapprovalwriteform', 'modiupdateform')))
	{
		$itemName = $baseSet['ITEM_NAME'];
		$itemShopCode = $baseSet['ITEMSHOP_CODE'];
		$itemCode = $baseSet['ITEM_CODE'];
		$pictureYn = $baseSet['PICTURE_YN'];
		$solesaleYn = $baseSet['SOLESALE_YN'];
		$optContent = $baseSet['OPTION_CONTENT'];
		$expContent = $baseSet['EXPLAIN_CONTENT'];
		$makContent = $baseSet['MAKING_CONTENT'];
		$refContent = $baseSet['REFPOLICY_CONTENT'];
		$refPolCodeNum = $baseSet['REFPOLICYCODE_NUM'];
		$orgShopNum = $baseSet['SHOP_NUM'];	//아이템 작성 샵고유번호
		$optionYn = $baseSet['OPTION_YN'];
		$rePresentYn = $baseSet['REPRESENT_YN'];
		$viewYn = $baseSet['VIEW_YN'];	
		$itemPrice = $baseSet['ITEM_PRICE'];
		$discountYn = $baseSet['DISCOUNT_YN'];
		$discountPrice = $baseSet['DISCOUNT_PRICE'];
		$approvalDate = $baseSet['APPROVAL_DATE'];
		$approvalUserName = $baseSet['APPROVALUSER_NAME'];
		$itemStatCodeNum = $baseSet['ITEMSTATECODE_NUM'];
		$itemStatCodeTitle = $baseSet['ITEMSTATECODE_TITLE'];
		$itemStatMemo = $baseSet['ITEMSTATE_MEMO'];
		$maxBuyCount = $baseSet['MAXBUY_COUNT'];
		$stockFreeYn = $baseSet['STOCKFREE_YN'];
		$stockCount = $baseSet['STOCK_COUNT'];
		$payAfterCancelYn = $baseSet['PAYAFTER_CANCEL_YN'];
		$payafterCancelMemo = $baseSet['PAYAFTER_CANCEL_MEMO'];
		$madeAfterRefundYn = $baseSet['MADEAFTER_REFUND_YN'];
		$madeAfterRefundMemo = $baseSet['MADEAFTER_REFUND_MEMO'];
		$madeAfterChangeYn = $baseSet['MADEAFTER_CHANGE_YN'];
		$madeAfterChangeMemo = $baseSet['MADEAFTER_CHANGE_MEMO'];
		$chargeType = $baseSet['CHARGE_TYPE'];
		$itemCharge = intval($baseSet['ITEM_CHARGE']);
		$payCharge = intval($baseSet['PAY_CHARGE']);
		$taxCharge = intval($baseSet['TAX_CHARGE']);
		
		//샵정책
		$shopChargeType = $shopPolicySet['CHARGE_TYPE'];
		$shopFixedCharge = $shopPolicySet['FIXED_CHARGE'];
		if ($shopChargeType == 'F')
		{
			//고정입점비 지정된 경우 아이템 수수료 0%
			$itemCharge = 0;
		}		
		
		$chargeTypeUpdateDate = (!empty($baseSet['CHARGETYPE_UPDATE_DATE'])) ? substr($baseSet['CHARGETYPE_UPDATE_DATE'], 0, 10) : '';
		$apprFirstReqDate = $baseSet['APPROVAL_FIRSTREQ_DATE'];
		$adYn = $baseSet['AD_YN'];
		$modiReason = $baseSet['MODIFY_REASON'];
		$originalItemNum = $baseSet['ORIGINAL_ITEM_NUM'];
		
		$shopRefContent = $polSet['REFPOLICY_CONTENT'];
		$mallRefContent = $stdPolSet['REFPOLICY_CONTENT'];
		
		if (empty($refPolCodeNum)) $refPolCodeNum = '12040'; //Mall 정책 사용
		if ($refPolCodeNum == '12020')
		{
			//아이템 개별
			$isRefContentView = TRUE;	//textarea 보이게
		}
		else if ($refPolCodeNum == '12030')
		{
			//shop 정책 사용
			$isRefContentView = FALSE;	//textarea 보이지 않게
			$refContent = $shopRefContent;
		}
		else if ($refPolCodeNum == '12040')
		{
			//circus 정책 사용
			$isRefContentView = FALSE;	//textarea 보이지 않게
			$refContent = $mallRefContent;
		}
		
		if (isset($fileSet))
		{
			for($i=0; $i<count($fileSet); $i++)
			{
				$flist[$i]['num'] = $fileSet[$i]['NUM'];
				$flist[$i]['file_name'] = $fileSet[$i]['FILE_NAME'];
				$flist[$i]['file_tmpname'] = $fileSet[$i]['FILE_TEMPNAME'];
				$flist[$i]['file_path'] = $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME'];
				$flist[$i]['thumb_yn'] = $fileSet[$i]['THUMB_YN'];			
				$flist[$i]['thumb_file_path'] = ($fileSet[$i]['THUMB_YN'] == 'Y') ? str_replace('.', '_s.', $flist[$i]['file_path']) : ''; 
			}
		}
	
		$fileCnt = (count($fileSet) == 0) ? 1 : (count($fileSet) / 2);
	
		if (isset($tagSet))
		{
			for($i=0; $i<count($tagSet); $i++)
			{
				$tag .= $tagSet[$i]['TAG'].',';
			}
	
			$tag = (count($tagSet) > 0) ? substr($tag, 0, -1) : '';
		}
		
		if ($pageMethod == 'copywriteform')
		{
			$itemName = '';
			$itemShopCode = '';
			$itemCode = '';
			$itemStatCodeNum = 0;
			$itemStatCodeTitle = '';
			$itemStatMemo = '';
			$chargeTypeUpdateDate = '';
			$apprFirstReqDate = '';
			$approvalDate = '';
			$approvalUserName = '';	
		}
		
		if ($pageMethod == 'copyapprovalwriteform')
		{
			//아이템샵코드 중복을 피하기 위해 temp코드를 붙여준다
			$now = DateTime::createFromFormat('U.u', microtime(true));
			$tmpCode = $now->format("mdHisu");	//$now->format("m-d-Y H:i:s.u");
			$tmpCode = substr($tmpCode, 0, -2);			
			$itemShopCode .= '_'.$tmpCode;
		}
	}
	
	if (!isset($cateSet)) $cateSet = array();
	
	if (in_array($pageMethod, array('copywriteform')))
	{
		//복사등록 하는 경우 파일정보도 초기화 한다
		$fileCnt = 1;
		for($i=0; $i<($fileCnt+1); $i++)
		{	//파일배열 초기화
			$flist[$i]['num'] = '';
			$flist[$i]['file_name'] = '';
			$flist[$i]['file_path'] = '';
			$flist[$i]['file_tmpname'] = '';
			$flist[$i]['thumb_yn'] = 'N';
			$flist[$i]['thumb_file_path'] = '';	
		}
	}
	

	$addUrl = (!empty($currentPage) && ($currentPage > 0)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? '?cs=item'.$currentParam : '';
	
	$resetUrl = '/manage/item_m/writeform';
	$submitUrl = '/manage/item_m/write';	
	$listUrl = '/manage/item_m/list';
	$pageTitle = '전체 Item 현황';
	
	if ($pageMethod == 'updateform')
	{
		$resetUrl = '/manage/item_m/updateform/sno/'.$sNum.'/sino/'.$siNum;		
		$submitUrl = '/manage/item_m/update/sno/'.$sNum.'/sino/'.$siNum;
	}
	else if ($pageMethod == 'apprupdateform')
	{
		$resetUrl = '/manage/item_m/apprupdateform/sno/'.$sNum.'/sino/'.$siNum;
		$submitUrl = '/manage/item_m/apprupdate/sno/'.$sNum.'/sino/'.$siNum;
		$listUrl = '/manage/item_m/apprlist';
	}
	else if ($pageMethod == 'denyupdateform')
	{
		$resetUrl = '/manage/item_m/denyupdateform/sno/'.$sNum.'/sino/'.$siNum;
		$submitUrl = '/manage/item_m/denyupdate/sno/'.$sNum.'/sino/'.$siNum;
		$listUrl = '/manage/item_m/denylist';
	}
	else if ($pageMethod == 'copywriteform') //아이템 복사
	{
		$resetUrl = '/manage/item_m/copywriteform/sno/'.$sNum.'/sino/'.$siNum;
		$submitUrl = '/manage/item_m/copywrite/sno/'.$sNum.'/sino/'.$siNum;
	}
	else if ($pageMethod == 'copyapprovalwriteform') //수정 승인 요청 form
	{
		$pageTitle = '수정요청현황';
		$resetUrl = '/manage/item_m/copyapprovalwriteform/sno/'.$sNum.'/sino/'.$siNum;
		$submitUrl = '/manage/item_m/copyapprovalwrite/sno/'.$sNum.'/sino/'.$siNum;
	}	
	else if ($pageMethod == 'modiupdateform') //수정 확인 form
	{
		$pageTitle = '수정요청현황';
		$listUrl = '/manage/item_m/modilist';
		$submitUrl = '/manage/item_m/modiupdate/sno/'.$sNum.'/sino/'.$siNum;
	}	
	
	$listUrl .= $addUrl;
	$resetUrl .= $addUrl;
	$deleteUrl = '/manage/item_m/delete/sino/'.$siNum;
	$copyWriteFormUrl = '/manage/item_m/copywriteform/sno/'.$sNum.'/sino/'.$siNum;
	$copyAppWriteFormUrl = '/manage/item_m/copyapprovalwriteform/sno/'.$sNum.'/sino/'.$siNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>	
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
		    /*
	    	CKEDITOR.replace('opt_content',
    		{
    			width: '80%',
    			height: '200',
    			toolbar: 'Basic'
    		});

    		CKEDITOR.replace('exp_content',
    		{
    			width: '80%',
    			height: '200',
    			toolbar: 'Basic'
    		});

    		CKEDITOR.replace('mak_content',
    		{
    			width: '80%',
    			height: '200',
    			toolbar: 'Basic'
    		});
    		*/

			$( "#chargetype_update_date" ).datepicker({
				dateFormat: 'yy-mm-dd',
				prevText: '이전 달',
				nextText: '다음 달',
				monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
				monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
				dayNames: ['일','월','화','수','목','금','토'],
				dayNamesShort: ['일','월','화','수','목','금','토'],
				dayNamesMin: ['일','월','화','수','목','금','토'],
				showMonthAfterYear: true,
				yearSuffix: '년'    	
			});

			$("#charge_dateImg").click(function() { 
				$("#chargetype_update_date").datepicker("show");
			});    		

	        $(':radio[name="refund_policy"]').click(function() {
		        <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>
	        		return checkedDeny();
	        	<?}else{?>
		        	var selValue = $(this).val();
		        	if (selValue == "12020"){
		        		$("#refConAreaDisp").show();
		        		$("#refContentDisp").hide();		        	
		        	}else if (selValue == "12030"){
			        	$("#refContentDisp").empty().html(nl2br($("#hidden_shop_ref_content").val()));
			        	//CKEDITOR.instances.ref_content.setData('');
		        		$("#refConAreaDisp").hide();
		        		$("#refContentDisp").show();
		        	}else if (selValue == "12040"){
		        		$("#refContentDisp").empty().html(nl2br($("#hidden_mall_ref_content").val()));		        	
		        		$("#refConAreaDisp").hide();
		        		$("#refContentDisp").show();
		        	}
	        	<?}?>
	        });	

	        $(':radio[name="option_yn"]').click(function() {
		        <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>
        			return checkedDeny();
        		<?}else{?>		        
		        	var selValue = $(this).val();
		        	if (selValue == "Y"){
			        	$('.mg_b10').show();
		        	}else if (selValue == "N"){
		        		$('.mg_b10').hide();
		        	}
	        	<?}?>	        	
	        });	

	        $(':radio[name="charge_type"]').click(function() {
	        	var selValue = $(this).val();
	        	if (selValue == "I"){
	        		$('#item_charge').attr('readonly',false);
	        		$('#pay_charge').attr('readonly',false);
	        		$('#tax_charge').attr('readonly',false);
	        		$('#item_charge').css("border","solid 1px #e4e4e4");
	        		$('#pay_charge').css("border","solid 1px #e4e4e4");
	        		$('#tax_charge').css("border","solid 1px #e4e4e4");
	        	}else if (selValue == "M"){
	        		$('#item_charge').val($('#std_item_charge').val());
	        		$('#pay_charge').val($('#std_pay_charge').val());
	        		$('#tax_charge').val($('#std_tax_charge').val());
	        		$('#item_charge').css('border','none');
	        		$('#pay_charge').css('border','none');
	        		$('#tax_charge').css('border','none');
	        		$('#item_charge').attr('readonly',true);
	        		$('#pay_charge').attr('readonly',true);
	        		$('#tax_charge').attr('readonly',true);
	        	}
	        });
	    });	

		function sendItem(){
			var sel;
			var content;

			<?//if (!in_array($pageMethod, array('copyapprovalwriteform')) && !$isAdmin){?>
			/*
			if (trim($('#itemshop_code').val()) == ''){
				alert('Craft Shop 자체 아이템 코드번호를 입력해 주세요.');
				return;
			}	

			if (!IsEngNumber($('#itemshop_code').val())){
				alert('영문과 숫자를 조합하여 8자리로 구성해야 합니다.');
				return;
			}

			if (trim($('#itemshop_code').val()).length != 8){
				alert('영문과 숫자를 조합하여 8자리로 구성해야 합니다.');
				return;				
			}
			*/			
			<?//}?>
			
			if (trim($('#item_name').val()) == ''){
				alert('아이템 이름을 입력하세요.');
				return;
			}		

			if ($("input:checkbox[name='item_cate']").is(":checked") == false){
				alert("카레고리를 선택해 주세요1");
				return;
			}

			content = $('#exp_content').val(); //CKEDITOR.instances.exp_content.getData();
			if (content == ''){
				alert('아이템설명 내용을 입력하세요.');
				return;
			}	

			sel = $(':radio[name="refund_policy"]:checked').val();
			if (sel == '' || sel == undefined){
				alert('교환 및 환불정책을 선택하세요.');
				return;				
			}	

			if (trim($('#item_price').val()) == '' || trim($('#item_price').val()) == '0'){
				alert('판매단가를 입력하세요.');
				return;
			}

			if (!IsNumber($('#item_price').val())){
				alert('판매단가는 숫자만 입력하세요.');
				return;				
			}

			if (trim($('#maxbuy_count').val()) != ''){
				if (!IsNumber($('#maxbuy_count').val())){
					alert('최대구매수량은 숫자만 입력하세요.');
					return;				
				}
			}			

			sel = $(':radio[name="stockfree_yn"]:checked').val();
			if (sel == 'N'){
				if (trim($('#stock_count').val()) == '' || trim($('#stock_count').val()) == '0'){
					alert('재고를 수량입력으로 선택한 경우 수량을 입력하셔야 합니다.');
					return;
				}

				if (!IsNumber($('#stock_count').val())){
					alert('재고수량은 숫자만 입력하세요.');
					return;				
				}
			}

			sel = $(':radio[name="payafter_cancel_yn"]:checked').val();
			if (sel == 'N'){
				if (trim($('#payafter_cancel_memo').val()) == ''){
					alert('절대불가 선택시 불가사유를 입력하셔야 합니다.');
					return;
				}
			}

			sel = $(':radio[name="madeafter_refund_yn"]:checked').val();
			if (sel == 'N'){
				if (trim($('#madeafter_refund_memo').val()) == ''){
					alert('절대불가 선택시 불가사유를 입력하셔야 합니다.');
					return;
				}
			}	

			sel = $(':radio[name="madeafter_change_yn"]:checked').val();
			if (sel == 'N'){
				if (trim($('#madeafter_change_memo').val()) == ''){
					alert('절대불가 선택시 불가사유를 입력하셔야 합니다.');
					return;
				}
			}

			sel = $(':radio[name="charge_type"]:checked').val();
			if (sel == '' || sel == undefined){
				alert('수수료 운영형태를 선택하셔야 합니다.');
				return;
			}				

			if (trim($('#item_charge').val()) != ''){
				if (!IsNumber($('#item_charge').val())){
					alert('판매 수수료는 숫자만 입력하세요.');
					return;				
				}
			}

			if (trim($('#pay_charge').val()) != ''){
				if (!IsNumber($('#pay_charge').val())){
					alert('결제대행 수수료는 숫자만 입력하세요.');
					return;				
				}
			}

			if (trim($('#tax_charge').val()) != ''){
				if (!IsNumber($('#tax_charge').val())){
					alert('수수료 부가세는 숫자만 입력하세요.');
					return;				
				}
			}		

			<?if ($pageMethod == 'copyapprovalwriteform'){?>
			if (trim($('#modi_reason').val()) == ''){
				alert('수정 사유를 입력하세요.');
				return;
			}	
			<?}?>	

			$('#categrp').val(getCheckboxSelectedValue('item_cate'));

			//불필요한 업로드 항목 remove
			var fileIndex = $('#fileDisp').children().length;
			var fi = 0;
			for(i=0; i<=fileIndex;i++){
				fi = i * 2;	
				if (trim($('#userfile'+fi).val()) == '' && trim($('#userfile'+(fi+1)).val()) == '' && trim($('#userHfile'+fi).val()) == '' && trim($('#userHfile'+(fi+1)).val()) == ''){
					$('#fileDisp_' + i).remove();
				}
			} 

			if (trim($('#tag').val()) != trim($('#tag_org').val())){
				$('#tag_change_yn').val('Y');
			}
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();
		}

		function addFileDisp(n){
			//var fileIndex = $('#fileDisp div').children().length;
			var fileIndex = $('#fileDisp').children().length;		
			var html = "";
			var fi = fileIndex * 2;

			if ((fileIndex-1) > 8){
				alert('최대 8개까지 등록할 수 있습니다.');
				return;
			}

			html = "<tbody id=\"fileDisp_"+fileIndex+"\">";
			html += "<tr>";
			html += "	<th rowspan=\"2\" class=\"ag_c\">"+(fileIndex-1)+"</th>";
			html += "	<td class=\"ag_c va_m\"><div>PC 웹용</div><span class=\"red\">(000 x 000)</span></td>";
			html += "	<td>";
			html += "		<dl class=\"dl_img\">";
			html += "			<dt><img src=\"/images/adm/@thumb.gif\" width=\"100\" height=\"100\" alt=\"\" /></dt>";
			html += "			<dd>";
			html += "				<a href=\"#\" class=\"alink\"></a>";
			html += "			</dd>";
			html += "			<dd>";
			html += "				<input type=\"file\" id=\"userfile"+fi+"\" name=\"userfile"+fi+"\" class=\"inp_file\" value=\"파일찾기\" />";
			html += "			</dd>";
			html += "		</dl>";
			html += "	</td>";
			html += "	<td rowspan=\"2\" class=\"ag_c va_m\">";
			html += "		<a href=\"javascript:addFileDisp('"+fileIndex+"');\" class=\"btn2\">파일선택추가</a><br /><br />";
			html += "		<a href=\"javascript:delFileDisp('"+fileIndex+"');\" class=\"btn2\">파일선택삭제</a>";
			html += "	</td>";
			html += "</tr>";
			html += "<tr>";
			html += "	<td class=\"ag_c va_m\"><div>모바일앱용</div><span class=\"red\">(000 x 000)</span></td>";
			html += "	<td>";
			html += "		<dl class=\"dl_img\">";
			html += "			<dt><img src=\"/images/adm/@thumb.gif\" width=\"100\" height=\"100\" alt=\"\" /></dt>";
			html += "			<dd>";
			html += "				<a href=\"#\" class=\"alink\"></a>";
			html += "			</dd>";
			html += "			<dd>";
			html += "				<input type=\"file\" id=\"userfile"+(fi+1)+"\" name=\"userfile"+(fi+1)+"\" class=\"inp_file\" value=\"파일찾기\" />";
			html += "			</dd>";
			html += "		</dl>";
			html += "	</td>";
			html += "</tr>";
			html += "</tbody>";
	
			//$("input[name*='addfileBtn']").remove();			
			$('#fileDisp').append(html);
		}

		function delFileDisp(n){
			var fileIndex = $('#fileDisp').children().length;
			if (fileIndex == 0){
				alert('마지막은 삭제할 수 없습니다.');
				return;
			}

			var fi = n * 2;			
			if (trim($('#userfile'+fi).val()) != '' || trim($('#userfile'+(fi+1)).val()) != ''){
				alert('내용이 있는 경우 삭제할 수 없습니다.');
				return;
			} 

			$('#fileDisp_'+n).remove();
		}

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?\n저장하지 않은 다른 정보는 소실됩니다.')){
				var url = '/manage/item_m/filedelete/sino/<?=$siNum?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}

		function addOption(){
			var optDispIndex = $('#optDisp').children('table').length;
			var html = '';
		
			html += "<table id=\"optTbl_"+optDispIndex+"\" class=\"write2 cboth mg_t10\">";
			html += "<input type=\"hidden\" name=\"item_opt["+optDispIndex+"][order]\" value=\""+optDispIndex+"\" />";			
			html += "	<colgroup><col width=\"5%\"><col width=\"20%\"><col width=\"35%\"><col width=\"20%\"><col width=\"20%\"></colgroup>";
			html += "	<tbody>";
			html += "		<tr>";
			html += "			<th rowspan=\"3\">"+(optDispIndex+1)+"</th>";
			html += "			<th>옵션명</th>";
			html += "			<th>옵션구분</th>";
			html += "			<th>추가가격(원)</th>";
			html += "			<th><a href=\"javascript:delOption('"+optDispIndex+"', '0');\" class=\"btn1\">전체삭제</a></th>";
			html += "		</tr>";
			html += "		<tr>";
			html += "			<td>";
			html += "				<input type=\"text\" id=\"opt_"+optDispIndex+"\" name=\"item_opt["+optDispIndex+"][opt_title]\" value=\"\" class=\"inp_sty80\" />";
			html += "				<input type=\"hidden\" id=\"opt_"+optDispIndex+"_org\" name=\"item_opt["+optDispIndex+"][opt_title_org]\" value=\"\"/>";
			html += "			</td>";							
			html += "			<td colspan=\"3\">";
			html += "				<table id=\"optDisp_"+optDispIndex+"\" style=\"width:100%;\">";
			html += "					<colgroup><col width=\"45%\"><col width=\"25%\"><col width=\"25%\"></colgroup>";
			html += "					<tbody id=\"optSubDisp_"+optDispIndex+"_0\">";
			html += "						<tr>";
			html += "							<td style=\"border:none;\">";
			html += "								<input type=\"text\" id=\"opt_"+optDispIndex+"_0\" name=\"item_opt["+optDispIndex+"][0][optsub_title]\" value=\"\" class=\"inp_sty50\" />";
			html += "							</td>";
			html += "							<td style=\"border:none;\">";
			html += "								<input type=\"text\" id=\"optprice_"+optDispIndex+"_0\" name=\"item_opt["+optDispIndex+"][0][optsub_price]\" value=\"0\" class=\"inp_sty60\" />";
			html += "								<input type=\"checkbox\" id=\"optsoldout_"+optDispIndex+"_0\" name=\"item_opt["+optDispIndex+"][0][optsub_soldout]\" value=\"Y\"  class=\"inp_check\"/>품절";			
			html += "							</td>";
			html += "							<td style=\"border:none;\">";
			html += "								<a href=\"javascript:delSubOption('"+optDispIndex+"', '0');\" class=\"btn2\">삭제</a>";
			html += "								 <a href=\"javascript:addSubOption('"+optDispIndex+"', '0');\" class=\"btn2\">추가</a>";
			html += "							</td>";
			html += "						</tr>";
			html += "					</tbody>";
			html += "				</table>";
			html += "			</td>";
			html += "		</tr>";
			html += "	</tbody>";
			html += "</table>";

			$('#optDisp').append(html);
		}

		function delOption(n, cnt){
			//var optDispIndex = $('#optDisp').children('div').length;			
			//$('#optDisp_'+n).remove();
			if (cnt > 0){
				alert('이미 구매된 옵션이 있습니다.\n구매된 옵션이 있는 경우 전체를 삭제할 수 없습니다.');
				return;
			}
			$('#optTbl_'+n).remove();
		}

		function optTextCheck(n, m, cnt){
			if (cnt > 0){
				alert('이미 구매된 옵션 입니다.\n구매된 옵션이 있는 경우 내용을 변경할 수 없습니다.\n가격만 변경할 수 있습니다.');
				$('#opttitle_'+n+'_'+m).val($('#opttitle_'+n+'_'+m+'_org').val());				
				return;				
			}
		}

		function addSubOption(n, m, cnt){
			var optIndex = $('#optDisp_'+n+' tbody').length;
			var html = '';			
			
			html += "<tbody id=\"optSubDisp_"+n+"_"+optIndex+"\">";
			html += "<input type=\"hidden\" name=\"item_opt["+n+"]["+optIndex+"][sub_order]\" value=\""+optIndex+"\" />";			
			html += "	<tr>";
			html += "		<td style=\"border:none;\">";
			html += "			<input type=\"text\" id=\"opt_"+n+"_"+optIndex+"\" name=\"item_opt["+n+"]["+optIndex+"][optsub_title]\" value=\"\" class=\"inp_sty50\" />";
			html += "			<input type=\"hidden\" id=\"opttitle_"+n+"_"+optIndex+"_org\" name=\"item_opt["+n+"]["+optIndex+"][optsub_title_org]\" value=\"\"/>";
			html += "		</td>";
			html += "		<td style=\"border:none;\">";
			html += "			<input type=\"text\" id=\"optprice_"+n+"_"+optIndex+"\" name=\"item_opt["+n+"]["+optIndex+"][optsub_price]\" value=\"0\" class=\"inp_sty60\" />";
			html += "			<input type=\"checkbox\" id=\"optsoldout_"+n+"_"+optIndex+"\" name=\"item_opt["+n+"]["+optIndex+"][optsub_soldout]\" value=\"Y\"  class=\"inp_check\"/>품절";			
			html += "		</td>";
			html += "		<td style=\"border:none;\">";
			html += "			<a href=\"javascript:delSubOption('"+n+"', '"+optIndex+"', '0');\" class=\"btn2\">삭제</a>";
			html += "			 <a href=\"javascript:addSubOption('"+n+"', '"+optIndex+"', '0');\" class=\"btn2\">추가</a>";
			html += "		</td>";
			html += "	</tr>";
			html += "</tbody>";
			
			$('#optDisp_'+n).append(html);		
		}
				
		function delSubOption(n, m, cnt){
			var optIndex = $('#optDisp_'+n).children('tbody').length;
			if (optIndex == 0){
				alert('마지막 내용은 전체삭제를 이용하여 삭제해 주세요');
				return;
			}

			if (cnt > 0){
				alert('이미 구매된 옵션 입니다.\n구매된 옵션이 있는 경우 삭제할 수 없습니다.');
				return;				
			}
			
			$('#optSubDisp_'+n+'_'+m).remove();			
		}

		function copyNewItemWrite(){
			if (confirm('해당 아이템을 복사하여 등록하시겠습니까?')){
				location.href='<?=$copyWriteFormUrl?>';
			}
		}	

		function copyApprovalItemWrite(){
			if (confirm('해당 아이템을 수정 하시겠습니까?\n페이지 이동후 수정내용 적용하시고 승인 요청하시기 바랍니다.')){
				location.href='<?=$copyAppWriteFormUrl?>';
			}			
		}
		
		function itemDelete(){
			if (confirm('아이템을 삭제 하시겠습니까?')){
				var url = '<?=$deleteUrl?>';
				url += '/return_url/' + $.base64.encode('<?=$listUrl?>');				
				hfrm.location.href = url;
			}			
		}	

		function checkedDeny(){
			alert('수정할 수 없습니다.\n수정 내용을 등록후 승인을 받아야 합니다.');
			return false;			
		}   

		function copyApprovalItemRequest(){
			if (confirm('수정된 내용으로 승인 요청 하시겠습니까?\n승인 요청후에는 수정할 수 없습니다.')){
				sendItem();	
			}
		} 
	</script>
<!-- container -->
<div id="container">
    <form name="form" method="post" enctype="multipart/form-data">
    <input type="hidden" id="categrp" name="categrp"/>
    <input type="hidden" id="tag_change_yn" name="tag_change_yn" value="N"/>
    <input type="hidden" id="org_itemno" name="org_itemno" value="<?=$originalItemNum?>"/>
	<div id="content">

		<div class="title">
			<h2>[<?=$pageTitle?>]</h2>
			<div class="location">Home &gt; Item 관리 &gt; <?=$pageTitle?></div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col width="10%" /><col width="28%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="5">Item 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<?if (!in_array($pageMethod, array('writeform', 'copywriteform', 'copyapprovalwriteform'))){?>
				<tr>
					<th>Item 코드</th>
					<td colspan="4"><?=$itemCode?></td>
				</tr>
				<?}?>
				<tr style="display:none;">
					<!--  필요없는 항목 -->
					<th><span class="important">*</span>Craft Shop<br />Item 코드</th>
					<td colspan="4">
						<input type="text" id="itemshop_code" name="itemshop_code" value="<?=$itemShopCode?>" placeholder="코드는 8자리로 입력하세요" class="inp_sty70" <?if (!in_array($pageMethod, array('writeform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?>/>
					</td>
				</tr>
				<?if ($pageMethod == 'modiupdateform'){?>				
				<tr>
					<th><span class="important">*</span>수정 사유</th>
					<td colspan="4">
						<?=$modiReason?> <a href="/manage/item_m/updateform/sno/<?=$sNum?>/sino/<?=$originalItemNum?>" class="btn1" target="_blank">원본 아이템 보기</a>
					</td>
				</tr>
				<?}?>
				<tr>
					<th><span class="important">*</span>Item 명</th>
					<td colspan="4"><input type="text" id="item_name" name="item_name" value="<?=$itemName?>" class="inp_sty70" <?if (!in_array($pageMethod, array('writeform', 'copywriteform', 'copyapprovalwriteform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?> placehold="ex. [Shop명] Item 명"/></td>
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th rowspan="2"><span class="important">*</span>Item 카테고리</th>
					<td class="bg_c1">CIRCUS</td>
					<td colspan="3">
					<?
						$i = 1;
						foreach ($itCateSet as $crs):
							if ($crs['CATE_TYPE'] == 'M')	//mall 생성 카테고리
							{						
								$findKey = array_search($crs['NUM'], array_column($cateSet, 'CATE_NUM'));
								$sel_chk = (strlen($findKey) > 0) ? 'checked="checked"' : '';	//$cateSet[$findKey]['DEL_YN'];								
					?>
					<label><input type="checkbox" id="item_cate<?=$i?>" name="item_cate" value="<?=$crs['NUM']?>" class="inp_check" <?=$sel_chk?> /><span><?=$crs['CATE_TITLE']?></span></label>					
					<?
								$i++;
							}
						endforeach;
					?>						
					</td>
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<td class="bg_c1">Craft Shop</td>
					<td colspan="3">
					<?
						$i = 1;
						foreach ($itCateSet as $crs):
							if ($crs['CATE_TYPE'] == 'S')	//shop 생성 카테고리
							{						
								$findKey = array_search($crs['NUM'], array_column($cateSet, 'CATE_NUM'));
								$sel_chk = (strlen($findKey) > 0) ? 'checked="checked"' : '';	//$cateSet[$findKey]['DEL_YN'];								
					?>
					<label><input type="checkbox" id="item_cate<?=$i?>" name="item_cate" value="<?=$crs['NUM']?>" class="inp_check" <?=$sel_chk?> /><span><?=$crs['CATE_TITLE']?></span></label>					
					<?
								$i++;
							}
						endforeach;
					?>					
					</td>
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th>태그</th>
					<td colspan="4">
						<input type="text" id="tag" name="tag" value="<?=$tag?>" class="inp_sty70" />
						<input type="hidden" id="tag_org" name="tag_org" value="<?=$tag?>"/>
						<ul class="mg_t10">
							<li class="lh_16">※ Item 검색에 활용할 단어를 등록해 주십시오.</li>
							<li class="lh_16">※ 콤마(,)로 구분해 주십시오.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th>Craft Shop <br />대표상품 여부</th>
					<td colspan="2">
					<?
						//샵관리자 > Craft Shop관리>대표 Item 등록이 있으므로
						//여기서 지정 불가하게 수정
						//if ($isAdmin) //관리자인 경우
						//{					

						/*
						<label><input type="radio" id="represent_yn1" name="represent_yn" value="Y" <?if($rePresentYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>YES</span></label>
						<label><input type="radio" id="represent_yn2" name="represent_yn" value="N" <?if($rePresentYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>NO</span></label>
						*/

						//}
						//else 
						//{
							echo ($rePresentYn == 'Y') ? 'YES' : 'NO';
							echo '<input type="hidden" name="represent_yn" value="'.$rePresentYn.'"/>';
						//}
					?>					
					</td>
					<th>AD 여부</th>
					<td>
					<?
						if ($isAdmin) //관리자인 경우
						{					
					?>
						<label><input type="radio" id="ad_yn1" name="ad_yn" value="Y" <?if($adYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>YES</span></label>
						<label><input type="radio" id="ad_yn2" name="ad_yn" value="N" <?if(empty($adYn) || $adYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>NO</span></label>
					<?
						}
						else 
						{
							echo ($adYn == 'Y') ? 'YES' : 'NO';
							echo '<input type="hidden" name="ad_yn" value="'.$adYn.'"/>';
						}
					?>						
					</td>					
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th>독점판매 여부</th>
					<td colspan="2">
					<?
						if ($isAdmin) //관리자인 경우
						{					
					?>
						<label><input type="radio" id="solesale_yn1" name="solesale_yn" value="Y" <?if($solesaleYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>YES</span></label>
						<label><input type="radio" id="solesale_yn2" name="solesale_yn" value="N" <?if($solesaleYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>NO</span></label>
					<?
						}
						else 
						{
							echo ($solesaleYn == 'Y') ? 'YES' : 'NO';
							echo '<input type="hidden" name="solesale_yn" value="'.$solesaleYn.'"/>';
						}
					?>
					</td>
					<th>촬영신청 여부</th>
					<td>
						<label><input type="radio" id="picture_yn1" name="picture_yn" value="Y" <?if($pictureYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>YES</span></label>
						<label><input type="radio" id="picture_yn2" name="picture_yn" value="N" <?if($pictureYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>NO</span></label>
					</td>
				</tr>
				<tr>
					<th>승인일시</th>
					<td colspan="2"><?=$approvalDate?></td>
					<th>승인처리자</th>
					<td><?=$approvalUserName?></td>
				</tr>
				<tr>
					<th>진열상태</th>
					<td colspan="2">
					<?
						if (!in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform')))
						{					
					?>				
						<label><input type="radio" id="view_yn1" name="view_yn" value="Y" <?if($viewYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?>/><span>진열중</span></label>
						<label><input type="radio" id="view_yn2" name="view_yn" value="N" <?if($viewYn == 'N'){?>checked="checked"<?}?> class="inp_radio" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>진열안함</span></label>
					<?
						}
						else 
						{
							echo ($viewYn == 'Y') ? '진열중' : '진열안함';
							echo '<input type="hidden" name="view_yn" value="'.$viewYn.'"/>';
						}
					?>					
					</td>
					<th>판매상태</th>
					<td>
					<?
						if ($isAdmin && $pageMethod != 'writeform') //관리자인 경우
						{
							$i = 1;
							foreach ($itemStCdSet as $crs):
								$isListUp = FALSE;
								if ($pageMethod == 'modiupdateform')
								{
									if ($crs['NUM'] >= 7920 && $crs['NUM'] < 7970) $isListUp = TRUE;	
								}
								else 
								{
									if ($itemStatCodeNum >= 8060) //승인이상 단계인 경우
									{
										if ($crs['NUM'] >= 8060) $isListUp = TRUE;
									}
									else
									{
										if ($crs['NUM'] > 8000 && $crs['NUM'] <= 8060) $isListUp = TRUE;
									}
								}
							
								if ($isListUp)
								{
									$sel_chk = ($crs['NUM'] == $itemStatCodeNum) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="item_state<?=$i?>" name="item_state" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
									$i++;					
								}
							endforeach;
						}
						else 
						{
							$i = 1;
							$isTxtView = FALSE;
							foreach ($itemStCdSet as $crs):
								$isListUp = FALSE;
								if ($itemStatCodeNum >= 8060) //승인이상 단계인 경우
								{
									if ($crs['NUM'] == 8060 || $crs['NUM'] == 8080) $isListUp = TRUE;
								}
								
								if ($isListUp)
								{
									$isTxtView = TRUE;
									$sel_chk = ($crs['NUM'] == $itemStatCodeNum) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="item_state<?=$i?>" name="item_state" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								}
								$i++;					
							endforeach;
														
							if (!$isTxtView) //radio 선택사항이 안나오는 경우, 수정요청사항 확인시에도 텍스트로만
							{
								echo $itemStatCodeTitle;
								echo '<input type="hidden" name="item_state" value="'.$itemStatCodeNum.'"/>';
							}
						}
					?>		
					<input type="hidden" id="item_state_org" name="item_state_org" value="<?=$itemStatCodeNum?>"/>
					<input type="hidden" id="appr_firstreq_date" name="appr_firstreq_date" value="<?=$apprFirstReqDate?>"/>
					<?if (!in_array($pageMethod, array('writeform', 'copywriteform'))){?>
					<a href="javascript:itemHistorySearch('<?=$siNum?>', '');" class="btn1">진행내역 자세히 보기</a>
						<?if ($itemStatCodeNum > 8020){?>
						<br /><br />
						<span><textarea id="item_state_memo" name="item_state_memo" style="width:350px;height:60px;"><?=$itemStatMemo?></textarea></span>
						<span style="display:none;"><textarea id="item_state_memo_org" name="item_state_memo_org" style="width:350px;height:60px;"><?=$itemStatMemo?></textarea></span>
						<?}?>
					<?}?>														
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col /></colgroup>
			<thead>
				<tr>
					<th colspan="2">Item 상세정보</th>
				</tr>
			</thead>
			<tbody>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th><span class="important">*</span>Item 옵션</th>
					<td>
						<textarea id="opt_content" name="opt_content" class="textarea1"><?=$optContent?></textarea>
					</td>
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th><span class="important">*</span>Item 설명</th>
					<td><textarea id="exp_content" name="exp_content" class="textarea1"><?=$expContent?></textarea></td>
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th><span class="important">*</span>제작 및 예상도착일</th>
					<td><textarea id="mak_content" name="mak_content" class="textarea1"><?=$makContent?></textarea></td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>교환 및 환불 정책</th>
					<td>
					<?
						$i = 1;
						foreach ($refPlCdSet as $crs):
							$sel_chk = ($crs['NUM'] == $refPolCodeNum) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="refund_policy<?=$i?>" name="refund_policy" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio"/><span><?=$crs['TITLE']?></span></label>
					<?
							$i++;
						endforeach;					
					?>							
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<span id="refConAreaDisp" <?if (!$isRefContentView){?>style="display:none;"<?}?>>
							<textarea id="ref_content" name="ref_content" rows="10" cols="100"><?=$refContent?></textarea>
						</span>
						<span id="refContentDisp" <?if ($isRefContentView){?>style="display:none;"<?}?>><?=nl2br($refContent)?></span>
											
						<span id="hidden_ref_content_disp" style="display:none;">
							<br /><textarea id="hidden_mall_ref_content" rows="10" cols="10"><?=$mallRefContent?></textarea><br />
							<textarea id="hidden_shop_ref_content" rows="10" cols="10"><?=$shopRefContent?></textarea>
						</span>											
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10" id="fileDisp" <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
			<colgroup><col width="5%"><col width="10%"><col width="75%"><col width="10%"></colgroup>
			<thead>
				<tr>
					<th colspan="2">Item 이미지</th>
					<th colspan="2" class="ag_r">* 노출순서대로 차례로 등록해 주십시오. (<span class="red">최대 8</span>개)</th>
				</tr>
			</thead>
		    <?
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	
		    	if ($fileCnt > 0)
		    	{
		    		for($i=0; $i<$fileCnt; $i++)
		    		{
		    			$fi = $i * 2;
		    			if (!empty($flist[$fi]['file_tmpname']))
		    			{
		    				$imgUrl = ($flist[$fi]['thumb_yn'] == 'Y') ? $flist[$fi]['thumb_file_path'] : $flist[$i]['file_path'];
		    			}
		    			else 
		    			{
		    				$imgUrl = $defaultImg;
		    			}
		    ?>			
			<tbody id="fileDisp_<?=$i?>">
				<tr>
					<th rowspan="2" class="ag_c"><?=($i+1)?></th>
					<td class="ag_c va_m"><div>PC 웹용</div><span class="red">(000 x 000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/sino/<?=$siNum?>/fno/<?=$flist[$fi]['num']?>" class="alink"><?=$flist[$fi]['file_name']?></a> 
								<?if (!empty($flist[$fi]['file_name'])){?><a href="javascript:delFile('<?=$siNum?>','<?=$fi?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi?>" name="userfile<?=$fi?>" class="inp_file" value="파일찾기" />
								<input type="hidden" id="userHfile<?=$fi?>" name="userHfile<?=$fi?>" value="<?=$flist[$fi]['file_name']?>"/>								
							</dd>
						</dl>
					</td>
					<?if ($i==($fileCnt-1)){?>
					<td rowspan="2" class="ag_c va_m"><a href="javascript:addFileDisp('<?=$i?>');" class="btn2">파일선택추가</a></td>
					<?}else{?>
					<td rowspan="2" class="ag_c va_m"></td>					
					<?}?>
				</tr>
				<?
						if (!empty($flist[$fi+1]['file_tmpname']))
						{
							$imgUrl = ($flist[$fi+1]['thumb_yn'] == 'Y') ? $flist[$fi+1]['thumb_file_path'] : $flist[$i]['file_path'];
						}
						else
						{
							$imgUrl = $defaultImg;
						}				
				?>				
				<tr>
					<td class="ag_c va_m"><div>모바일앱용</div><span class="red">(000 x 000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/sino/<?=$siNum?>/fno/<?=$flist[$fi+1]['num']?>" class="alink"><?=$flist[$fi+1]['file_name']?></a> 
								<?if (!empty($flist[$fi+1]['file_name'])){?><a href="javascript:delFile(<?=$siNum?>,'<?=$fi+1?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi+1?>" name="userfile<?=$fi+1?>" class="inp_file" value="파일찾기" />
								<input type="hidden" id="userHfile<?=$fi+1?>" name="userHfile<?=$fi+1?>" value="<?=$flist[$fi+1]['file_name']?>"/>								
							</dd>
						</dl>
					</td>
				</tr>
			</tbody>
			<?
		    		}
				}
			?>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="88%"></colgroup>
			<thead>
				<tr>
					<th colspan="2">옵션정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>구매옵션</th>
					<td>
						<label><input type="radio" id="option_yn1" name="option_yn" value="Y" class="inp_radio" <?if($optionYn == 'Y'){?>checked="checked"<?}?> /><span>사용</span></label>
						<label><input type="radio" id="option_yn2" name="option_yn" value="N" class="inp_radio" <?if($optionYn == 'N'){?>checked="checked"<?}?> /><span>사용안함</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<!-- 옵션추가 -->
		<div class="mg_b10" <?if ($optionYn == 'N'){?>style="display:none;"<?}?>>
			<?if (in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) || $isAdmin){?>
			<a href="javascript:addOption();" class="btn1 mg_t10">옵션추가</a>
			<?}?>
			<div class=" fl_r mg_t10">※ 추가비용 0원으로 입력 시 표기되지 않습니다.</div>
			<div id="optDisp">
			<?
				if (isset($optSet)) $optCnt = count($optSet);
 				if ($optCnt == 0) $optCnt = 1;
				for($i=0; $i<$optCnt; $i++)
				{
					$buyCountAll = 0;
					if (isset($optSet[$i]['OPT_TITLE']))
					{
						$optTitle = $optSet[$i]['OPT_TITLE'];
						$buyCountAll = $optSet[$i]['buyCountAll'];
					}
					//echo '<br />옵션명고유번호'.$optSet[$i]['NUM'];
					//echo '<br />하위옵션구매수'.$optSet[$i]['buyCountAll'];
			?>
				<table id="optTbl_<?=$i?>" class="write2 cboth mg_t10">
				<input type="hidden" name="item_opt[<?=$i?>][order]" value="<?=$i?>" />				
					<colgroup><col width="5%"><col width="20%"><col width="35%"><col width="20%"><col width="20%"></colgroup>
					<tbody>
						<tr>
							<th rowspan="3"><?=$i+1?></th>
							<th>옵션명</th>
							<th>옵션구분</th>
							<th>추가가격(원)</th>
							<th>
							<?if (in_array($pageMethod, array('writeform', 'copyapprovalwriteform'))){?>							
								<a href="javascript:delOption('<?=$i?>', '<?=$buyCountAll?>');" class="btn1">전체삭제</a>
							<?}?>
							</th>
						</tr>
						<tr>
							<td>
								<input type="text" id="opt_<?=$i?>" name="item_opt[<?=$i?>][opt_title]" value="<?=$optTitle?>" class="inp_sty80" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?>/>
								<input type="hidden" id="opt_<?=$i?>_org" name="item_opt[<?=$i?>][opt_title_org]" value="<?=$optTitle?>"/>
							</td>
							<td colspan="3">
								<table id="optDisp_<?=$i?>" style="width:100%;">
									<colgroup><col width="45%"><col width="25%"><col width="25%"></colgroup>
									<?
										if (isset($optSet[$i]['optSubSet'])) $optSubCnt = count($optSet[$i]['optSubSet']);
										if ($optSubCnt == 0) $optSubCnt = 1;
										for($j=0; $j<$optSubCnt; $j++)
										{
											$buyCount = 0;
											$soldOutYn = 'N';
											if (isset($optSet[$i]['optSubSet'][$j]['OPTSUB_TITLE']))
											{
												$buyCount = $optSet[$i]['optSubSet'][$j]['BUY_COUNT'];
												$optSubTitle = $optSet[$i]['optSubSet'][$j]['OPTSUB_TITLE'];
												$optSubPrice = $optSet[$i]['optSubSet'][$j]['OPTION_PRICE'];
												$soldOutYn = $optSet[$i]['optSubSet'][$j]['SOLDOUT_YN'];
												//echo '<br />옵션고유번호'.$optSet[$i]['optSubSet'][$j]['SHOPITEM_OPTION_SUB_NUM'];
												//echo '<br />구매카운트'.$optSet[$i]['optSubSet'][$j]['BUY_COUNT'];
											}
									?>
									<tbody id="optSubDisp_<?=$i?>_<?=$j?>">
									<input type="hidden" name="item_opt[<?=$i?>][<?=$j?>][sub_order]" value="<?=$j?>" />									
										<tr>
											<td style="border:none;">
												<input type="text" id="opttitle_<?=$i?>_<?=$j?>" name="item_opt[<?=$i?>][<?=$j?>][optsub_title]" value="<?=$optSubTitle?>" onkeydown="javascript:optTextCheck('<?=$i?>', '<?=$j?>', '<?=$buyCount?>');" class="inp_sty50" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?>/>
												<input type="hidden" id="opttitle_<?=$i?>_<?=$j?>_org" name="item_opt[<?=$i?>][<?=$j?>][optsub_title_org]" value="<?=$optSubTitle?>"/>
											</td>
											<td style="border:none;">
												<input type="text" id="optprice_<?=$i?>_<?=$j?>" name="item_opt[<?=$i?>][<?=$j?>][optsub_price]" value="<?=$optSubPrice?>" class="inp_sty60" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?> />
												<input type="checkbox" id="optsoldout_<?=$i?>_<?=$j?>" name="item_opt[<?=$i?>][<?=$j?>][optsub_soldout]" value="Y" <?if ($soldOutYn == 'Y'){?>checked="checked"<?}?> class="inp_check" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?>/>품절										
												<input type="hidden" id="optprice_<?=$i?>_<?=$j?>_org" name="item_opt[<?=$i?>][<?=$j?>][optsub_price_org]" value="<?=$optSubPrice?>"/>
												<input type="hidden" id="optsoldout_<?=$i?>_<?=$j?>_org" name="item_opt[<?=$i?>][<?=$j?>][optsub_soldout_org]" value="<?=$soldOutYn?>"/>												
											</td>
											<td style="border:none;">
											<?if (in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) || $isAdmin){?>
												<a href="javascript:delSubOption('<?=$i?>', '<?=$j?>', '<?=$buyCount?>');" class="btn2">삭제</a>
												<a href="javascript:addSubOption('<?=$i?>', '<?=$j?>', '<?=$buyCount?>');" class="btn2">추가</a>
											<?}?>
											</td>
										</tr>
									</tbody>
									<?
										}
									?>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			<?
				}
			?>				
			</div>
		</div>
		<!-- //옵션추가 -->
		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="88%"></colgroup>
			<thead>
				<tr>
					<th colspan="2">판매정보</th>
				</tr>
			</thead>			
			<tbody>
				<tr>
					<th><span class="important">*</span>Item 판매단가</th>
					<td><input type="text" id="item_price" name="item_price" value="<?=$itemPrice?>" class="inp_sty20 va_m"<?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?> /> 원 <span class="dp1 mg_l20">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>할인여부 및<br /> 할인가격</th>
					<td>
						<label><input type="checkbox" id="discount_yn" name="discount_yn" value="Y" <?if($discountYn == 'Y'){?>checked="checked"<?}?> class="inp_check" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?>/><span>할인</span></label>
						<input type="text" id="discount_price" name="discount_price" value="<?=$discountPrice?>" class="inp_sty20 va_m" <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?> /> 원 <span class="dp1 mg_l20">* 숫자만 입력</span>
					</td>
				</tr>			
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th>1회 구매 시 <br /> 최대구매수량</th>
					<td><input type="text" id="maxbuy_count" name="maxbuy_count" value="<?=$maxBuyCount?>" class="inp_sty10 va_m" /> 개 <span class="dp1 mg_l20">* 정수로, 숫자만 입력</span></td>
				</tr>
				<tr <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
					<th>재고수량</th>
					<td>
						<label><input type="radio" id="stockfree_yn1" name="stockfree_yn" value="Y" <?if($stockFreeYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>무제한</span></label>
						<label><input type="radio" id="stockfree_yn2" name="stockfree_yn" value="N" <?if($stockFreeYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>수량입력</span></label>
						<input type="text" id="stock_count" name="stock_count" value="<?=$stockCount?>" class="inp_sty10 va_m" /> 개
						<p class="mg_t10">※ 입력하신 재고수량이 모두 판매되면 자동으로 ‘품절‘ 표시가 됩니다.</p>
					</td>
				</tr>
				<tr>
					<th>결제/입금확인 후<br /> 구매취소 여부</th>
					<td>
						<label><input type="radio" id="payafter_cancel_yn1" name="payafter_cancel_yn" value="Y" class="inp_radio" <?if($payAfterCancelYn == 'Y'){?>checked="checked"<?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>조건부 가능</span></label>
						<label><input type="radio" id="payafter_cancel_yn2" name="payafter_cancel_yn" value="N" class="inp_radio" <?if($payAfterCancelYn == 'N'){?>checked="checked"<?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>절대불가</span></label>
						<input type="text" id="payafter_cancel_memo" name="payafter_cancel_memo" value="<?=$payafterCancelMemo?>" class="inp_sty60 va_m" placeholder="고객에게 안내될 불가사유를 입력해 주십시오." <?if ($pageMethod == 'modiupdateform'){?><?=$styleCss?> <?=$readonly?><?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onkeydown="return checkedDeny();"<?}?>/>
					</td>
				</tr>
				<tr>
					<th>Itme 제작 완료 후<br /> 환불신청 여부</th>
					<td>
						<label><input type="radio" id="madeafter_refund_yn1" name="madeafter_refund_yn" value="Y" class="inp_radio" <?if($madeAfterRefundYn == 'Y'){?>checked="checked"<?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>조건부 가능</span></label>
						<label><input type="radio" id="madeafter_refund_yn2" name="madeafter_refund_yn" value="N" class="inp_radio" <?if($madeAfterRefundYn == 'N'){?>checked="checked"<?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>절대불가</span></label>
						<input type="text" id="madeafter_refund_memo" name="madeafter_refund_memo" value="<?=$madeAfterRefundMemo?>" class="inp_sty60 va_m" placeholder="고객에게 안내될 불가사유를 입력해 주십시오." <?if ($pageMethod == 'modiupdateform'){?><?=$styleCss?> <?=$readonly?><?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onkeydown="return checkedDeny();"<?}?> />
					</td>
				</tr>
				<tr>
					<th>Itme 제작 완료 후<br /> 교환요청 여부</th>
					<td>
						<label><input type="radio" id="madeafter_change_yn1" name="madeafter_change_yn" value="Y" class="inp_radio" <?if($madeAfterChangeYn == 'Y'){?>checked="checked"<?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>조건부 가능</span></label>
						<label><input type="radio" id="madeafter_change_yn2" name="madeafter_change_yn" value="N" class="inp_radio" <?if($madeAfterChangeYn == 'N'){?>checked="checked"<?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onclick="return checkedDeny()"<?}?> /><span>절대불가</span></label>
						<input type="text" id="madeafter_change_memo" name="madeafter_change_memo" value="<?=$madeAfterChangeMemo?>" class="inp_sty60 va_m" placeholder="고객에게 안내될 불가사유를 입력해 주십시오." <?if ($pageMethod == 'modiupdateform' && !$isAdmin){?><?=$styleCss?> <?=$readonly?><?}?> <?if (!in_array($pageMethod, array('writeform', 'copyapprovalwriteform')) && !$isAdmin){?>onkeydown="return checkedDeny();"<?}?> />
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10" <?if (in_array($pageMethod, array('copyapprovalwriteform', 'modiupdateform'))){?>style="display:none;"<?}?>>
			<colgroup><col width="12%"><col width="38%"><col width="12%"><col width="38%"></colgroup>
			<thead>
				<tr>
					<th colspan="4">수수료 정보</th>
				</tr>
			</thead>
			<input type="hidden" id="std_item_charge" value="<?=$stdItemCharge?>"/>
			<input type="hidden" id="std_pay_charge" value="<?=$stdPayCharge?>"/>
			<input type="hidden" id="std_tax_charge" value="<?=$stdTaxCharge?>"/>
			<tbody>
			<?
				$chargeType = (empty($chargeType)) ? 'I' : $chargeType;
				//$chargeType = 'I'; //개별수수료로만 적용
				if ($chargeType == 'M' && !$isAdmin) //M 몰(써커스)기준
				{
					$readonly = 'readonly';
					$styleCss = 'style="border:none;"';
					$style = 'border:none;';
				}
			?>
				<tr style="display:none;">
					<th><span class="important">*</span>수수료 운영형태</th>
					<td colspan="3">
						<label><input type="radio" id="charge_type1" name="charge_type" value="M" <?if($chargeType == 'M'){?>checked="checked"<?}?> class="inp_radio" /><span>전체 수수료</span></label>
						<label><input type="radio" id="charge_type2" name="charge_type" value="I" <?if(empty($chargeType) || $chargeType == 'I'){?>checked="checked"<?}?> class="inp_radio" /><span>Item 개별 수수료</span></label>
						<label><input type="radio" id="charge_type3" name="charge_type" value="F" <?if($chargeType == 'F'){?>checked="checked"<?}?> class="inp_radio" /><span>고정입점비</span></label>						
						<input type="hidden" name="charge_type_org" value="<?=$chargeType?>" />						
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>판매 수수료</th>
					<td>
						<input type="text" id="item_charge" name="item_charge" value="<?=$itemCharge?>" class="inp_sty20 va_m" <?=$styleCss?> <?=$readonly?>/> %
						<input type="hidden" name="item_charge_org" value="<?=$itemCharge?>" />
					</td>
					<th><span class="important">*</span>수수료 부가세</th>
					<td>
						<input type="text" id="tax_charge" name="tax_charge" value="<?=$taxCharge?>" class="inp_sty10 va_m" <?=$styleCss?> <?=$readonly?>/> %
						<input type="hidden" name="tax_charge_org" value="<?=$taxCharge?>" />
					</td>					
				</tr>
				<tr style="display:none;">
					<th><span class="important">*</span>결제대행 수수료</th>
					<td colspan="3">
						<input type="text" id="pay_charge" name="pay_charge" value="<?=$payCharge?>" class="inp_sty20 va_m" <?=$styleCss?> <?=$readonly?>/> %
						<input type="hidden" name="pay_charge_org" value="<?=$payCharge?>" />
					</td>
				</tr>
				<?
					if (!empty($approvalDate))
					{	//최초 등록시에는 적용일을 보여주지 않고
						//승인 이력이 있는 경우에만 적용일을 보여줌 
				?>
				<tr>
					<th><span class="important">*</span>수수료 적용일</th>
					<td colspan="3">
						<input type="text" id="chargetype_update_date" name="chargetype_update_date" value="<?=$chargeTypeUpdateDate?>" class="inp_sty10 va_m" readonly /><a id="charge_dateImg" class="calendar va_m"></a> <span class="dp1 mg_l20">※ 선택일자의 0시 부터 적용</span>
						<input type="hidden" id="chargetype_update_date_org" name="chargetype_update_date_org" value="<?=$chargeTypeUpdateDate?>"/>
					</td>
				</tr>
				<?
					}
				?>
			</tbody>
		</table>
		<?
			//샵선택없는 신규등록인 경우 샵정보를 보여줄 수 없다		
			if ((!in_array($pageMethod, array('writeform', 'copywriteform', 'copyapprovalwriteform', 'modiupdateform'))))
			{
		?>
		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="38%"><col width="12%"><col width="38%"></colgroup>
			<thead>
				<tr>
					<th colspan="4">Craft Shop 정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Shop 코드</th>
					<td><?=$shopBaseSet['SHOP_CODE']?></td>
					<th>판매자 구분</th>
					<td><?=$shopBaseSet['SELLERTYPECODE_TITLE']?></td>
				</tr>
				<tr>
					<th>Shop 명</th>
					<td colspan="3"><a href="/manager/shop_m/view/sno/<?=$shopBaseSet['NUM']?>" class="alink" target="_blank"><?=$shopBaseSet['SHOP_NAME']?></a></td>
				</tr>
				<tr>
					<th>작가</th>
					<td colspan="3"><?=$shopBaseSet['SHOPUSER_NAME']?> <?if ($isAdmin){?><a href="javascript:messageSend('<?=addslashes(htmlspecialchars($shopBaseSet['SHOP_NAME']))?>', '<?=$shopBaseSet['NUM']?>', 'shop');" class="btn2">메시지</a><a href="javascript:smsSend('<?=addslashes(htmlspecialchars($shopBaseSet['SHOP_NAME']))?>', '<?=$shopBaseSet['SHOP_MOBILE_DEC']?>');" class="btn2">SMS</a><?}?>
				</tr>
				<tr>
					<th>CIRCUS 담당자</th>
					<td colspan="3">이름:<?=$shopBaseSet['MANAGERUSER_NAME']?> / 전화번호:<?=$shopBaseSet['MANAGER_TEL_DEC']?> / 휴대폰 번호:<?=$shopBaseSet['MANAGER_MOBILE_DEC']?></td>
				</tr>
				<tr>
					<th>Shop 상태</th>
					<td colspan="3"><?=$shopBaseSet['SHOPSTATECODE_TITLE']?></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="38%"><col width="12%"><col width="38%"></colgroup>
			<thead>
				<tr>
					<th colspan="4">판매 내역</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 판매금액</th>
					<td colspan="3"><?=number_format($shopBaseSet['TOTSELL_AMOUNT'])?> 원</td>
				</tr>
				<tr>
					<th>총 건수</th>
					<td><?=number_format($shopBaseSet['TOTSELL_COUNT'])?> 건</td>
					<th>판매순위(건수)</th>
					<td>
					<?
						if (isset($itemStatsSet['SELLCOUNT_RANK']))
						{
							if ($itemStatsSet['SELLCOUNT_RANK'] > 0)
							{
								echo number_format($itemStatsSet['SELLCOUNT_RANK']).' 위 ';
								echo '('.$itemStatsSet['CREATE_DATE'].' 현재)';
							}
							else 
							{
								echo '집계중';
							}
						}
						else 
						{
							echo '집계중';
						}	
					?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%"><col width="38%"><col width="12%"><col width="38%"></colgroup>
			<thead>
				<tr>
					<th colspan="4">인기 지수</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>등록 Item / Flag수</th>
					<td>
						<?=number_format($shopBaseSet['TOTITEM_COUNT'])?>개 / <?=number_format($shopBaseSet['TOTITEMFLAG_COUNT'])?>건
						(Flag순위 : 
					<?
						if (isset($shopStatsSet['FLAGCOUNT_RANK']))
						{
							if ($shopStatsSet['FLAGCOUNT_RANK'] > 0)
							{
								echo number_format($shopStatsSet['FLAGCOUNT_RANK']).' 위 ';
								echo '- '.$shopStatsSet['CREATE_DATE'].' 현재';
							}
							else 
							{
								echo '집계중';
							}
						}
						else 
						{
							echo '집계중';
						}	
					?>				
						)		
					</td>
					<th>Shop Flag 수</th>
					<td><?=number_format($shopBaseSet['TOTFLAG_COUNT'])?>건</td>
				</tr>
			</tbody>
		</table>
		<?
			}
		?>
		<div class="btn_list">
			<a href="" class="btn1 fl_l">미리보기</a>
			<?if (in_array($pageMethod, array('view', 'updateform'))){?>
			<a href="javascript:copyNewItemWrite();" class="btn1 fl_l">복사후 신규등록</a>
			<a href="javascript:itemDelete();" class="btn2 fl_r">삭제</a>
			<?}?>			

			<?if (in_array($pageMethod, array('writeform', 'updateform', 'apprupdateform', 'copywriteform')) || $isAdmin){?>
			<a href="javascript:sendItem();" class="btn2 fl_r">저장</a>
			<?}?>
			<a href="<?=$listUrl?>" class="btn2 fl_r">목록</a>
		</div>

		<?if ($pageMethod == 'copyapprovalwriteform' && $pageMethod != 'modiupdateform' && !$isAdmin){?>
		<div class="btn_list">
			수정내용 반영 및 승인을 요청하시려면 오른쪽의 ‘저장 후 승인요청’ 버튼을 클릭해 주십시오.
			승인완료 후, 수정된 내용으로 Item을 진열/판매하시려면 동일한 Item(수정전 Item)은 반드시 판매중지 처리를 해 주십시오. <br />
			<input type="text" id="modi_reason" name="modi_reason" value="" placeholder="수정해야 하는 사유 입력" class="inp_sty40" />
			<a href="javascript:copyApprovalItemRequest();" class="btn3 fl_r">저장 후 승인요청</a>
		</div>
		<?}else if ($pageMethod != 'writeform' && $pageMethod != 'copywriteform' && $pageMethod != 'modiupdateform' && !$isAdmin){?>		
		<div class="btn_list">
			Item명, 교환 및 환불정책, 판매정보를 수정하시려면 수정된 내용에 대한 승인이 필요합니다.
			수정 및 승인을 요청하시려면 오른쪽의 ‘수정 및 승인요청’ 버튼을 클릭해 주십시오 
			<a href="javascript:copyApprovalItemWrite();" class="btn3 fl_r">수정 및 승인요청</a>
		</div>
		<?}?>		

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		