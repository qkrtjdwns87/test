<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/item_m/enlist/evtype/'.$eventType.$addUrl;
	$writeNewUrl = '/manage/item_m/enwriteform/evtype/'.$eventType.$addUrl;
	$deleteUrl = '/manage/item_m/grpendelete/evtype/'.$eventType;
	
	$colspan = 10;
	if ($eventType == 'g') $colspan = 9;
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

		function grpEventDel(){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
			
			if (confirm('삭제하시겠습니까?')){
				var url = '<?=$deleteUrl?>';
				url += '/return_url/' + $.base64.encode(location.pathname + location.search);				
				hfrm.location.href = url + '?selval='+sel;	
			}			
		}		
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[<?=$eventTypeTitle?> 관리]</h2>
			<div class="location">Home &gt; Item 관리 &gt; <?=$eventTypeTitle?> 관리</div>
		</div>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
			<?if ($eventType == 'g'){?>			
				<tr>
					<th>게시여부</th>
					<td>
						<label><input type="radio" id="viewyn1" name="viewyn" value="" <?if (empty($viewYn)){?>checked="checked"<?}?> class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="viewyn2" name="viewyn" value="Y" <?if ($viewYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>게시</span></label>
						<label><input type="radio" id="viewyn3" name="viewyn" value="N" <?if ($viewYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>미게시</span></label>
					</td>
					<th>검색어</th>
					<td>
				    	<select id="skey" name="skey">
				    		<option value="">선택</option>
				    		<option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option>
				    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
				    	</select>
			    	<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="inp_sty30"/>					
				</tr>
			<?}else{?>							
				<tr>
					<th>진행상태</th>
					<td>
						<label><input type="radio" id="eventstate" name="eventstate" value="" <?if (empty($eventState)){?>checked="checked"<?}?> class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="eventstate" name="eventstate" value="ing" <?if ($eventState == 'ing'){?>checked="checked"<?}?> class="inp_radio" /><span>진행중</span></label>
						<label><input type="radio" id="eventstate" name="eventstate" value="exp" <?if ($eventState == 'exp'){?>checked="checked"<?}?> class="inp_radio" /><span>진행예정</span></label>
						<label><input type="radio" id="eventstate" name="eventstate" value="end" <?if ($eventState == 'end'){?>checked="checked"<?}?> class="inp_radio" /><span>종료</span></label>
					</td>
					<th>게시여부</th>
					<td>
						<label><input type="radio" id="viewyn1" name="viewyn" value="" <?if (empty($viewYn)){?>checked="checked"<?}?> class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="viewyn2" name="viewyn" value="Y" <?if ($viewYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>게시</span></label>
						<label><input type="radio" id="viewyn3" name="viewyn" value="N" <?if ($viewYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>미게시</span></label>
					</td>
				</tr>
				<tr>
					<th>진행기간</th>
					<td colspan="3">
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>검색어</th>
					<td colspan="3">
			    	<select id="skey" name="skey">
			    		<option value="">선택</option>
			    		<option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option>
			    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
			    	</select>
			    	<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="inp_sty30"/>					
				</tr>
			<?}?>				
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
		
		<div class="sub_title2">총 <?=number_format($rsTotalCount)?>개</div> 
		<table class="write2">
			<thead>
				<tr>
					<th><label class="mgn"><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check mgn" /><span class="blind">선택</span></label></th>
					<th>No</th>
					<?if ($eventType != 'g'){?>					
					<th>상태</th>
					<?}?>					
					<th>제 목</th>
					<?if ($eventType != 'e'){?>	
					<th>Item개수</th>
					<?}?>
					<?if ($eventType != 'g'){?>					
					<th>진행기간</th>
					<?}?>					
					<th>게시여부</th>
					<th>작성자</th>
					<th>등록일</th>
					<th>조회수</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$compDate = date("Y-m-d",strtotime("-1 day"));
		    	$toDate = date("Y-m-d");
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
		    		$shopNum = $this->common->nullCheck($rs['SHOP_NUM'], 'int', 0);
					$url = '/manage/item_m/enview/evtype/'.$eventType.'/sno/'.$shopNum.'/enno/'.$rs['NUM'].$addUrl;
					$isNew = (subStr($rs['CREATE_DATE'], 0, 10) > $compDate) ? TRUE : FALSE;
					$viewTitle = ($rs['VIEW_YN'] == 'Y') ? '게시' : '미게시';
					
					if ((subStr($rs['START_DATE'], 0, 10) > $toDate))
					{
						$progTitle = '진행예정';
					}
					else if ((subStr($rs['END_DATE'], 0, 10) < $toDate))
					{
						$progTitle = '종료';
					}
					else 
					{
						$progTitle = '진행중';
					}
			?>				
				<tr>
					<td width="3%"><label class="mgn"><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check mgn"><span class="blind">선택</span></label></td>
					<td width="5%"><?=$no?></td>
					<?if ($eventType != 'g'){?>					
					<td width="7%"><?=$progTitle?></td>
					<?}?>					
					<td class="ag_l"><a href="<?=$url?>" class="alink"><?=$rs['TITLE']?></a> <?if ($isNew){?><span class="icn_new"></span><?}?></td>
					<?if ($eventType != 'e'){?>						
					<td width="8%"><?=number_format($rs['TOTITEM_COUNT'])?></td>
					<?}?>					
					<?if ($eventType != 'g'){?>					
					<td width="12%"><span><?=subStr($rs['START_DATE'], 0, 10)?></span> ~ <div><?=subStr($rs['END_DATE'], 0, 10)?></div></td>
					<?}?>	
					<td width="8%"><?=$viewTitle?></td>
					<td width="10%"><?=$rs['USER_NAME']?></td>
					<td width="10%"><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td width="7%"><?=number_format($rs['READ_COUNT'])?></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="<?=$colspan?>" class="ag_c pd_t20 pd_b20">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다.</td>
				</tr>
			<?
				}
			?>					
			</tbody>
		</table>

		<div class="btn_list">
			<a href="javascript:grpEventDel();" class="btn1">선택삭제</a>
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