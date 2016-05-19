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

		function storySet(stono, storytitle){
			top.storyResultSet(stono, storytitle);
		}
	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title">
		<h3>[Story 검색]</h3>
	</div>

	<form name="srcfrm" method="post" action="<?=$currentUrl?>">
	<table class="write1">
		<colgroup><col width="10%" /></colgroup>
		<tbody>
			<tr>
				<th>검색어</th>
				<td>
			    	<select id="skey" name="skey">
			    		<option value="">선택</option>
			    		<option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option>
			    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
			    	</select>
			    	<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="inp_sty60"/>
				</td>
			</tr>
		</tbody>
	</table>
	</form>
	
	<div class="btn_list">
		<a href="javascript:searchReset();" class="btn1">초기화</a>
		<a href="javascript:search();" class="btn2">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_l">총 <?=number_format($rsTotalCount)?>개</span><!-- <span class="fl_r color_day">2016-01-10 12:30 현재</span> --></div>
	
	<table class="write2 cboth">
		<colgroup><col width="10%" /><col width="30%" /><col width="20%" /><col width="20%" /><col width="20%" /></colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>제목</th>
				<th>작성자</th>
				<th>등록일</th>
				<th>선택</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = 1;
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
				$title = $rs['TITLE'];
				$titleJs = addslashes(htmlspecialchars($title)); 
		?>			
			<tr>
				<td><?=$no?></td>
				<td class="ag_l"><?=$title?></td>
				<td><?=$rs['USER_NAME']?></td>
				<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
				<td><a href="javascript:storySet('<?=$rs['NUM']?>','<?=$titleJs?>');" class="btn2">선택</a></td>
			</tr>
		<?
				$i++;
			endforeach;
			
			if ($rsTotalCount == 0)
			{
		?>
			<tr>
				<td colspan="5">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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