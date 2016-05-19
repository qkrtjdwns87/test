<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$writeNewUrl = '/manage/story_m/writeform'.$addUrl;
	$deleteUrl = '/manage/story_m/grpdelete';
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

		function grpStoryDel(){
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
			<h2>[Story]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; Story</div>
		</div>
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<div class="btn_list">
			<span class="fl_l pd_t20">총 <?=number_format($rsTotalCount)?>개</span>
	    	<select id="skey" name="skey">
	    		<option value="">선택</option>
	    		<option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option>
	    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
	    	</select>
	    	<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="inp_sty10 mg_l10"/>			
			<a href="javascript:search();" class="btn1">검색</a>
		</div>
		</form>
		
		<table class="write2 cboth">
			<colgroup><col width="5%" /><col width="5%" /><col /><col width="7%" /><col width="10%" /><col width="5%" /><col width="8%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<th>제목</th>
					<th>작성자</th>
					<th>등록일</th>
					<th>조회수</th>
					<th>총 공유수</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$header = '';
		    	$compDate = date("Y-m-d",strtotime("-1 day"));
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/story_m/view/stono/'.$rs['NUM'].$addUrl;
					$isNew = (subStr($rs['CREATE_DATE'], 0, 10) > $compDate) ? TRUE : FALSE;
			?>				
				<tr>
					<td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td><?=$no?></td>
					<td class="ag_l"><a href="<?=$url?>" class="alink"><?=$rs['TITLE']?></a> <?if ($isNew){?><span class="icn_new"></span><?}?></td>
					<td><?=$rs['USER_NAME']?></td>
					<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td><?=number_format($rs['READ_COUNT'])?></td>
					<td><?=number_format($rs['TOTSHARE_COUNT'])?></td>
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
			<a href="<?=$writeNewUrl?>" class="btn1 fl_r">신규등록</a>
			<a href="javascript:grpStoryDel();" class="btn1 fl_r">선택삭제</a>
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