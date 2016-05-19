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
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>답변상태</th>
					<td>
						<label><input type="radio" id="replystate1" name="replystate" value="" class="inp_radio" <?if (empty($replyState)){?>checked="checked"<?}?> /><span>전체</span></label>
						<label><input type="radio" id="replystate2" name="replystate" value="N" class="inp_radio" <?if ($replyState=='N'){?>checked="checked"<?}?> /><span>문의접수</span></label>
						<label><input type="radio" id="replystate3" name="replystate" value="Y" class="inp_radio" <?if ($replyState=='Y'){?>checked="checked"<?}?> /><span>답변완료</span></label>
					</td>
					<th>문의분류</th>
					<td>
						<label><input type="radio" id="boardcate1" name="boardcate" class="inp_radio" value="" <?if (empty($boardCate)){?>checked="checked"<?}?> /><span>전체</span></label>
					<?
						$i = 2;
						foreach ($qnaCateCdSet as $crs):
							$sel_chk = ($crs['NUM'] == $boardCate) ? 'checked="checked"' : '';
					?>
						<label><input type="radio" id="boardcate<?=$i?>" name="boardcate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
							$i++;
						endforeach;					
					?>
					</td>
				</tr>
				<tr>
					<th>기간선택</th>
					<td>
						<select id="replydatetype" name="replydatetype">
							<option value="" selected="selected">일자선택</option>
							<option value="create" <?if ($replyDateType=='create'){?>selected="selected"<?}?>>등록일</option>
							<option value="reply" <?if ($replyDateType=='reply'){?>selected="selected"<?}?>>답변일</option>
						</select>
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" style="width:70px;" readonly/><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" style="width:70px;" readonly/><a href="javascript:void(0);" id="edateImg" class="calendar"></a>						
					</td>
					<th>검색어</th>
					<td>
						<select id="skey" name="skey">
							<option value="">선택</option>
				    		<option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option>
				    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
						</select>
						<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="inp_sty60" />
					</td>
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
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col width="38%" /><col width="7%" /><col width="7%" /><col width="8%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<th>문의분류</th>
					<th>제목</th>
					<th>작성자</th>
					<th>답변자</th>
					<th>상태</th>
					<th>등록일</th>
					<th>답변일</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$compDate = date("Y-m-d",strtotime("-1 day"));		    	
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/board_m/view/setno/'.$setNum.'/bno/'.$rs['NUM'].$addUrl;
					$dpDisp = ($rs['DEPTH'] > 0) ? str_repeat('&nbsp;', ($rs['DEPTH'] * 2)).'<span class="icn_answer"></span>' : '';
					$replyDisp = ($rs['REPLYCOUNT'] > 0) ? '답변완료' : '문의접수';
					$isNew = (subStr($rs['CREATE_DATE'], 0, 10) > $compDate) ? TRUE : FALSE;
			?>				
				<tr>
					<td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td><?=$no?></td>
					<td><?=$rs['BOARDCATECODE_TITLE']?></td>
					<td class="ag_l"><?=$dpDisp?><a href="<?=$url?>"><?=$rs['TITLE']?></a> <?if ($isNew){?><span class="icn_new"></span><?}?></td>
					<td><?=$rs['USER_NAME']?></td>
					<td><?=$rs['REPLYUSER_NAME']?></td>
					<td><?=$replyDisp?></td>
					<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td><?=subStr($rs['REPLY_UPDATE_DATE'], 0, 10)?></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="9">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>				
			</tbody>
		</table>

		<div class="btn_list">
			<a href="javascript:grpBoardDel();" class="btn1">선택삭제</a>
			<a href="<?=$writeNewUrl?>" class="btn1">신규등록</a>
		</div>

		<!-- paging -->
		<div class="pagination"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		