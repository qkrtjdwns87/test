<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/js/jquery.battatech.excelexport.js"></script>		
	<script type="text/javascript">
		$(function() {
			$( "#sdate, #edate" ).datepicker({
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

			$("#sdateImg").click(function() { 
				$("#sdate").datepicker("show");
			});
			$("#edateImg").click(function() { 
				$("#edate").datepicker("show");
			});			
		});

		function search(){
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function grpUserChange(method, methodTitle){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
						
			if (confirm('선택한 회원을 ' + methodTitle + ' 처리 하시겠습니까?')){

				var url = '/manage/user_m/change';
				url += '/return_url/' + $.base64.encode(location.pathname + location.search);				
				hfrm.location.href = url + '?selval='+sel+'&method='+method;	
			}
		}		
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체회원현황]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 전체회원현황</div>
		</div>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>회원상태</th>
					<td>
						<label><input type="radio" id="userstate" name="userstate" value="" class="inp_radio" <?if (empty($itemState)){?>checked="checked"<?}?> /><span>전체</span></label>
					<?
						$i = 2;
						foreach ($uStateCdSet as $crs):
							if ($crs['NUM'] > 900)
							{
								$sel_chk = ($crs['NUM'] == $userState) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="userstate<?=$i?>" name="userstate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								$i++;					
							}
						endforeach;					
					?>							
					</td>
					<th>회원등급</th>
					<td>
						<label><input type="radio" id="userlevel" name="userlevel" value="" class="inp_radio" <?if (empty($userLevel)){?>checked="checked"<?}?> /><span>전체</span></label>
					<?
						$i = 2;
						foreach ($uLevelCdSet as $crs):
							if ($crs['NUM'] > 660 && !in_array($crs['NUM'], array(890)))
							{
								$sel_chk = ($crs['NUM'] == $userLevel) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="userlevel<?=$i?>" name="userlevel" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								$i++;					
							}
						endforeach;					
					?>											
					</td>
				</tr>
				<tr>
					<th>계정(이메일)</th>
					<td><input type="text" id="useremail" name="useremail" value="<?=$userEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
					<th>휴대폰</th>
					<td><input type="text" id="usermobile" name="usermobile" value="<?=$userMobile?>" class="inp_sty40" /> <span class="ex">* 숫자만 입력</span></td>
				</tr>
				<tr>
					<th>이름</th>
					<td><input type="text" id="username" name="username" value="<?=$userName?>" class="inp_sty40" /></td>
					<th>성별</th>
					<td>
						<label><input type="radio" id="usergender1" name="usergender" value="" class="inp_radio" <?if (empty($userGender)){?>checked="checked"<?}?> /><span>전체</span></label>
						<label><input type="radio" id="usergender2" name="usergender" value="N" class="inp_radio" <?if ($userGender == 'N'){?>checked="checked"<?}?> /><span>모름</span></label>						
						<label><input type="radio" id="usergender3" name="usergender" value="M" class="inp_radio" <?if ($userGender == 'M'){?>checked="checked"<?}?> /><span>남성</span></label>
						<label><input type="radio" id="usergender4" name="usergender" value="F" class="inp_radio" <?if ($userGender == 'F'){?>checked="checked"<?}?> /><span>여성</span></label>
					</td>
				</tr>
				<tr>
					<th>이메일 수신</th>
					<td>
						<label><input type="radio" id="emailyn1" name="emailyn" value="" class="inp_radio" <?if (empty($emailYn)){?>checked="checked"<?}?> /><span>전체</span></label>
						<label><input type="radio" id="emailyn2" name="emailyn" value="Y" class="inp_radio" <?if ($emailYn == 'Y'){?>checked="checked"<?}?> /><span>수신허용</span></label>
						<label><input type="radio" id="emailyn3" name="emailyn" value="N" class="inp_radio" <?if ($emailYn == 'N'){?>checked="checked"<?}?> /><span>수신안함</span></label>
					</td>
					<th>SMS 수신</th>
					<td>
						<label><input type="radio" id="smsyn1" name="smsyn" value="" class="inp_radio"  <?if (empty($smsYn)){?>checked="checked"<?}?>/><span>전체</span></label>
						<label><input type="radio" id="smsyn2" name="smsyn" value="Y" class="inp_radio"  <?if ($smsYn == 'Y'){?>checked="checked"<?}?>/><span>수신허용</span></label>
						<label><input type="radio" id="smsyn3" name="smsyn" value="N" class="inp_radio"  <?if ($smsYn == 'N'){?>checked="checked"<?}?>/><span>수신안함</span></label>
					</td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td>
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>					
					</td>
					<th>미로그인 시간</th>
					<td><input type="text" id="logincheckday" name="logincheckday" value="<?=$logincheckDay?>" class="inp_sty5" /> 일 이상 미접속 회원 <span class="ex">* 숫자만 입력</span></td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
		
		<div class="sub_title">총 <?=number_format($rsTotalCount)?>개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="5%" /><col width="15%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<th>회원명</th>
					<th>계정(이메일)</th>
					<th>휴대폰</th>
					<th>생년월일</th>
					<th>성별</th>
					<th>가입방법</th>
					<th>가입일</th>
					<th>상태</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/user_m/updateform/uno/'.$rs['NUM'].$addUrl;
					
					if ($rs['USER_GENDER'] == 'M')
					{
						$userGender = '남성';
					}
					else if ($rs['USER_GENDER'] == 'M')
					{
						$userGender = '여성';
					}
					else 
					{
						$userGender = '모름';
					}
					
					if ($rs['USTATECODE_NUM'] == 950)
					{
						//패널티
						$css = 'class="red"';
					}
					else if ($rs['USTATECODE_NUM'] == 940)
					{
						//승인대기
						$css = 'class="blue"';
					}
					else
					{
						$css = '';
					}					
			?>			
				<tr>
					<td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td><?=$no?></td>
					<td><a href="<?=$url?>" class="alink"><?=$rs['USER_NAME']?></a></td>
					<td><?=$rs['USER_EMAIL_DEC']?></td>
					<td><?=$rs['USER_MOBILE_DEC']?></td>
					<td><?=$rs['USER_BIRTH']?></td>
					<td><?=$userGender?></td>
					<td><?=$rs['ULEVELCODE_TITLE']?></td>
					<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td><span <?=$css?>><?=$rs['USTATECODE_TITLE']?></span></td>
					<td><a href="javascript:messageSend('<?=$rs['USER_NAME']?>', '<?=$rs['NUM']?>', 'user');" class="btn2">메시지</a><a href="javascript:smsSend('<?=$rs['USER_NAME']?>', '<?=$rs['USER_MOBILE_DEC']?>');" class="btn2">SMS</a></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="11">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>					
			</tbody>
		</table>

		<table  style="display: none" class="write3" id="tblExport">
		<!-- <table class="write3" id="tblExport"> -->
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="5%" /><col width="15%" /></colgroup>
			<thead>
				<tr>
					<!-- <th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th> -->
					<th>No</th>
					<th>회원명</th>
					<th>계정(이메일)</th>
					<th>휴대폰</th>
					<th>생년월일</th>
					<th>성별</th>
					<th>가입방법</th>
					<th>가입일</th>
					<th>상태</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/user_m/updateform/uno/'.$rs['NUM'].$addUrl;
					
					if ($rs['USER_GENDER'] == 'M')
					{
						$userGender = '남성';
					}
					else if ($rs['USER_GENDER'] == 'M')
					{
						$userGender = '여성';
					}
					else 
					{
						$userGender = '모름';
					}
					
					if ($rs['USTATECODE_NUM'] == 950)
					{
						//패널티
						$css = 'class="red"';
					}
					else if ($rs['USTATECODE_NUM'] == 940)
					{
						//승인대기
						$css = 'class="blue"';
					}
					else
					{
						$css = '';
					}					
			?>			
				<tr>
					<!-- <td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td> -->
					<td><?=$no?></td>
					<td><?=$rs['USER_NAME']?></td>
					<td><?=$rs['USER_EMAIL_DEC']?></td>
					<td><?=$rs['USER_MOBILE_DEC']?></td>
					<td><?=$rs['USER_BIRTH']?></td>
					<td><?=$userGender?></td>
					<td><?=$rs['ULEVELCODE_TITLE']?></td>
					<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td><span <?=$css?>><?=$rs['USTATECODE_TITLE']?></span></td>
					<td><a href="javascript:messageSend('<?=$rs['USER_NAME']?>', '<?=$rs['NUM']?>', 'user');" class="btn2">메시지</a><a href="javascript:smsSend('<?=$rs['USER_NAME']?>', '<?=$rs['USER_MOBILE_DEC']?>');" class="btn2">SMS</a></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="11">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>					
			</tbody>
		</table>

		<!-- <a id='btnExport' href="#" class="btn1 mg_t10" download="">엑셀다운로드</a> -->

		<script type="text/javascript">
		    $(document).ready(function () {
		 
		        function itoStr($num)
		        {
		            $num < 10 ? $num = '0'+$num : $num;
		            return $num.toString();
		        }
		         
		        var btn = $('#btnExport');
		        var tbl = 'tblExport';
		        //var tbl = 'tblExport :not(checkbox)'; // 이친구를 Jquery Selector로 만져줘야됨 

		        btn.on('click', function () {
		            var dt = new Date();
		            var year =  itoStr( dt.getFullYear() );
		            var month = itoStr( dt.getMonth() + 1 );
		            var day =   itoStr( dt.getDate() );
		            var hour =  itoStr( dt.getHours() );
		            var mins =  itoStr( dt.getMinutes() );
		 
		            var postfix = year + month + day + "_" + hour + mins;
		            var fileName = "UserList_"+ postfix + ".xls";
		 
		            var uri = $("#"+tbl).battatech_excelexport({
		                containerid: tbl
		                , datatype: 'table'
		                , returnUri: true
		            });
		 
		            $(this).attr('download', fileName).attr('href', uri).attr('target', '_blank');
		        });
		    });


	    </script>

		<div class="btn_list">
			<!-- <a href="" class="btn1 fl_l">엑셀다운로드</a> -->
			<a id='btnExport' href="#" class="btn1 fl_l" download="">엑셀다운로드</a>
			<a href="javascript:grpUserChange('dormant', '휴면');" class="btn1 fl_r">휴면계정처리</a>
			<a href="javascript:grpUserChange('delete', '탈퇴');" class="btn1 fl_r">선택삭제</a>
		</div>

		<!-- paging -->
		<div class="pagination cboth"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>	