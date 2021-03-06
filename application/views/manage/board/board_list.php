<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$writeNewUrl = '/manage/board_m/writeform/setno/'.$setNum.$addUrl;
	$deleteUrl = '/manage/board_m/grpdelete/setno/'.$setNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>	
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

		function grpBoardDel(){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
			
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = '<?=$deleteUrl?>?selval='+sel;
			}			
		}
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[<?=$tblTitle?>]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; <?=$tblTitle?></div>
		</div>
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="10%" /></colgroup>
			<?
				$tdCnt = 6;
				$brdCateCdSet = array();
				if (in_array($setNum, array(9100, 9110, 9130, 9140)))
				{
					//샵-써커스QNA, 회원-써커스QNA, FAQ, TERMS
					if (in_array($setNum, array(9100, 9110)))
					{
						$brdCateCdSet = $qnaCateCdSet;
					}
					else if (in_array($setNum, array(9130)))
					{
						$tdCnt = 7;
						$brdCateCdSet = $faqCateCdSet;
					}
					else if (in_array($setNum, array(9140)))
					{
						$tdCnt = 8;
						$brdCateCdSet = $trmCateCdSet;
					}	
			?>
			<tr>
				<th>분류</th>
				<td>
					<label><input type="radio" id="boardcate1" name="boardcate" class="inp_radio" value="" <?if (empty($boardCate)){?>checked="checked"<?}?> /><span>전체</span></label>
				<?
					$i = 2;
					foreach ($brdCateCdSet as $crs):
						$sel_chk = ($crs['NUM'] == $boardCate) ? 'checked="checked"' : '';
				?>
					<label><input type="radio" id="boardcate<?=$i?>" name="boardcate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
				<?
						$i++;
					endforeach;					
				?>
				</td>
			</tr>
			<?
		 		}
		 	?>	
			<?if (in_array($setNum, array(9140))){?>
			<tr>
				<th>사용여부</th>
				<td>
					<label><input type="radio" id="applyyn1" name="applyyn" class="inp_radio" value="" <?if (empty($applyYn)){?>checked="checked"<?}?> /><span>전체</span></label>				
					<label><input type="radio" id="applyyn2" name="applyyn" class="inp_radio" value="Y" <?if ($applyYn == 'Y'){?>checked="checked"<?}?> /><span>사용중</span></label>
					<label><input type="radio" id="applyyn3" name="applyyn" class="inp_radio" value="N" <?if ($applyYn == 'N'){?>checked="checked"<?}?> /><span>미사용</span></label>
				</td>
			</tr>			
			<?}?>		 			
			<tr>
				<th>검색어</th>
				<td>
			    	<select id="skey" name="skey">
			    		<option value="">선택</option>
			    		<option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option>
			    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
			    	</select>
			    	<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="inp_sty90"/>				
				</td>
			</tr>
		</table>
	    </form>		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
		
		<div class="sub_title">총 <?=number_format($rsTotalCount)?>개</div>
		<table class="write2">
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<?if (in_array($setNum, array(9130, 9140))){?>
					<th>분류</th>
					<?}?>
					<th>제목</th>
					<th>작성자</th>
					<?if (in_array($setNum, array(9140))){?>
					<th>시행일</th>
					<?}?>
					<th>등록일</th>
					<th><?if (in_array($setNum, array(9140))){?>구분<?}else{?>조회수<?}?></th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$header = '';
		    	$compDate = date("Y-m-d",strtotime("-1 day"));
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/board_m/view/setno/'.$setNum.'/bno/'.$rs['NUM'].$addUrl;
					$dpDisp = ($rs['DEPTH'] > 0) ? str_repeat('&nbsp;', ($rs['DEPTH'] * 2)).'<span class="icn_answer"></span>' : '';
					$isNew = (subStr($rs['CREATE_DATE'], 0, 10) > $compDate) ? TRUE : FALSE;
					if (in_array($setNum, array(9140))) $header = ($rs['SELECT_YN'] == 'Y') ? '[선택] ' : '[필수] ';
					if (in_array($setNum, array(9140)))
					{
						$lastDisp = ($rs['DEPTH'] == 'Y') ? '선택' : '필수';
					}
					else 
					{
						$lastDisp = number_format($rs['READ_COUNT']);					
					}
					
					$urgencyDisp = ($rs['URGENCY_YN'] == 'Y') ? '<span class="red">[긴급]</span>' : ''; 
			?>				
				<tr>
					<td width="5%"><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td width="5%"><?=$no?></td>
					<?if (in_array($setNum, array(9130, 9140))){?>
					<td width="15%"><?=$rs['BOARDCATECODE_TITLE']?></td>
					<?}?>					
					<td class="ag_l"><?=$urgencyDisp?><?=$header?><?=$dpDisp?><a href="<?=$url?>"><?=$rs['TITLE']?></a> <?if ($isNew){?><span class="icn_new"></span><?}?></td>
					<td width="10%"><?=$rs['USER_NAME']?></td>
					<?if (in_array($setNum, array(9140))){?>
					<td width="10%"><?=subStr($rs['APPLY_DATE'], 0, 10)?></td>
					<?}?>					
					<td width="10%"><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td width="5%"><?=$lastDisp?></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="<?=$tdCnt?>">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>				
			</tbody>
		</table>

		<?if ($isAdmin){?>
		<div class="btn_list">
			<a href="javascript:grpBoardDel();" class="btn1">선택삭제</a>
			<a href="<?=$writeNewUrl?>" class="btn1">신규등록</a>
		</div>
		<?}?>

		<!-- paging -->
		<div class="pagination"><?=$pagination?></div>
		<!--// paging -->
		
	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>	