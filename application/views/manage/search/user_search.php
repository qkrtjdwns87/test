<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		function search(){
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function userSet(uno, uname){
			top.userResultSet(uno, uname);
		}
	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title">
		<h3>[회원 검색]</h3>
	</div>

	<form name="srcfrm" method="post" action="<?=$currentUrl?>">
	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>회원명</th>
				<td><input type="text" id="username" name="username" value="<?=$userName?>" class="inp_sty40" /></td>
			</tr>
			<tr>
				<th>계정(이메일)</th>
				<td><input type="text" id="useremail" name="useremail" value="<?=$userEmail?>" class="inp_sty40" /><span class="ex">* 예시) abc@abc.co.kr</span></td>
			</tr>
			<tr>
				<th>휴대폰</th>
				<td><input type="text" id="usermobile" name="usermobile" value="<?=$userMobile?>" class="inp_sty40" /><!-- <span class="ex">* 숫자만 입력해 주십시오.</span> --></td>
			</tr>
			<tr>
				<th>상태</th>
				<td>
					<label><input type="radio" id="userstate" name="userstate" value="" <?if (empty($userState)){?>checked="checked"<?}?> class="inp_radio" /><span>전체</span></label>
					<label><input type="radio" id="userstate" name="userstate" value="930" <?if ($userState == '930'){?>checked="checked"<?}?> class="inp_radio" /><span>정상</span></label>
					<label><input type="radio" id="userstate" name="userstate" value="940" <?if ($userState == '940'){?>checked="checked"<?}?> class="inp_radio" /><span>보호자 동의 대기</span></label>
					<label><input type="radio" id="userstate" name="userstate" value="950" <?if ($userState == '950'){?>checked="checked"<?}?> class="inp_radio" /><span>패널티 회원</span></label>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
		
	<div class="btn_list">
		<a href="javascript:searchReset();" class="btn1">초기화</a>
		<a href="javascript:search();" class="btn2">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_l">총 <?=number_format($rsTotalCount)?>명</span><span class="fl_r color_day">2016-01-10 12:30 현재</span></div>
	
	<table class="write2">
		<colgroup><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>회원명</th>
				<th>계정(이메일)</th>
				<th>휴대폰</th>
				<th>가입일</th>
				<th>상태</th>
				<th>선택</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = 1;
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
	    		$useYnTitle = ($rs['USE_YN'] == 'Y') ? '이용중' : '이용중지';
	    		$userName = $rs['USER_NAME'];
	    		$userNameJs = addslashes(htmlspecialchars($userName));
		?>			
			<tr>
				<td><?=$no?></td>
				<td><a href="/manage/user_m/updateform/uno/<?=$rs['NUM']?>" target="_blank"><?=$rs['USER_NAME']?></a></td>
				<td><?=$rs['USER_EMAIL_DEC']?></td>
				<td><?=$rs['USER_MOBILE_DEC']?></td>
				<td><?=substr($rs['CREATE_DATE'], 0, 10)?></td>
				<td><?=$rs['USTATECODE_TITLE']?></td>
				<td><a href="javascript:userSet('<?=$rs['NUM']?>','<?=$userNameJs?>');" class="btn2">선택</a></td>
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

	<!-- paging -->
	<div class="pagination"><?=$pagination?></div>
	<!--// paging -->
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>			