<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$name = $sessionData['user_name'];
	$email = '';
	$content = '';
	$thread = 0;
	$depth = 0;
	$groupNo = 0;
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
		if ($pageMethod == "updateform" || $pageMethod == "replyform")
		{
			//$pageMethod가 replyform인 경우 댓글 달고자 하는 원본글 내용
			$comtNum = $recordSet['NUM'];
			$thread = $recordSet['THREAD'];
			$depth = $recordSet['DEPTH'];
			$groupNo = $recordSet['GROUPNUM'];
			$parentContent = nl2br($recordSet['CONTENT']);//답변달고자 하는 부모글
			$parentUserName = $recordSet['USER_NAME'];//답변달고자 하는 부모글의 작성자
			$itemName = $recordSet['ITEM_NAME'];
			$itemNum = $recordSet['ITEM_NUM'];
			
			if ($pageMethod == "updateform")
			{
				$name = $recordSet['USER_NAME'];
				$email = $recordSet['USER_EMAIL_DEC'];
				$content = $recordSet['CONTENT'];
				$orgWriteUserNum = $recordSet['USER_NUM'];	//원글쓴이
				
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
	
	$addActionUrl = $addUrl = ($tNum > 0) ? '/t_no/'.$tNum.'/t_info/'.$tblInfo : '';
	$addUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/comment_m/list'.$addUrl;
	
	if ($pageMethod == 'writeform')
	{
		$submitUrl = '/manage/comment_m/write'.$addActionUrl;
	}
	else if ($pageMethod == 'updateform')
	{
		$submitUrl = '/manage/comment_m/update'.$addActionUrl.'/comtno/'.$comtNum;	
	}
	else if ($pageMethod == 'replyform')
	{
		$submitUrl = '/manage/comment_m/reply'.$addActionUrl.'/comtno/'.$comtNum;	
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script type="text/javascript">
	    $(document).ready(function () {
		    
	    });
	    	
		function sendComment(){
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
						
			if ($('#title').val() == ''){
				alert('제목을 입력하세요.');
				return;
			}
						
			if (trim($('#brd_content').val()) == ''){
				alert('내용을 입력하세요.');
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
	<input type="hidden" id="pthread" name="pthread" value="<?=$thread?>"/>
    <input type="hidden" id="pdepth" name="pdepth" value="<?=$depth?>"/>
    <input type="hidden" id="pgroupno" name="pgroupno" value="<?=$groupNo?>"/>
	<div id="content">

		<div class="title">
			<h2>[댓글관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 댓글관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col /></colgroup>
			<tr>
				<th>작성자</th>
				<td><?=$name?></td>
			</tr>
			<tr>
				<th>Item</th>
				<td>
					<input type="text" id="itemtxt" name="itemtxt" value="<?=$itemName?>" class="inp_sty60" readonly/>
					<input type="hidden" id="itemno" name="itemno" value="<?=$itemNum?>"/>
					<a href="javascript:msgItemSearch();" class="btn1">찾아보기</a>				
				</td>
			</tr>			
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
			<a href="javascript:sendComment();" class="btn3">등록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		