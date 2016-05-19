<?

	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$userId =
	$userName = '';
	$userNick = '';
	$userEmail = '';
	$userPass = '';
	$uLevelCodeNum = '';
	$uStateCodeNum = '';
	$uStateMemo = '';
	$userTel = '';
	$userMobile = '';
	$userPart = '';
	$userGender = '';
	$userBirth = '';
	$snsCodeNum = 0;
	$snsId = '';
	$snsName = '';
	$snsNick = '';
	$snsEmail = '';
	$snsProfileImg = '';
	$totOrderCount = 0;
	$totOrderAmount = 0;
	$penaltyCount = 0;
	$penaltyDate = '';
	$totItemFlagCount = 0;
	$totShopFlagCount = 0;
	$totSendMsgCount = 0;
	$totReplyCount = 0;
	$flagOpenYn = 'Y';
	$useYn = 'Y';
	$smsYn = 'N';
	$emailYn = 'N';
	$emailYnChangeDate = '';
	$smsYnChangeDate = '';
	$marketYn = 'N';
	$leaveDate = '';
	$leaveAdminYn = 'N';
	$approvalDate = '';
	$lastLoginDate = '';
	$lastLoginRemoteIp = '';
	$inflowRoute = '';
	$updateDate = '';
	$remoteIp = '';
	$createDate = '';
	$fileName = '';
	$followerCount = 0;
	$followingCount = 0;
	$dormantDate = '';
	
	if ($pageMethod == 'writeform')
	{

	}
	else if (in_array($pageMethod, array('updateform')))
	{
		$userId = $baseSet['USER_ID'];
		$userName = $baseSet['USER_NAME'];                 
		$userNick = $baseSet['USER_NICK'];                  
		$userEmail = $baseSet['USER_EMAIL_DEC'];                 
		$userPass = $baseSet['USER_PASS'];                  
		$uLevelCodeNum = $baseSet['ULEVELCODE_NUM'];         
		$uLevelCodeTitle = $baseSet['ULEVELCODE_TITLE'];
		$uStateCodeNum = $baseSet['USTATECODE_NUM'];        
		$uStateCodeTitle = $baseSet['USTATECODE_TITLE'];
		$uStateMemo = $baseSet['USTATE_MEMO'];             
		$userTel = $baseSet['USER_TEL_DEC'];                    
		$userMobile = $baseSet['USER_MOBILE_DEC'];               
		$userPart = $baseSet['USER_PART'];                  
		$userGender = $baseSet['USER_GENDER'];              
		$userBirth = $baseSet['USER_BIRTH'];                 
		$snsCodeNum = $baseSet['SNSCODE_NUM'];             
		$snsId = $baseSet['SNS_ID'];                       
		$snsName = $baseSet['SNS_NAME'];                  
		$snsNick = $baseSet['SNS_NICK'];                    
		$snsEmail = $baseSet['SNS_EMAIL'];                  
		$snsProfileImg = $baseSet['SNSPROFILE_IMG'];           
		$totOrderCount = $baseSet['TOTORDER_COUNT'];         
		$totOrderAmount = $baseSet['TOTORDER_AMOUNT'];      
		$penaltyCount = $baseSet['PENALTY_COUNT'];           
		$penaltyDate = $baseSet['PENALTY_DATE'];              
		$totItemFlagCount = $baseSet['TOTITEMFLAG_COUNT'];     
		$totShopFlagCount = $baseSet['TOTSHOPFLAG_COUNT'];    
		$totSendMsgCount = $baseSet['TOTSENDMSG_COUNT'];    
		$totReplyCount = $baseSet['TOTREPLY_COUNT'];          
		$flagOpenYn = $baseSet['FLAGOPEN_YN'];              
		$useYn = $baseSet['USE_YN'];                       
		$delYn = $baseSet['DEL_YN'];                       
		$smsYn = $baseSet['SMS_YN'];                      
		$emailYn = $baseSet['EMAIL_YN'];                    
		$emailYnChangeDate = $baseSet['EMAILYN_CHANGE_DATE'];                   
		$smsYnChangeDate = $baseSet['SMSYN_CHANGE_DATE'];
		$marketYn = $baseSet['MARKET_YN'];
		$approvalDate = $baseSet['APPROVAL_DATE'];
		$leaveDate = $baseSet['LEAVE_DATE'];
		$leaveAdminYn = $baseSet['LEAVE_ADMIN_YN']; //관리자에 의해 강제 탈퇴됐는지 여부
		$lastLoginDate = $baseSet['LASTLOGIN_DATE'];               
		$lastLoginRemoteIp = $baseSet['LASTLOGIN_REMOTEIP'];         
		$inflowRoute = $baseSet['INFLOW_ROUTE'];                 
		$updateDate = $baseSet['UPDATE_DATE'];                   
		$remoteIp = $baseSet['REMOTEIP'];                       
		$createDate = $baseSet['CREATE_DATE'];   
		$followerCount = $baseSet['FOLLOWER_COUNT'];
		$followingCount = $baseSet['FOLLOWING_COUNT'];
		$dormantDate = $baseSet['DORMANT_DATE'];
	}
	
	$resetUrl = '/manage/user_m/writeform';
	$submitUrl = '/manage/user_m/write';	
	$listUrl = '/manage/user_m/list';
	$tmpPassUrl = '/manage/user_m/passwordupdate';
	
	$addUrl = (!empty($currentPage) && ($currentPage > 0)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? '?cs=user'.$currentParam : '';	
	
	if ($pageMethod == 'updateform')
	{
		$resetUrl = '/manage/user_m/updateform/uno/'.$uNum;		
		$submitUrl = '/manage/user_m/update/uno/'.$uNum;
	}
	
	$listUrl .= $addUrl;
	$resetUrl .= $addUrl;
	$deleteUrl = '/manage/user_m/delete/uno/'.$uNum;
	
	$toDate = date('Y-m-d');
	$toYear = substr($toDate, 0 , 4);

?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/js/jquery.browser.js"></script>	
	<script src="/js/jquery.iframe-auto-height.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	    	$('#memofrm').iframeAutoHeight({minHeight: 300});
	    });	

		function sendUser(){
			if (trim($('#user_mobile1').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (trim($('#user_mobile2').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (trim($('#user_mobile3').val()) == ''){
				alert('휴대폰번호를 입력하세요.');
				return;
			}

			if (!IsNumber(trim($('#user_mobile1').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#user_mobile2').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}

			if (!IsNumber(trim($('#user_mobile3').val()))){
				alert('휴대폰번호는 숫자만 입력할 수 있습니다.');
				return;
			}
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();
		}

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?')){
				var url = '/manage/user_m/profilefiledelete/uno/<?=$uNum?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}
		function sendTemp(sendtype){

			//Controller use params setting
			document.getElementById("sendType").value = sendtype;
			document.getElementById("updateType").value = sendtype;

			//Message Setting 
			var msg = '';

			if(sendtype =='EMAIL'){
				//EMAIL 일때 
				msg = '<? echo $userEmail; ?>';
			}else{
				//SMS 일때 
				msg = '<? echo $userMobile; ?>';
			}

			if (confirm('선택한 '+msg+' 로 임시 비밀번호를 전송 하시겠습니까?')){
			    document.form.target = 'hfrm';
				document.form.action = "<?=$tmpPassUrl?>";
				document.form.submit();
	     	}
	    }
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<input type="hidden" name="penalty_count" value="<?=$penaltyCount?>"/>
	<input type="hidden" name="selUserNum" id="selUserNum"  value="<?=$uNum?>"/>
	<input type="hidden" name="userEmail"  id="userEmail"  value="<?=$userEmail?>"/>
	<input type="hidden" name="userMobile" id="userMobile"  value="<?=$userMobile?>"/>
	<input type="hidden" name="updateType" id="updateType" value=""/>
	<input type="hidden" name="sendType" id="sendType"  value=""/>
	<div id="content">

		<div class="title">
			<h2>[전체회원현황]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 전체회원현황</div>
		</div>
		
		<div class="sub_title"><span class="important">*</span>은 필수 입력 사항입니다.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col width="35%" /><col width="12%" /><col width="35%" /><col width="6%" /></colgroup>
			<thead>
				<tr>
					<th colspan="5">개인정보</th>
				</tr>
			</thead>
			<?
				$fNum = 0;
				$img = $imgFileName = '';
				$defaultImg = '/images/adm/shop.jpg';
				$arrFile = explode('|', $baseSet['PROFILE_FILE_INFO']);
				if (!empty($arrFile[0]))
				{
					$fNum = $arrFile[0];
					$imgFileName = $arrFile[1];
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
			<tbody>
				<tr>
					<th>계정(이메일)</th>
					<td colspan="3"><?=$userEmail?></td>
					<td rowspan="3">
						<img src="<?=$fileName?>" width="100" height="100" alt="" />
						<?if (($fNum > 0)){?>
						<br />
						<a href="/download/route/uno/<?=$uNum?>/fno/<?=$fNum?>" class="alink"><?=$imgFileName?></a> 
						<a href="javascript:delFile('<?=$fNum?>','0');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a>
						<?}?>
						<input type="file" id="userfile0" name="userfile0" class="inp_file"/>					
					</td>
				</tr>
				<tr>
					<th>이름</th>
					<td colspan="3"><?=$userName?> <a href="javascript:messageSend('<?=$userName?>', '<?=$uNum?>', 'user');" class="btn2">메시지</a><a href="javascript:smsSend('<?=$userName?>', '<?=$userMobile?>');" class="btn2">SMS</a></td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호</th>
					<td colspan="3">
						 <a href="javascript:sendTemp('EMAIL');"  class="btn2">이메일로 임시 비밀번호 발송</a>
						 <a href="javascript:sendTemp('SMS');"    class="btn2">휴대폰으로 임시 비밀번호 발송</a>
						 <a href="javascript:passwordChange('<?=$uNum?>');" class="btn2">비밀번호 임의변경</a>
					</td>
				</tr>
				<tr>
					<th>생년월일</th>
					<td>
					<?
						$arrBirth = explode('-', $userBirth);
						$birthY = (isset($arrBirth) && count($arrBirth)>0) ? $arrBirth[0] : '';
						$birthM = (isset($arrBirth) && count($arrBirth)>1) ? $arrBirth[1] : '';
						$birthD = (isset($arrBirth) && count($arrBirth)>2) ? $arrBirth[2] : '';					
					?>
						<select id="birth_year" name="birth_year">
							<option value="" selected="selected">년</option>
						<?
							for($i=1930; $i<intval($toYear); $i++)
							{
								$sel_chk = ($i == $birthY) ? 'checked="checked"' : '';
						?>							
							<option value="<?=$i?>" <?=$sel_chk?>><?=$i?></option>
						<?
							}
						?>
						</select>
						<select id="birth_month" name="birth_month">
							<option value="" selected="selected">월</option>						
						<?
							for($i=1; $i<12; $i++)
							{
								$mon = str_pad($i, 2, '0', STR_PAD_LEFT);
								$sel_chk = ($mon == $birthM) ? 'checked="checked"' : '';
								
						?>						
							<option value="<?=$mon?>" <?=$sel_chk?>><?=$mon?></option>
						<?
							}
						?>
						</select>
						<select id="birth_day" name="birth_day">
							<option value="" selected="selected">일</option>
						<?
							for($i=1; $i<32; $i++)
							{
								$day = str_pad($i, 2, '0', STR_PAD_LEFT);
								$sel_chk = ($day == $birthD) ? 'checked="checked"' : '';
								
						?>								
							<option value="<?=$day?>" <?=$sel_chk?>><?=$day?></option>
						<?
							}
						?>							
						</select>
					</td>
					<th>성별</th>
					<td colspan="2">
						<label><input type="radio" id="user_gender1" name="user_gender" value="N" class="inp_radio" <?if (empty($userGender) || $userGender == 'N'){?>checked="checked"<?}?>/><span>모름</span></label>					
						<label><input type="radio" id="user_gender2" name="user_gender" value="M" class="inp_radio" <?if ($userGender == 'M'){?>checked="checked"<?}?>/><span>남성</span></label>
						<label><input type="radio" id="user_gender3" name="user_gender" value="F" class="inp_radio" <?if ($userGender == 'F'){?>checked="checked"<?}?> /><span>여성</span></label>
					</td>
				</tr>
				<tr>
					<th><span class="important">*</span>휴대폰 번호</th>
					<td colspan="4">
					<?
						$arrMb = explode('-', $userMobile);
						$mbNum1 = (isset($arrMb) && count($arrMb)>0) ? $arrMb[0] : '';
						$mbNum2 = (isset($arrMb) && count($arrMb)>1) ? $arrMb[1] : '';
						$mbNum3 = (isset($arrMb) && count($arrMb)>2) ? $arrMb[2] : '';					
					?>					
						<input type="text" id="user_mobile1" name="user_mobile1" value="<?=$mbNum1?>" class="inp_sty40" style="width:80px;" maxlength="4"/>-
						<input type="text" id="user_mobile2" name="user_mobile2" value="<?=$mbNum2?>" class="inp_sty40" style="width:80px;" maxlength="4"/>-
						<input type="text" id="user_mobile3" name="user_mobile3" value="<?=$mbNum3?>" class="inp_sty40" style="width:80px;" maxlength="4"/> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>가입방법</th>
					<td><?=$uLevelCodeTitle?></td>
					<th>가입일시</th>
					<td colspan="2"><?=$createDate?></td>
				</tr>
				<tr>
					<th>승인일시</th>
					<td><?=$approvalDate?></td>
					<th>패널티적용일시</th>
					<td colspan="2"><?=$penaltyDate?></td>
				</tr>
				<tr>
					<th rowspan="2">회원상태</th>
					<td colspan="4">
					<?
						$i = 2;
						foreach ($uStateCdSet as $crs):
							if ($crs['NUM'] > 900)
							{
								$sel_chk = ($crs['NUM'] == $uStateCodeNum) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="user_state<?=$i?>" name="user_state" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								$i++;					
							}
						endforeach;					
					?>
						<input type="hidden" id="user_state_org" name="user_state_org" value="<?=$uStateCodeNum?>"/>							
					</td>
				</tr>
				<tr>
					<td colspan="4" class="bo_tn pd_tn">
						<textarea id="ustate_memo" name="ustate_memo" rows="5" cols="5" class="textarea1"><?=$uStateMemo?></textarea>
						<p><span class="ex">패널티 회원은 모든 댓글(한줄 남기기) 서비스를 이용할 수 없습니다.</span></p>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">방문정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>최근 로그인 일시</th>
					<td><?=$lastLoginDate?></td>
					<th>최근 방문 IP/UUID</th>
					<td><?=$lastLoginRemoteIp?></td>
				</tr>
				<tr>
					<th>미로그인</th>
					<?
						$dateDiffDay = '-';
						if (!empty($lastLoginDate))
						{
							$date1 = new DateTime($toDate);	$date2 = new DateTime($lastLoginDate);
							$dateDiff = date_diff($date1, $date2);
							$dateDiffDay = $dateDiff->days;
						}
					?>
					<td colspan="3"><?=intval($dateDiffDay)?>일</td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">정보 수신 동의</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>이메일 수신</th>
					<td>
						<label><input type="radio" id="email_yn1" name="email_yn" value="Y" class="inp_radio" <?if ($emailYn == 'Y'){?>checked="checked"<?}?>/><span>수신허용</span></label>
						<label><input type="radio" id="email_yn2" name="email_yn" value="N" class="inp_radio" <?if ($emailYn == 'N'){?>checked="checked"<?}?>/><span>수신안함</span></label>
						<input type="hidden" id="email_yn_org" name="email_yn_org" value="<?=$emailYn?>"/>
					</td>
					<th>최근 이메일 수신정보 변경</th>
					<td><?=$emailYnChangeDate?></td>
				</tr>
				<tr>
					<th>SMS 수신</th>
					<td>
						<label><input type="radio" id="sms_yn1" name="sms_yn" value="Y" class="inp_radio" <?if ($smsYn == 'Y'){?>checked="checked"<?}?>/><span>수신허용</span></label>
						<label><input type="radio" id="sms_yn2" name="sms_yn" value="N" class="inp_radio" <?if ($smsYn == 'N'){?>checked="checked"<?}?>/><span>수신안함</span></label>
						<input type="hidden" id="sms_yn_org" name="sms_yn_org" value="<?=$smsYn?>"/>
					</td>
					<th>최근 SMS 수신정보 변경</th>
					<td><?=$smsYnChangeDate?></td>
				</tr>
				<tr>
					<th>MARKET정보 수신</th>
					<td colspan="3">
						<label><input type="radio" id="market_yn1" name="market_yn" value="Y" class="inp_radio" <?if ($marketYn == 'Y'){?>checked="checked"<?}?>/><span>수신허용</span></label>
						<label><input type="radio" id="market_yn2" name="market_yn" value="N" class="inp_radio" <?if ($marketYn == 'N'){?>checked="checked"<?}?>/><span>수신안함</span></label>
						<input type="hidden" id="market_yn_org" name="market_yn_org" value="<?=$marketYn?>"/>
					</td>
				</tr>				
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">구매내역</th>
					<th class="ag_r"><?=date('Y-m-d H:i:s')?> 현재 <a href="javascript:orderSearch('<?=$uNum?>', 'user');" class="btn1">자세히 보기</a></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>총 결제금액</th>
					<td colspan="3"><strong><?=number_format($totOrderAmount)?>원</strong></td>
				</tr>
				<tr>
					<th>총 건수</th>
					<td colspan="3"><strong><?=number_format($totOrderCount)?>건</strong></td>
				</tr>
				<tr>
					<th>최근 배송지</th>
					<td colspan="3"></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">활동정보</th>
					<th class="ag_r"><?=date('Y-m-d H:i:s')?> 현재</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>Item Flag</th>
					<td><?=number_format($totItemFlagCount)?>개</td>
					<th>Craft Shop Flag</th>
					<td><?=number_format($totItemFlagCount)?>개</td>
				</tr>
				<tr>
					<th>댓글(한줄 남기기)</th>
					<td><?=number_format($totReplyCount)?>건</td>
					<th>메시지 발송</th>
					<td><?=number_format($totSendMsgCount)?>건</td>
				</tr>
				<tr>
					<th>Follower</th>
					<td><?=number_format($followerCount)?>명</td>
					<th>Following</th>
					<td><?=number_format($followingCount)?>명</td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="javascript:sendUser();" class="btn3">저장</a>
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>

		<div><iframe id="memofrm" src="/manage/memo_m/list/t_no/<?=$uNum?>/t_info/<?=$tblEnc?>" width="100%" scrolling="no" frameborder="0"></iframe></div>

	</div>
	</form>
</div>
<!--// container -->

<div id="popuptest">
  
</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		