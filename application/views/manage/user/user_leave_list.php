<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>		
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
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[탈퇴관리]</h2>
			<div class="location">Home &gt; 회원관리 &gt; 탈퇴관리</div>
		</div>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>탈퇴유형</th>
					<td>
						<label><input type="radio" id="leaveadminyn1" name="leaveadminyn" value="" <?if (empty($leaveAdminYn)){?>checked="checked"<?}?> class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="leaveadminyn2" name="leaveadminyn" value="N" <?if ($leaveAdminYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>일반탈퇴</span></label>
						<label><input type="radio" id="leaveadminyn3" name="leaveadminyn" value="Y" <?if ($leaveAdminYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>강제탈퇴</span></label>
					</td>
					<th>계정(이메일)</th>
					<td><input type="text" id="useremail" name="useremail" value="<?=$userEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>탈퇴사유</th>
					<td colspan="3">
						<select id="leavereason" name="leavereason">
							<option value="" selected="selected">사유선택</option>
					<?
						$i = 2;
						foreach ($leaveCdSet as $crs):
							if ($crs['NUM'] > 6000)
							{
								$sel_chk = ($crs['NUM'] == $leaveReason) ? 'selected="selected"' : '';								
					?>
							<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['TITLE']?></option>
					<?
								$i++;					
							}
						endforeach;					
					?>								
						</select>
					</td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td colspan="3">
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
		
		<div class="sub_title">총 <?=number_format($rsTotalCount)?>명</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="25%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="35%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<th>계정(이메일)</th>
					<th>가입일</th>
					<th>탈퇴일 <a href="#" class="tooltip" data-tooltip="- 일반탈퇴 : 회원이 직접 탈퇴양식을 작성하여 탈퇴
					- 강제탈퇴 : 관리자에 의해 삭제처리 된 회원"><img src="/images/adm/icn_q.png" alt="물음표" class="icn_q" /></a></th>
					<th>탈퇴유형</th>
					<th>탈퇴사유</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					
					$leaveTitle = ($rs['LEAVE_ADMIN_YN'] == 'Y') ? '강제탈퇴' : '일반탈퇴';
					$reason = $rs['LEAVE_RESONCODE_TITLE']; 
					$reason .= (!empty($rs['LEAVE_REASON'])) ? '<br />'.$rs['LEAVE_REASON'] : '';
			?>				
				<tr>
					<td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td><?=$no?></td>
					<td><?=$rs['USER_EMAIL_DEC']?></td>
					<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td><?=subStr($rs['LEAVE_DATE'], 0, 10)?></td>
					<td><?=$leaveTitle?></td>
					<td><?=$reason?></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="7">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">엑셀다운로드</a>
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