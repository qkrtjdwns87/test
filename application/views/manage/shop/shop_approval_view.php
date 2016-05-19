<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$listUrl = '/manage/shop_m/apprlist';
	$listUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$listUrl .= (!empty($currentParam)) ? '?cs=shop'.$currentParam : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<script src="/js/jquery.browser.js"></script>	
	<script src="/js/jquery.iframe-auto-height.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
      
	    });	

		function sendShop(){
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

			/*
			if (trim($('#co_mailorderno').val()) != ''){
				if (!IsNumber($('#co_mailorderno').val())){
					alert('통신판매업 번호는 숫자로만 입력하세요.');
					return;
				}
			}
			*/
			
			document.form.target = 'hfrm';
			document.form.action = "/manage/shop_m/apprupdate/sno/<?=$sNum?>/page/<?=$currentPage.$currentParam?>";
			document.form.submit();
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post">
	<div id="content">

		<div class="title">
			<h2>[승인현황]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 승인현황</div>
		</div>
		
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>판매자 구분</th>
					<td colspan="3">
					<?
						$i = 1;
						foreach ($sellTyCdSet as $crs):
							$sel_chk = ($crs['NUM'] == $baseSet['SELLERTYPECODE_NUM']) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="seller_type<?=$i?>" name="seller_type" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
							$i++;
						endforeach;					
					?>							
					</td>
				</tr>
				<tr>
					<th>Shop 명</th>
					<td colspan="3"><input type="text" id="shop_name" name="shop_name" value="<?=$baseSet['SHOP_NAME']?>" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>작가</th>
					<td colspan="3"><input type="text" id="shopuser_name" name="shopuser_name" value="<?=$baseSet['SHOPUSER_NAME']?>" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>계정 이메일</th>
					<td colspan="3"><?=$baseSet['USER_EMAIL_DEC']?></td>
				</tr>
				<tr>
					<th>작가(샵) 이메일</th>
					<td colspan="3"><input type="text" id="shop_email" name="shop_email" value="<?=$baseSet['SHOP_EMAIL_DEC']?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>작가(샵) 휴대폰 번호</th>
					<td colspan="3">
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
					<th>작가(샵) 대표 번호</th>
					<td colspan="3">
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
					<th>최초 승인요청일시</th>
					<td><?=$baseSet['APPROVAL_FIRSTREQ_DATE']?></td>
					<th>최근 상태변경일시</th>
					<td><?=$baseSet['SHOPSTATE_UPDATE_DATE']?></td>
				</tr>
				<tr>
					<th>CIRCUS 관리 담당자</th>
					<td colspan="3">
						<span id="managerDisp">이름:<?=$baseSet['MANAGERUSER_NAME']?> / 전화번호:<?=$baseSet['MANAGER_TEL_DEC']?> / 휴대폰 번호:<?=$baseSet['MANAGER_MOBILE_DEC']?></span> <a href="javascript:managerChangeSearch();" class="btn1">변경</a>
						<input type="hidden" id="manager_change_yn" name="manager_change_yn" value="N"/>
						<input type="hidden" id="manager_change_uno" name="manager_change_uno"/>
						<input type="hidden" id="manager_no_org" name="manager_no_org" value="<?=$baseSet['MANAGEUSER_NUM']?>"/>					
					</td>
				</tr>
				<tr>
					<th>승인진행 상태</th>
					<td colspan="3">
					<?
						if ($isAdmin)
						{
							$i = 1;
							foreach ($spStatCdSet as $crs):
								$isListUp = FALSE;
								/*
								if ($baseSet['SHOPSTATECODE_NUM'] >= 3060) //승인이상 단계인 경우
								{
									if ($crs['NUM'] >= 3060) $isListUp = TRUE;
								}
								else
								{
									if ($crs['NUM'] <= 3060) $isListUp = TRUE;
								}
								*/
								if ($crs['NUM'] <= 3060) $isListUp = TRUE;
							
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
							echo $baseSet['SHOPSTATECODE_TITLE'];
							echo '<input type=\"hidden\" name=\"shop_state\" value=\"'.$baseSet['SHOPSTATECODE_NUM'].'\"/>';
						}
					?>						
						<input type="hidden" id="shop_state_org" name="shop_state_org" value="<?=$baseSet['SHOPSTATECODE_NUM']?>"/>
						<input type="hidden" id="appr_firstreq_date" name="appr_firstreq_date" value="<?=$baseSet['APPROVAL_FIRSTREQ_DATE']?>"/>
						<a href="javascript:shopHistorySearch('<?=$sNum?>', 'lowerApproval');" class="btn1">진행내역 자세히 보기</a>
					</td>
				</tr>
				<tr>
					<th>보류/거부 사유<br />(이력 작성)</th>
					<?
						$historyContent = ($hisSet['rsTotalCount'] > 0) ? $hisSet['recordSet'][0]['CONTENT'] : '';
					?>
					<td colspan="3">
						<textarea id="shop_history_content" name="shop_history_content" rows="5" cols="5" class="textarea1"><?=$historyContent?></textarea>
						<input type="hidden" id="shop_history_content_org" name="shop_history_content_org" value="<?=$historyContent?>"/>
						<input type="hidden" id="shop_state_memo" name="shop_state_memo" value="<?=$baseSet['SHOPSTATE_MEMO']?>" />
						<input type="hidden" id="shop_state_memo_org" name="shop_state_memo_org" value="<?=$baseSet['SHOPSTATE_MEMO']?>"/>						
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
						<!-- <label><input type="checkbox" id="" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label> -->
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>사업자 번호</th>
					<td colspan="3">
					<?
						$arrCoNum = explode('-', $infoSet['CO_NUM']);
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
					<th><span class="important">*</span>사업자 형태</th>
					<td colspan="3"><?=$baseSet['SELLERTYPECODE_TITLE']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td><input type="text" id="co_name" name="co_name" value="<?=$infoSet['CO_NAME']?>" class="inp_sty40" /></td>
					<th><span class="important">*</span>대표자명</th>
					<td><input type="text" id="co_ceoname" name="co_ceoname" value="<?=$infoSet['CO_CEONAME']?>" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td><input type="text" id="co_biztype" name="co_biztype" value="<?=$infoSet['CO_BIZTYPE']?>" class="inp_sty40" /></td>
					<th><span class="important">*</span>종목</th>
					<td><input type="text" id="co_bizevent" name="co_bizevent" value="<?=$infoSet['CO_BIZEVENT']?>" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3"><input type="text" id="co_ceoemail" name="co_ceoemail" value="<?=$infoSet['CO_CEOEMAIL_DEC']?>" class="inp_sty40" /> <span class="ex">* 예시) abc#abc.co.kr</span></td>
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
						<input type="text" id="co_tel1" name="co_tel1" value="<?=$tel1?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="co_tel2" name="co_tel2" value="<?=$tel2?>" class="inp_sty40" style="width:80px;" maxlength="4" />-
						<input type="text" id="co_tel3" name="co_tel3" value="<?=$tel3?>" class="inp_sty40" style="width:80px;" maxlength="4" />
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

		<div class="btn_list">
			<a href="javascript:sendShop();" class="btn3">저장</a>
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>	