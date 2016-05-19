<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$isRefContentView = FALSE;
	$readonly = (!$isAdmin) ? 'readonly' : '';
	$styleCss = (!$isAdmin) ? 'style="border:none;"' : '';
	$style = (!$isAdmin) ? 'border:none;' : '';
	$refContent = '';
	$shopRefContent = $polSet['REFPOLICY_CONTENT'];
	$mallRefContent = $stdPolSet['REFPOLICY_CONTENT'];
	$refPolCodeNum = $polSet['REFPOLICYCODE_NUM'];
	
	if (empty($refPolCodeNum)) $refPolCodeNum = '12040';
	if ($refPolCodeNum == '12020')
	{
		//아이템 개별
		$isRefContentView = FALSE;	//textarea 보이지 않게
		$refContent = '';
	}
	else if ($refPolCodeNum == '12030')
	{
		//shop 정책 사용
		$isRefContentView = TRUE;	//textarea 보이게
		$refContent = $shopRefContent;
	}
	else if ($refPolCodeNum == '12040')
	{
		//circus 정책 사용
		$isRefContentView = FALSE;	//textarea 보이지 않게
		$refContent = $mallRefContent;
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
	
	if (isset($profileFileSet))
	{
		for($i=0; $i<count($profileFileSet); $i++)
		{
			$flist[$i]['num'] = $profileFileSet[$i]['NUM'];
			$flist[$i]['file_name'] = $profileFileSet[$i]['FILE_NAME'];
			$flist[$i]['file_tmpname'] = $profileFileSet[$i]['FILE_TEMPNAME'];
			$flist[$i]['file_path'] = $profileFileSet[$i]['FILE_PATH'].$profileFileSet[$i]['FILE_TEMPNAME'];
			$flist[$i]['thumb_yn'] = $profileFileSet[$i]['THUMB_YN'];
			$flist[$i]['thumb_file_path'] = ($profileFileSet[$i]['THUMB_YN'] == 'Y') ? str_replace('.', '_s.', $flist[$i]['file_path']) : '';
		}
		
		//$fileCnt = (count($profileFileSet) == 0) ? 1 : (count($profileFileSet) / 2);
	}
	
	$listUrl = '/manage/shop_m/list';
	$listUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$listUrl .= (!empty($currentParam)) ? $currentParam : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/ckeditor/ckeditor.js"></script>	
	<script src="/js/jquery.browser.js"></script>	
	<script src="/js/jquery.iframe-auto-height.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
			$( "#s_date, #e_date" ).datepicker({
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

	    	CKEDITOR.replace('profile_content',
	       	{
	       		width: '80%',
	       		height: '350',
	       		toolbar: 'Basic'
	       	});			

			$("#sdateImg").click(function() { 
				$("#s_date").datepicker("show");
			});
			$("#edateImg").click(function() { 
				$("#e_date").datepicker("show");
			});			
					    
	        $(':radio[name="refund_policy"]').click(function() {
	        	var selValue = $(this).val();
	        	if (selValue == "12020"){
	        		$("#refConAreaDisp").hide();
	        		$("#refContentDisp").hide();		        	
	        	}else if (selValue == "12030"){
		        	$("#ref_content").empty().text($("#hidden_shop_ref_content").val());
		        	//CKEDITOR.instances.ref_content.setData('');
	        		$("#refConAreaDisp").show();
	        		$("#refContentDisp").hide();
	        	}else if (selValue == "12040"){
	        		$("#refContentDisp").empty().text($("#hidden_mall_ref_content").val());		        	
	        		$("#refConAreaDisp").hide();
	        		$("#refContentDisp").show();
	        	}
	        });	

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

	        $("#refundaddr_check").click(function(){
		        if ($(this).is(":checked")){
		        	$('#refund_zip').val($('#co_zip').val());
		        	$('#refund_addr1').val($('#co_addr1').val())
		        	$('#refund_addr2').val($('#co_addr2').val())
		        	$('#refund_addr_jibun').val($('#co_addr_jibun').val())
		        }else{
		        	$('#refund_zip').val('');
		        	$('#refund_addr1').val('');
		        	$('#refund_addr2').val('');
		        	$('#refund_addr_jibun').val('');
		        }
	        });	   	              

	        $('#memofrm').iframeAutoHeight({minHeight: 300}); 	        
	    });	

		function sendShop(){
            var password = "";
            password = $("#passwd1").val();

            if (password != '' && password != undefined){
                var pwCheck = charValueCheck(password, 8, 20, "passwd");
                if (pwCheck == "minlength") {
                    alert("비밀번호는 8자 이상만 가능합니다.");
                    $("#passwd1").focus();
                    return;
                }

                if (pwCheck == "han") {
                    alert("비밀번호에는 한글을 사용하실 수 없습니다.");
                    $("#passwd1").focus();
                    return;
                }

    			if (trim($('#passwd2').val()) == ''){
    				alert('비밀번호 확인을 입력하세요.');
    				$("#passwd2").focus();
    				return;
    			}               

    			if (password != trim($('#passwd2').val())){
    				alert('비밀번호 확인이 일치하지 않습니다.');
    				$("#passwd2").focus();
    				return;
    			}    
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

			if (trim($('#co_ceoemail').val()) != ''){
				if (!IsEmail($('#co_ceoemail').val())){
					alert('올바른 이메일 주소를 입력하세요.');
					return;
				}
			}

			/*
			if (trim($('#co_mailorderno').val()) != ''){
				if (!IsNumber($('#co_mailorderno').val())){
					alert('통신판매업 번호는 숫자로만 입력하세요.');
					return;
				}
			}
			*/
			
			if (trim($('#cal_account').val()) != ''){
				if (!IsNumber($('#cal_account').val())){
					alert('정산계좌는 숫자로만 입력하세요.');
					return;
				}
			}

			if (trim($('#delivery_policy_price').val()) != ''){
				if (!IsNumber($('#delivery_policy_price').val())){
					alert('배송비 정책 조건부 금액은 숫자로만 입력하세요.');
					return;
				}
			}			

			if (trim($('#delivery_price').val()) != ''){
				if (!IsNumber($('#delivery_price').val())){
					alert('배송비 금액은 숫자로만 입력하세요.');
					return;
				}
			}

			if ($("input:checkbox[name='charge_type']").is(":checked") == true){
				if (!IsNumber($('#fixed_charge').val())){
					alert('고정입점비 금액은 숫자로만 입력하세요.');
					return;
				}

				if (parseInt($('#fixed_charge').val()) < 1){
					alert('고정입점비 금액은 0원 이상 입력하세요.');
					return;					
				}
			}			

			if (trim($('#cal_account').val()) != ''){
				if (!IsNumber($('#cal_account').val())){
					alert('정산대금 입금 계좌번호는 숫자로만 입력하세요.');
					return;
				}
			}			

			/*
			var dp_sel = $(':radio[name="delivery_policy"]:checked').val();

			if (dp_sel == '10540' && (trim($('#delivery_policy_price').val()) == '' || trim($('#delivery_policy_price').val()) == '0')){
				alert('배송비 정책이 조건부인 경우 금액을 입력하셔야 합니다.');
				return;
			}

			var island_sel = $(':radio[name="islandadd_yn"]:checked').val();

			if (island_sel =='Y' && (trim($('#islandadd_addprice').val()) == '' || trim($('#islandadd_addprice').val()) == '0'))
			{
				alert('도서산간 추가비용 사용을 한 경우 추가금액을 입력하셔야 합니다.');
				return;
			}
			*/
			
			document.form.target = 'hfrm';
			document.form.action = "/manage/shop_m/update/sno/<?=$sNum?>/page/<?=$currentPage.$currentParam?>";
			document.form.submit();
		}    

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?\n저장하지 않은 다른 정보는 소실됩니다.')){
				var url = '/manage/shop_m/profilefiledelete/sno/<?=$sNum?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}		

	    function sendPassRe(type){
		    /*
			if (trim($('#useremail').val()) == ''){
				alert('이메일을 입력하세요.');
				return;
			}
	
			if (!IsEmail($('#useremail').val())){
				alert('올바른 이메일 주소를 입력하세요.');
				return;
			}	
			*/
			$('#reqtype').val(type);
			document.form.target = 'hfrm';
			document.form.action = "/manage/shop_m/pwreissue";
			document.form.submit();		
	    }		
	</script>	   

<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<input type="hidden" id="reqtype" name="reqtype" value=""/>
	<input type="hidden" id="reqpw_email" name="reqpw_email" value="<?=$baseSet['USER_EMAIL_DEC']?>"/>
	<input type="hidden" id="reqpw_mobile" name="reqpw_mobile" value="<?=$baseSet['USER_MOBILE_DEC']?>"/>
	<div id="content">

		<div class="title">
			<h2>[Craft Shop - 상세내역]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 전체 Craft Shop 현황</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col /><col width="10%" /><col width="35%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="5">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<?
				/*
				$fNum = 0;
				$img = $imgFileName = '';
				$defaultImg = '/images/adm/shop.jpg';
				$arrFile = explode('|', $baseSet['PROFILE_FILE_INFO']);
				if (!empty($arrFile[0]))
				{
					$fNum = $arrFile[0];
					$imgFileName = $arrFile[3];
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
				*/
			
				$fi = 0;
				$defaultImg = '/images/adm/shop.jpg';
				if (!empty($flist[$fi]['file_tmpname']))
				{
					$imgUrl = ($flist[$fi]['thumb_yn'] == 'Y') ? $flist[$fi]['thumb_file_path'] : $flist[$i]['file_path'];
				}
				else
				{
					$imgUrl = $defaultImg;
				}				
			?>
			<tbody>
				<tr>
					<th>판매자 구분</th>
					<td colspan="3"><?=$baseSet['SELLERTYPECODE_TITLE']?></td>
					<td rowspan="5">
						<img src="<?=CDN.$imgUrl?>" width="150" height="150" alt="" />
						<br />
						<a href="/download/route/sno/<?=$sNum?>/fno/<?=$flist[$fi]['num']?>" class="alink"><?=$flist[$fi]['file_name']?></a> 
						<?if (!empty($flist[$fi]['file_name'])){?><a href="javascript:delFile('<?=$flist[$fi]['num']?>','<?=$fi?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
						<input type="file" id="userfile0" name="userfile0" class="inp_file"/>
						<input type="file" style="display:none;" id="userfile1" name="userfile1" class="inp_file"/> <!-- 파일 순서를 맞추기 위한 temp (모바일용 프로필 이미지가 필요한 경우 활용) -->
					</td>
				</tr>
				<tr>
					<th>Shop 코드</th>
					<td colspan="3"><?=$baseSet['SHOP_CODE']?></td>
				</tr>
				<tr>
					<th>Shop 명</th>
					<td colspan="3">
						<?=$baseSet['SHOP_NAME']?>
						<input type="hidden" id="shop_name" name="shop_name" value="<?=$baseSet['SHOP_NAME']?>"/>						
					</td>
				</tr>
				<tr>
					<th>작가(계정) 이메일</th>
					<td colspan="3"><?=$baseSet['USER_EMAIL_DEC']?></td>
				</tr>
				<tr>
					<th>작가</th>
					<td colspan="3">
						<?=$baseSet['SHOPUSER_NAME']?> <?if ($isAdmin){?><a href="javascript:messageSend('<?=addslashes(htmlspecialchars($baseSet['SHOP_NAME']))?>', '<?=$sNum?>', 'shop');" class="btn2">메시지</a><a href="javascript:smsSend('<?=addslashes(htmlspecialchars($baseSet['SHOP_NAME']))?>', '<?=$baseSet['SHOP_MOBILE_DEC']?>');" class="btn2">SMS</a><?}?>
						<input type="hidden" id="shopuser_name" name="shopuser_name" value="<?=$baseSet['SHOPUSER_NAME']?>" />						
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호</th>
				<?if ($isAdmin){?>					
					<td colspan="4">
						<a href="javascript:sendPassRe('email');" class="btn2">대표 이메일로 임시 비밀번호 발송</a><a href="javascript:sendPassRe('mobile');" class="btn2">작가 휴대폰으로 임시 비밀번호 발송</a>
					</td>
				<?}else{?>
					<td>
						<input type="password" id="passwd1" name="passwd1" value="" class="inp_sty40" /> <span class="ex">* 변경시에만 입력</span>					
					</td>
					<th>비밀번호 확인</th>
					<td colspan="2">
						<input type="password" id="passwd2" name="passwd2" value="" class="inp_sty40" />					
					</td>	
				<?}?>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 이메일</th>
					<td colspan="4"><input type="text" id="shop_email" name="shop_email" value="<?=$baseSet['SHOP_EMAIL_DEC']?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 휴대폰 번호</th>
					<td colspan="4">
					<?
						$arrMb = explode('-', $baseSet['SHOP_MOBILE_DEC']);
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
					<td colspan="4">
					<?
						$arrTel = explode('-', $baseSet['SHOP_TEL_DEC']);
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
					<th>계약시작일</th>
					<td>
					<?
						if ($isAdmin)
						{
					?>
						<input type="text" id="s_date" name="s_date" value="<?=subStr($baseSet['CONTRACT_START_DATE'], 0, 10)?>" class="inp_sty10" style="width:90px;" readonly/><a id="sdateImg" class="calendar"></a>
					<?
						}
						else 
						{
							echo subStr($baseSet['CONTRACT_START_DATE'], 0, 10);
							echo '<input type="hidden" name="s_date" value="'.subStr($baseSet['CONTRACT_START_DATE'], 0, 10).'"/>'; 
						}
					?>					
					</td>
					<th>계약종료일</th>
					<td colspan="2">
					<?
						if ($isAdmin)
						{
					?>					
						<input type="text" id="e_date" name="e_date" value="<?=subStr($baseSet['CONTRACT_END_DATE'], 0, 10)?>" class="inp_sty10" style="width:90px;"  readonly/><a id="edateImg" class="calendar"></a>
					<?
						}
						else 
						{
							echo subStr($baseSet['CONTRACT_END_DATE'], 0, 10);
							echo '<input type="hidden" name="e_date" value="'.subStr($baseSet['CONTRACT_END_DATE'], 0, 10).'"/>'; 
						}
					?>							
					</td>
				</tr>
				<tr>
					<th>승인일자</th>
					<td><?=$baseSet['APPROVAL_DATE']?></td>
					<th>승인처리자</th>
					<td colspan="2"><?=$baseSet['APPROVALUSER_NAME']?></td>
				</tr>
				<tr>
					<th>CIRCUS 담당자</th>
					<td colspan="4">
						<span id="managerDisp">이름:<?=$baseSet['MANAGERUSER_NAME']?> / 전화번호:<?=$baseSet['MANAGER_TEL_DEC']?> / 휴대폰 번호:<?=$baseSet['MANAGER_MOBILE_DEC']?></span>
						<?if ($isAdmin){?> 
						<a href="javascript:managerChangeSearch();" class="btn1">변경</a>
						<?}?>
						<input type="hidden" id="manager_change_yn" name="manager_change_yn" value="N"/>
						<input type="hidden" id="manager_change_uno" name="manager_change_uno"/>
						<input type="hidden" id="manager_no_org" name="manager_no_org" value="<?=$baseSet['MANAGEUSER_NUM']?>"/>
					</td>
				</tr>
				<tr>
					<th rowspan="2">Shop 상태</th>
					<td colspan="4">
					<?
						$isTxtView = FALSE; //상태에 따라 txt 메모 보임 여부
						if ($isAdmin)
						{
							$i = 1;
							foreach ($spStatCdSet as $crs):
								$isListUp = FALSE;
							
								if ($baseSet['SHOPSTATECODE_NUM'] >= 3060) //승인이상 단계인 경우
								{
									if ($crs['NUM'] >= 3060) $isListUp = TRUE;
								}
								else
								{
									if ($crs['NUM'] <= 3060) $isListUp = TRUE;
								}								
							
								if ($isListUp)
								{
									$sel_chk = ($crs['NUM'] == $baseSet['SHOPSTATECODE_NUM']) ? 'checked="checked"' : '';									
					?>
						<label><input type="radio" id="shop_state<?=$i?>" name="shop_state" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								}
								$i++;
							endforeach;
						}
						else 
						{
							$i = 1;
							foreach ($spStatCdSet as $crs):
								$isListUp = FALSE;
								if ($baseSet['SHOPSTATECODE_NUM'] >= 3060) //승인이상 단계인 경우
								{
									if ($crs['NUM'] == 3060 || $crs['NUM'] == 3070) $isListUp = TRUE;
								}
								
								if ($isListUp)
								{
									if (!$isTxtView) $isTxtView = TRUE;
									$sel_chk = ($crs['NUM'] == $baseSet['SHOPSTATECODE_NUM']) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="shop_state<?=$i?>" name="shop_state" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								}
								$i++;					
							endforeach;
							
							if (!$isTxtView) //radio 선택사항이 안나오는 경우
							{
								echo $baseSet['SHOPSTATECODE_TITLE'];
								echo '<input type="hidden" name="shop_state" value="'.$baseSet['SHOPSTATECODE_NUM'].'"/>';								
							}
						}
					?>						
						<input type="hidden" id="shop_state_org" name="shop_state_org" value="<?=$baseSet['SHOPSTATECODE_NUM']?>"/>
						<input type="hidden" id="appr_firstreq_date" name="appr_firstreq_date" value="<?=$baseSet['APPROVAL_FIRSTREQ_DATE']?>"/>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="bo_tn pd_tn">
						<input type="text" <?if (!$isTxtView){?>style="display:none;"<?}?> id="shop_state_memo" name="shop_state_memo" value="<?=$baseSet['SHOPSTATE_MEMO']?>" class="inp_sty80" placeholder="임시휴업시 고객 안내 메모" />
						<input type="hidden" id="shop_state_memo_org" name="shop_state_memo_org" value="<?=$baseSet['SHOPSTATE_MEMO']?>"/>
					</td>
				</tr>
				<tr>
					<th>뱃지</th>
					<td colspan="4">
						<label <?if (!$isAdmin){?>style="display:none;"<?}?>><input type="checkbox" id="author_type1" name="author_type" value="today" class="inp_radio" <?if ($baseSet['TODAYAUTHOR_YN']=='Y'){?>checked="checked"<?}?> /><span>오늘의 작가</span></label>
						<label <?if (!$isAdmin){?>style="display:none;"<?}?>><input type="checkbox" id="author_type2" name="author_type" value="pop" class="inp_radio" <?if ($baseSet['POPAUTHOR_YN']=='Y'){?>checked="checked"<?}?> /><span>인기작가</span></label>
						<?
							if (!$isAdmin){
								if ($baseSet['TODAYAUTHOR_YN']=='Y') echo ' [오늘의 작가] ';
								if ($baseSet['POPAUTHOR_YN']=='Y') echo ' [인기작가] ';
							}
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">사업자 정보</th>
					<th class="ag_r">
					<?if ($isAdmin){?>
						<label><input type="checkbox" id="copyinfo_check" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label>
					<?}?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>사업자번호</th>
					<td colspan="3">
					<?
						$arrCoNum = explode('-', $infoSet['CO_NUM']);
						$coNum1 = (isset($arrCoNum) && count($arrCoNum)>0) ? $arrCoNum[0] : '';
						$coNum2 = (isset($arrCoNum) && count($arrCoNum)>1) ? $arrCoNum[1] : '';
						$coNum3 = (isset($arrCoNum) && count($arrCoNum)>2) ? $arrCoNum[2] : '';
					?>
						<input type="text" id="co_num1" name="co_num1" value="<?=$coNum1?>" class="inp_sty40" style="width:60px;<?=$style?>" maxlength="3" <?=$readonly?>/>-
						<input type="text" id="co_num2" name="co_num2" value="<?=$coNum2?>" class="inp_sty40" style="width:50px;<?=$style?>" maxlength="2" <?=$readonly?>/>-
						<input type="text" id="co_num3" name="co_num3" value="<?=$coNum3?>" class="inp_sty40" style="width:100px;<?=$style?>" maxlength="5" <?=$readonly?>/> <span class="ex">* 예시) 111-11-11111</span>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자형태</th>
					<td colspan="3"><?=$baseSet['SELLERTYPECODE_TITLE']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td><input type="text" id="co_name" name="co_name" value="<?=$infoSet['CO_NAME']?>" class="inp_sty40" <?=$styleCss?> <?=$readonly?>/></td>
					<th><span class="important">*</span>대표자명</th>
					<td><input type="text" id="co_ceoname" name="co_ceoname" value="<?=$infoSet['CO_CEONAME']?>" class="inp_sty40" <?=$styleCss?> <?=$readonly?>/></td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td><input type="text" id="co_biztype" name="co_biztype" value="<?=$infoSet['CO_BIZTYPE']?>" class="inp_sty40" <?=$styleCss?> <?=$readonly?>/></td>
					<th><span class="important">*</span>종목</th>
					<td><input type="text" id="co_bizevent" name="co_bizevent" value="<?=$infoSet['CO_BIZEVENT']?>" class="inp_sty40" <?=$styleCss?> <?=$readonly?>/></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3"><input type="text" id="co_ceoemail" name="co_ceoemail" value="<?=$infoSet['CO_CEOEMAIL_DEC']?>" class="inp_sty40" <?=$styleCss?> <?=$readonly?>/> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 전화</th>
					<td colspan="3">
					<?
						$arrTel = explode('-', $infoSet['CO_TEL_DEC']);
						$tel1 = (isset($arrTel) && count($arrTel)>0) ? $arrTel[0] : '';
						$tel2 = (isset($arrTel) && count($arrTel)>1) ? $arrTel[1] : '';
						$tel3 = (isset($arrTel) && count($arrTel)>2) ? $arrTel[2] : '';
					?>					
						<input type="text" id="co_tel1" name="co_tel1" value="<?=$tel1?>" class="inp_sty40" style="width:80px;<?=$style?>" maxlength="4" <?=$readonly?>/>-
						<input type="text" id="co_tel2" name="co_tel2" value="<?=$tel2?>" class="inp_sty40" style="width:80px;<?=$style?>" maxlength="4" <?=$readonly?>/>-
						<input type="text" id="co_tel3" name="co_tel3" value="<?=$tel3?>" class="inp_sty40" style="width:80px;<?=$style?>" maxlength="4" <?=$readonly?>/>
						<span class="ex">* 숫자만 입력</span>
					</td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>사업장 소재지</th>
					<td colspan="3"><input type="text" id="co_zip" name="co_zip" value="<?=$infoSet['CO_ZIP_DEC']?>" class="inp_sty10" readonly/><a href="javascript:searchAddress('co_zip','co_addr1','co_addr2','co_addr_jibun');" class="btn1">우편번호 찾기</a></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><input type="text" id="co_addr1" name="co_addr1" value="<?=$infoSet['CO_ADDR1_DEC']?>" class="inp_sty80" readonly/></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn">
						<input type="text" id="co_addr2" name="co_addr2" value="<?=$infoSet['CO_ADDR2_DEC']?>" class="inp_sty80"/>
						<input type="hidden" id="co_addr_jibun" name="co_addr_jibun" value="<?=$infoSet['CO_ADDR_JIBUN_DEC']?>"/>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>통신판매업 번호</th>
					<td colspan="3"><input type="text" id="co_mailorderno" name="co_mailorderno" value="<?=$infoSet['CO_MAILORDER_NO']?>" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>

		<table class="write1">
			<colgroup><col width="12%" /><col /></colgroup>
			<thead>
				<tr>
					<th>기본 배송 정보 및 정책</th>
					<th class="ag_r">
						<!-- <label><input type="checkbox" id="" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label> -->
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>기본 택배사 설정</th>
					<td>
						<select id="delivery_code_num" name="delivery_code_num">
							<option value="">배송업체선택 (default)</option>
							<?
								foreach ($deliCdSet as $crs):
									$sel_chk = ($crs['NUM'] == $polSet['DELIVERYCODE_NUM']) ? 'selected="selected"' : '';
							?>
							<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
							<?
								endforeach;
							?>
						</select>
						<span class="ex">* Shop에서 이용하는 택배 업체 선택</span>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>반품 택배사 설정</th>
					<td>
						<select id="refund_delivery_code_num" name="refund_delivery_code_num">
							<option value="">배송업체선택 (default)</option>
							<?
								foreach ($deliCdSet as $crs):
									$sel_chk = ($crs['NUM'] == $polSet['REFDELIVERYCODE_NUM']) ? 'selected="selected"' : '';
							?>
							<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
							<?
								endforeach;
							?>
						</select>
						<span class="ex">* Shop에서 이용하는 택배 업체 선택</span>
					</td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>배송비 정책</th>
					<td>
					<?
						$i = 1;
						foreach ($deliPlCdSet as $crs):
							$sel_chk = ($crs['NUM'] == $polSet['DELIVPOLICYCODE_NUM']) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="delivery_policy<?=$i?>" name="delivery_policy" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
							$i++;
						endforeach;					
					?>						
						<input type="text" id="delivery_policy_price" name="delivery_policy_price" value="<?=$polSet['DELIVPOLICY_PRICE']?>" class="inp_sty10" /> <span class="ex">원 이상 구매 시 무료</span>
					</td>
				</tr>
				<tr>
					<td class="bo_tn pd_tn">※ 무료 선택 시에도 도서산간 추가비용, 지역별 차등배송비는 추가됩니다.</td>
				</tr>
				<tr>
					<th><span class="important">*</span>배송비</th>
					<td><input type="text" id="delivery_price" name="delivery_price" value="<?=$polSet['DELIVERY_PRICE']?>" class="inp_sty10" /> <span class="ex">* 선불, 착불, 반품 배송비 모두 동일하게 책정</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>도서산간 배송가능<br />여부</th>
					<td>
						<label><input type="radio" id="islanddelivery_yn1" name="islanddelivery_yn" value="Y" class="inp_radio" <?if ($polSet['ISLANDDELIVERY_YN']=='Y'){?>checked="checked"<?}?> /><span>가능</span></label>
						<label><input type="radio" id="islanddelivery_yn2" name="islanddelivery_yn" value="N" class="inp_radio" <?if ($polSet['ISLANDDELIVERY_YN']=='N' || empty($polSet['ISLANDDELIVERY_YN'])){?>checked="checked"<?}?> /><span>불가능</span></label>
					</td>
				</tr>
				<!-- 2016.01 삭제
				<tr>
					<th><span class="important">*</span>도서산간 추가비용 사용 여부</th>
					<td>
						<label><input type="radio" id="islandadd_yn1" name="islandadd_yn" class="inp_radio" <?//if ($polSet['ISLANDADD_YN']=='Y'){?>checked="checked"<?//}?> /><span>사용</span></label>
						<label><input type="radio" id="islandadd_yn2" name="islandadd_yn" class="inp_radio" <?//if ($polSet['ISLANDADD_YN']=='N' || empty($polSet['ISLANDADD_YN'])){?>checked="checked"<?//}?> /><span>사용안함</span></label>
					</td>
					<th>도서산간 추가비용 금액</th>
					<td><input type="text" id="islandadd_addprice" name="islandadd_addprice" class="inp_sty30" value="<?//=$polSet['ISLAND_PRICE']?>" /> 원</td>
				</tr>
				<tr>
					<th><span class="important">*</span>지역별 차등배송비 사용 여부</th>
					<td colspan="3">
						<label><input type="radio" id="area_delivery_yn1" name="area_delivery_yn" class="inp_radio" <?//if ($polSet['AREADELIVERY_YN']=='Y'){?>checked="checked"<?//}?> /><span>사용</span></label>
						<label><input type="radio" id="area_delivery_yn2" name="area_delivery_yn" class="inp_radio" <?//if ($polSet['AREADELIVERY_YN']=='N' || empty($polSet['ISLANDADD_YN'])){?>checked="checked"<?//}?> /><span>사용안함</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>지역별 배송비용</th>
					<td colspan="3">
						<p>※ 지역명 입력 시 콤마(,)로 구분하고, ‘시’ ‘도’ 등은 빼고 입력 (예사. 제주,울릉, 거제)<br /><br /></p>
						<span><input type="text" id="" class="inp_sty10 mg_r10" placeholder="배송지역 입력" /></span> <input type="text" id="" class="inp_sty10" /> 원 <a href="" class="btn1">삭제</a> <br /><br />
						<span><input type="text" id="" class="inp_sty10 mg_r10" placeholder="배송지역 입력" /></span> <input type="text" id="" class="inp_sty10" /> 원 <a href="" class="btn1">삭제</a> <br /><br />
						<span><input type="text" id="" class="inp_sty10 mg_r10" placeholder="배송지역 입력" /></span> <input type="text" id="" class="inp_sty10" /> 원 <a href="" class="btn1">추가</a>
					</td>
				</tr>
				-->				
				<tr>
					<th rowspan="4"><span class="important">*</span>반품지 연락처 및 주소</th>
					<td>
					<?
						$arrRefTel = explode('-', $polSet['REFUND_TEL_DEC']);
						$refTel1 = (isset($arrTel) && count($arrRefTel)>0) ? $arrRefTel[0] : '';
						$refTel2 = (isset($arrTel) && count($arrRefTel)>1) ? $arrRefTel[1] : '';
						$refTel3 = (isset($arrTel) && count($arrRefTel)>2) ? $arrRefTel[2] : '';
					?>						
						<input type="text" id="refund_tel1" name="refund_tel1" value="<?=$refTel1?>" class="inp_sty10" style="width:80px;" maxlength="4"/>-
						<input type="text" id="refund_tel2" name="refund_tel2" value="<?=$refTel2?>" class="inp_sty10" style="width:80px;" maxlength="4" />-
						<input type="text" id="refund_tel3" name="refund_tel3" value="<?=$refTel3?>" class="inp_sty10" style="width:80px;" maxlength="4" />
						<span class="ex">* 숫자만 입력</span>
					</td>
				</tr>
				<tr>
					<td class="bo_tn pd_tn">
						<p><label><input type="checkbox" id="refundaddr_check" name="refundaddr_check" class="inp_check" /><span>사업장 소재지 주소와 동일</span></label></p><br />
						<input type="text" id="refund_zip" name="refund_zip" value="<?=$polSet['REFUND_ZIP_DEC']?>" class="inp_sty20" readonly/><a href="javascript:searchAddress('refund_zip','refund_addr1','refund_addr2','refund_addr_jibun');" class="btn1">우편번호 찾기</a>
					</td>
				</tr>
				<tr>
					<td class="bo_tn pd_tn"><input type="text" id="refund_addr1" name="refund_addr1" value="<?=$polSet['REFUND_ADDR1_DEC']?>" class="inp_sty80" readonly/></td>
				</tr>
				<tr>
					<td class="bo_tn pd_tn">
						<input type="text" id="refund_addr2" name="refund_addr2" value="<?=$polSet['REFUND_ADDR2_DEC']?>" class="inp_sty80" />
						<input type="hidden" id="refund_addr_jibun" name="refund_addr_jibun" value="<?=$polSet['REFUND_ADDR_JIBUN_DEC']?>" />						
					</td>
				</tr>
				<?if ($isAdmin){?>
				<tr>
					<th><span class="important">*</span>수수료 정책</th>
					<td colspan="3">
						<label><input type="checkbox" id="charge_type" name="charge_type" class="inp_radio" <?if ($polSet['CHARGE_TYPE']=='F'){?>checked="checked"<?}?> value="F" /><span>고정입점비 대상샵으로 지정</span></label>
						&nbsp;&nbsp;
						<label><input type="text" id="fixed_charge" name="fixed_charge" value="<?=(empty($polSet['FIXED_CHARGE'])) ? 0 :$polSet['FIXED_CHARGE']?>" class="inp_sty40"/>원</label>
					</td>
				</tr>			
				<?}?>	
				<tr>
					<th rowspan="2"><span class="important">*</span>교환 및 환불 정책</th>
					<td>
					<?
						$i = 1;
						foreach ($refPlCdSet as $crs):
							$sel_chk = ($crs['NUM'] == $polSet['REFPOLICYCODE_NUM']) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="refund_policy<?=$i?>" name="refund_policy" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
							$i++;
						endforeach;					
					?>						
					</td>
				</tr>
				<tr>
					<td class="bo_tn pd_tn">
						<span id="refConAreaDisp" <?if (!$isRefContentView){?>style="display:none;"<?}?>>
							<textarea id="ref_content" name="ref_content" rows="10" cols="100"><?=$refContent?></textarea>
						</span>
						<span id="refContentDisp" <?if ($isRefContentView){?>style="display:none;"<?}?>><?=$refContent?></span>
						
						<span id="hidden_ref_content_disp" style="display:none;">
							<br /><textarea id="hidden_mall_ref_content" rows="10" cols="10"><?=$mallRefContent?></textarea><br />
							<textarea id="hidden_shop_ref_content" rows="10" cols="10"><?=$shopRefContent?></textarea>
						</span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<table class="write1" id="fileDisp">
			<colgroup><col width="12%"><col width="10%"><col></colgroup>
			<thead>
				<tr>
					<th colspan="3">Shop 소개</th>
				</tr>
			</thead>
		    <?
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	if ($fileCnt > 0)
		    	{
		    		for($i=1; $i<($fileCnt-1); $i++)
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
					<th rowspan="2" class="ag_c">상단 꾸미기 이미지</th>
					<td class="ag_c va_m"><div>PC 웹용</div><span class="red">(000 x 000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/sno/<?=$sNum?>/fno/<?=$flist[$fi]['num']?>" class="alink"><?=$flist[$fi]['file_name']?></a> 
								<?if (!empty($flist[$fi]['file_name'])){?><a href="javascript:delFile('<?=$flist[$fi]['num']?>','<?=$fi?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi?>" name="userfile<?=$fi?>" class="inp_file" value="파일찾기" />
								<input type="hidden" id="userHfile<?=$fi?>" name="userHfile<?=$fi?>" value="<?=$flist[$fi]['file_name']?>"/>								
							</dd>
						</dl>
					</td>
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
								<a href="/download/route/sno/<?=$sNum?>/fno/<?=$flist[$fi+1]['num']?>" class="alink"><?=$flist[$fi+1]['file_name']?></a> 
								<?if (!empty($flist[$fi+1]['file_name'])){?><a href="javascript:delFile(<?=$flist[$fi+1]['num']?>,'<?=$fi+1?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
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
			<tbody>
				<tr>
					<th><span class="important">*</span>런칭일</th>
					<td colspan="2"><input type="text" id="profile_date" name="profile_date" value="<?=$baseSet['PROFILE_DATE']?>" class="inp_sty40" />
				</tr>				
				<tr>
					<th><span class="important">*</span>소개 글</th>
					<td colspan="2">
						<textarea id="profile_content" name="profile_content" rows="10" cols="100"><?=$baseSet['PROFILE_CONTENT']?></textarea>
					</td>
				</tr>				
			</tbody>			
		</table>		

		<table class="write1">
			<colgroup><col width="12%" /><col /></colgroup>
			<thead>
				<tr>
					<th colspan="2">정산 정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>정산주기</th>
					<td>
					<?
						if ($isAdmin)
						{
							$i = 1;
							foreach ($calCdSet as $crs):
								$sel_chk = ($crs['NUM'] == $polSet['CALCYCLECODE_NUM']) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="cal_cycle<?=$i?>" name="cal_cycle" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								$i++;
							endforeach;
						}
						else 
						{
							echo $polSet['CALCYCLECODE_TITLE'];
							echo '<input type="hidden" name="cal_cycle" value="'.$polSet['CALCYCLECODE_NUM'].'"/>';
						}
					?>								
					</td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>정산대금 입금계좌</th>
					<td>
						<span>은행명:</span>
						<select id="cal_bank" name="cal_bank" <?if (!$isAdmin){?>style="display:none"<?}?>>
							<option value="" selected="selected">은행명</option>
							<?
								foreach ($calBankCdSet as $bnk):
									$sel_chk = ($bnk['NUM'] == $polSet['CALBANKCODE_NUM']) ? 'selected="selected"' : '';
							?>							
							<option value="<?=$bnk['NUM']?>" <?=$sel_chk?>><?=$bnk['TITLE']?></option>
							<?
								endforeach;
							?>
						</select>
						<?if (!$isAdmin) echo $polSet['CALBANKCODE_TITLE'];?>
						<span class="mg_l10">예금주명:</span>
						<input type="text" id="cal_name" name="cal_name" value="<?=$polSet['CAL_NAME']?>" class="inp_sty10" <?=$styleCss?> <?=$readonly?>/>
					</td>
				</tr>
				<tr>
					<td><span class="mg_r10">계좌번호:</span><input type="text" id="cal_account" name="cal_account" value="<?=$polSet['CAL_ACCOUNT']?>" class="inp_sty10" <?=$styleCss?> <?=$readonly?>/> <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>

		<table class="write1">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">판매 내역</th>
					<th class="ag_r">
						<!-- <span><?=date('Y-m-d H:i:s')?> 현재</span> -->
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 판매금액</th>
					<td colspan="3"><?=number_format($baseSet['TOTSELL_AMOUNT'])?>원</td>
				</tr>
				<tr>
					<th>총 건수</th>
					<td><?=number_format($baseSet['TOTSELL_COUNT'])?>건</td>
					<th>매출순위</th>
					<td>
					<?
						if (isset($shopStatsSet['SELLCOUNT_RANK']))
						{
							if ($shopStatsSet['SELLCOUNT_RANK'] > 0)
							{
								echo number_format($shopStatsSet['SELLAMOUNT_RANK']).' 위 ';
								echo '('.$shopStatsSet['CREATE_DATE'].' 현재)';
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

		<table class="write1">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">활동정보</th>
					<th class="ag_r">
						<!-- <span><?=date('Y-m-d H:i:s')?> 현재</span> -->
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>등록 Item / Flag수</th>
					<td>
						<?=number_format($baseSet['TOTITEM_COUNT'])?>개 / <?=number_format($baseSet['TOTITEMFLAG_COUNT'])?>건
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
					<td><?=number_format($baseSet['TOTFLAG_COUNT'])?>건</td>
				</tr>
				<!-- <tr>
					<th>PC웹에서 공유된 건</th>
					<td>0건</td>
					<th>모바일앱에서 공유된 건</th>
					<td>0건</td>
				</tr> -->
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="javascript:sendShop();" class="btn3">저장</a>
			<?if ($isAdmin){?><a href="<?=$listUrl?>" class="btn1">목록</a><?}?>
		</div>
		
		<?if ($isAdmin){?>
		<div><iframe id="memofrm" src="/manage/memo_m/list/t_no/<?=$sNum?>/t_info/<?=$tblEnc?>" width="100%" scrolling="no" frameborder="0"></iframe></div>
		<?}?>		

	</div>
	</form>
</div>
<!--// container -->
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>