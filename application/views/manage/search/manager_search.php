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

		function managerSet(uno, uname, utel, umobile){
			var txt = "이름:" + uname + " / 전화번호:" + utel + " / 휴대폰 번호:" + umobile;

			$('#managerDisp', parent.document).empty().html(txt);
			$('#manager_change_yn', parent.document).val('Y');
			$('#manager_change_uno', parent.document).val(uno);
			$('#popfrm', parent.document).attr('src', '');
			$('#layer_pop', parent.document).hide();
		}
	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title">
		<h3>[관리자 검색]</h3>
	</div>
	
	<form name="srcfrm" method="post" action="<?=$currentUrl?>">
	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>관리자명</th>
				<td><input type="text" id="username" name="username" value="<?=$userName?>" class="inp_sty40" /></td>
			</tr>
			<tr>
				<th>계정(이메일)</th>
				<td><input type="text" id="useremail" name="useremail" value="<?=$userEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
			</tr>
		</tbody>
	</table>
	<p class="mg_t10"><label><input type="checkbox" id="useyn" name="useyn" value="Y" <?if ($userUseYn=='Y'){?>checked="checked"<?}?> class="inp_check" /><span>이용정지 관리자 제외</span></label></p>
	</form>
		
	<div class="btn_list">
		<a href="javascript:searchReset();" class="btn1">초기화</a>
		<a href="javascript:search();" class="btn2">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_l">총 <?=number_format($rsTotalCount)?>명</span><!-- <span class="fl_r color_day">2016-01-10 12:30 현재</span> --></div>
	
	<table class="write2">
		<colgroup><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>회원명</th>
				<th>계정(이메일)</th>
				<th>소속</th>
				<th>구분</th>
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
				<td><?=$rs['USER_NAME']?></td>
				<td><?=$rs['USER_EMAIL_DEC']?></td>
				<td><?=$rs['USER_PART']?></td>
				<td><?=$rs['ULEVELCODE_TITLE']?></td>
				<td><?=$useYnTitle?></td>
				<td><a href="javascript:managerSet('<?=$rs['NUM']?>', '<?=$userNameJs?>', '<?=$rs['USER_TEL_DEC']?>', '<?=$rs['USER_MOBILE_DEC']?>');" class="btn2">선택</a></td>
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
