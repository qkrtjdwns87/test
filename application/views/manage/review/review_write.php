<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$name = $sessionData['user_name'];
	$email = '';
	$content = '';
	$score = 0;
	$itemName = '';
	$itemNum = '';
	
	$flist = array();
	for($i=0; $i<$fileCnt; $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
	}
	
	if (isset($pageMethod))
	{
		if ($pageMethod == "updateform")
		{
			//$pageMethod가 replyform인 경우 댓글 달고자 하는 원본글 내용
			$rvNum = $recordSet['NUM'];
			$name = $recordSet['USER_NAME'];
			$email = $recordSet['USER_EMAIL_DEC'];
			$content = $recordSet['CONTENT'];
			$orgWriteUserNum = $recordSet['USER_NUM'];	//원글쓴이
			$score = $recordSet['SCORE'];
			$itemName = $recordSet['ITEM_NAME'];
			$itemNum = $recordSet['ITEM_NUM'];
						
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
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/review_m/list'.$addUrl;
	
	if ($pageMethod == 'writeform')
	{
		$submitUrl = '/manage/review_m/write';
	}
	else if ($pageMethod == 'updateform')
	{
		$submitUrl = '/manage/review_m/update/rvno/'.$rvNum.$addUrl;	
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script type="text/javascript">
	    $(document).ready(function () {
		    
	    });
	    	
		function sendReview(){
			if ($('#itemno').val() == ''){
				alert('아이템을 선택 하세요.');
				return;
			}

			var arrItem = $('#itemno').val().split(',');
			if (arrItem.length > 1){
				alert('아이템은 1건만 가능합니다.');
				$('#itemno').val('');
				$('#itemtxt').val('');
				return;
			}		
						
			if (trim($('#review_content').val()) == ''){
				alert('내용을 입력하세요.');
				return;
			}			

			sel = $(':radio[name="score"]:checked').val();
			if (sel == '' || sel == undefined){
				alert('점수를 선택하세요.');
				return;				
			}			

			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}

		function msgItemSearch(){
			/*
			var arrItem = $('#itemno').val().split(',');
			if (arrItem != '' && arrItem.length >= 1){
				alert('검색은 1건씩만 가능합니다.');
				return;
			}
			*/			
			itemSearch();
		}
		
		function itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			var targetNo=$('#itemno').val();
			var targetTxt=$('#item_txt').val();
			if (targetNo == ''){
				$('#itemno').val(itemnum);
				$('#itemtxt').val(itemname);
			}else{
				$('#itemno').val(targetNo+','+itemnum);
				$('#itemtxt').val(targetTxt+','+itemname);
			}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}			
	</script>
<!-- container -->
<div id="container">
    <form name="form" method="post" enctype="multipart/form-data">
	<div id="content">

		<div class="title">
			<h2>[후기관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 후기관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col /></colgroup>
			<tr>
				<th>작성자</th>
				<td><?=$name?></td>
			</tr>
			<tr <?if ($pageMethod == "updateform"){?>style="display:none;"<?}?>>
				<th>Item</th>
				<td>
					<input type="text" id="itemtxt" name="itemtxt" value="<?=$itemName?>" class="inp_sty60" readonly/>
					<input type="hidden" id="itemno" name="itemno" value="<?=$itemNum?>"/>
					<a href="javascript:msgItemSearch();" class="btn1">찾아보기</a>				
				</td>
			</tr>			
			<tr>
				<th>내용</th>
				<td>
					<textarea id="review_content" name="review_content" rows="5" cols="5" class="textarea1"><?=$content?></textarea>
				</td>
			</tr>
			<tr>
				<th>별점</th>
				<td>
					<label><input type="radio" id="score1" name="score" value="1" class="inp_radio" <?if ($score == 1){?>checked="checked"<?}?> /><span>1점</span></label>
					<label><input type="radio" id="score2" name="score" value="2" class="inp_radio" <?if ($score == 2){?>checked="checked"<?}?> /><span>2점</span></label>
					<label><input type="radio" id="score3" name="score" value="3" class="inp_radio" <?if ($score == 3){?>checked="checked"<?}?> /><span>3점</span></label>
					<label><input type="radio" id="score4" name="score" value="4" class="inp_radio" <?if ($score == 4){?>checked="checked"<?}?> /><span>4점</span></label>
					<label><input type="radio" id="score5" name="score" value="5" class="inp_radio" <?if ($score == 5){?>checked="checked"<?}?> /><span>5점</span></label>
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
			<a href="javascript:sendReview();" class="btn3">등록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		