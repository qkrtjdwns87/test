<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$title = '';
	$name = $sessionData['user_name'];
	$email = '';
	$content = '';
	$thread = 0;
	$depth = 0;
	$groupNo = 0;
	$boardCateNum = 0;
	$applyDate = '';
	$selectYn = '';
	$urgencyYn = '';
	
	$flist = array();
	for($i=0; $i<$fileCnt; $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
	}
	
	if (isset($pageMethod))
	{
		if ($pageMethod == "updateform" || $pageMethod == "replyform")
		{
			//$pageMethod가 replyform인 경우 댓글 달고자 하는 원본글 내용
			$bNum = $recordSet['NUM'];
			$thread = $recordSet['THREAD'];
			$depth = $recordSet['DEPTH'];
			$groupNo = $recordSet['GROUPNUM'];
			$parentContent = $recordSet['CONTENT'];//답변달고자 하는 부모글
			$parentUserName = $recordSet['USER_NAME'];//답변달고자 하는 부모글의 작성자
			$parentTitle = $recordSet['TITLE'];//답변달고자 하는 부모글의 제목		
			$boardCateNum = $recordSet['CATECODE_NUM'];
			$title = '[Re] '.$parentTitle;
			
			if ($pageMethod == "updateform")
			{
				$title = $recordSet['TITLE'];
				$name = $recordSet['USER_NAME'];
				$email = $recordSet['USER_EMAIL_DEC'];
				$content = $recordSet['CONTENT'];
				$orgWriteUserNum = $recordSet['USER_NUM'];	//원글쓴이
				$applyDate = substr($recordSet['APPLY_DATE'], 0, 10);
				$selectYn = $recordSet['SELECT_YN'];
				$urgencyYn = $recordSet['URGENCY_YN'];
				
				if ($orgWriteUserNum != $sessionData['user_num'])
				{
					//$this->common->message('작성자만 수정할 수 있습니다.', '', 'self');
				}
					
				if (isset($fileSet))
				{
					for($i=0; $i<count($fileSet); $i++)
					{
						$flist[$i]['num'] = $fileSet[$i]['NUM'];
						$flist[$i]['file_name'] = $fileSet[$i]['FILE_NAME'];
					}
				}
			}
		}
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/board_m/list/setno/'.$setNum.$addUrl;
	
	if ($pageMethod == 'writeform')
	{
		$submitUrl = '/manage/board_m/write/setno/'.$setNum;
	}
	else if ($pageMethod == 'updateform')
	{
		$submitUrl = '/manage/board_m/update/setno/'.$setNum.'/bno/'.$bNum.$addUrl;	
	}
	else if ($pageMethod == 'replyform')
	{
		$submitUrl = '/manage/board_m/reply/setno/'.$setNum.$addUrl;	
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
			$( "#sdate" ).datepicker({
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
					    
	        CKEDITOR.replace('brd_content',
	        {
		        width: '80%',
	            height: '350',
	            toolbar: 'Full'
	        });
	    });
	    	
		function sendBoard(){
			<?
				if (in_array($setNum, array(9100, 9110, 9130, 9140))) //샵-써커스QNA, 회원-써커스QNA, FAQ, TERMS
				{			
			?>
			var sel = $(':radio[name="board_cate"]:checked').val();
			if (sel == '' || sel == undefined){
				alert('문의분류 구분을 선택하세요.');
				return;				
			}
			<?
				}
			?>
			<?if (in_array($setNum, array(9140))){?>
			var seltype = $(':radio[name="select_yn"]:checked').val();
			if (seltype == '' || seltype == undefined){
				alert('구분을 선택하세요.');
				return;				
			}	

			if ($('#sdate').val() == ''){
				alert('시행일을 입력하세요.');
				return;
			}
			<?}?>
						
			if ($('#title').val() == ''){
				alert('제목을 입력하세요.');
				return;
			}
						
			var content = CKEDITOR.instances.brd_content.getData();

			if (content == ''){
				alert('내용을 입력하세요.');
				return;
			}			

			//document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}
	</script>
<!-- container -->
<div id="container">
    <form name="form" method="post" enctype="multipart/form-data">
	<input type="hidden" id="pthread" name="pthread" value="<?=$thread?>"/>
    <input type="hidden" id="pdepth" name="pdepth" value="<?=$depth?>"/>
    <input type="hidden" id="pgroupno" name="pgroupno" value="<?=$groupNo?>"/>
	<div id="content">

		<div class="title">
			<h2>[<?=$tblTitle?>]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; <?=$tblTitle?></div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col /></colgroup>
			<?
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
						$brdCateCdSet = $faqCateCdSet;
					}
					else if (in_array($setNum, array(9140)))
					{
						$brdCateCdSet = $trmCateCdSet;
					}		
			?>			
			<tr>
				<th>문의분류</th>
				<td>
				<?
					$i = 1;
					foreach ($brdCateCdSet as $crs):
						$sel_chk = ($crs['NUM'] == $boardCateNum) ? 'checked="checked"' : '';
				?>
					<label><input type="radio" id="board_cate<?=$i?>" name="board_cate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
				<?
						$i++;
					endforeach;					
				?>					
				</td>
			</tr>
			<?
				}
			?>
			<tr>
				<th>제목</th>
				<td><input type="text" id="title" name="title" value="<?=$title?>" class="inp_sty90" /></td>
			</tr>
			<tr>
				<th>작성자</th>
				<td><?=$name?></td>
			</tr>
			<?if (in_array($setNum, array(9140))){?>
			<tr>
				<th>구분</th>
				<td>
					<label><input type="radio" id="select_yn1" name="select_yn" value="Y" <?if ($selectYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>선택</span></label>				
					<label><input type="radio" id="select_yn2" name="select_yn" value="N" <?if ($selectYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>필수</span></label>
				</td>
			</tr>				
			<tr>
				<th>시행일</th>
				<td><input type="text" id="sdate" name="apply_date" value="<?=$applyDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a></td>
			</tr>			
			<?}?>	
			<?if (in_array($setNum, array(9150))){?>
			<tr>
				<th>긴급공지</th>
				<td>
					<label><input type="checkbox" id="urgency_yn" name="urgency_yn" value="Y" <?if ($urgencyYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" /><span>예 (체크시 긴급공지로 공지됩니다.)</span></label>
				</td>
			</tr>				
			<?}?>						
			<?if ($pageMethod == 'replyform'){?>
			<tr>
				<th>원본 내용</th>
				<td><?=$parentContent?></td>
			</tr>			
			<?}?>
			<tr>
				<th>내용</th>
				<td>
					<textarea id="brd_content" name="brd_content" rows="5" cols="5" class="textarea1"><?=$content?></textarea>
				</td>
			</tr>
			<?
				for($i=0; $i<$fileCnt; $i++)
				{
			?>
			<tr>
				<th>파일첨부</th>
				<td>
					<input type="file" name="userfile<?=$i?>" class="inp_file" value="찾아보기"> 
					<?if (!empty($flist[$i]['file_name'])){?>첨부파일:<?=$flist[$i]['file_name']?><?}?>
				</td>
			</tr>
			<?
				}
			?>
		</table>
		<div class="btn_list">
			<a href="<?=$listUrl?>" class="btn1">취소</a>
			<a href="javascript:sendBoard();" class="btn3">등록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		