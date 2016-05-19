<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$setNum = $recordSet['SET_NUM'];
	$bNum = $recordSet['NUM'];
	$thread = $recordSet['THREAD'];
	$depth = $recordSet['DEPTH'];
	$groupNo = $recordSet['GROUPNUM'];
	$title = $recordSet['TITLE'];
	$name = $recordSet['USER_NAME'];
	$email = $recordSet['USER_EMAIL_DEC'];
	$content = $recordSet['CONTENT'];
	$readCount = $recordSet['READ_COUNT'];
	$createDate = $recordSet['CREATE_DATE'];
	$replyDate = $recordSet['REPLY_UPDATE_DATE'];
	$orgWriteUserNum = $recordSet['USER_NUM'];	//원글쓴이
	$boardCateTitle = $recordSet['QNACATECODE_TITLE'];
	$replyCount = $recordSet['REPLYCOUNT'];
	$replyUserName = $recordSet['REPLYUSER_NAME'];
	$replyContent  = $recordSet['REPLY_CONTENT'];
	$replyBoardNum  = $recordSet['REPLYBOARD_NUM'];
	$applyDate  = substr($recordSet['APPLY_DATE'], 0, 10);
	$urgencyYn = $recordSet['URGENCY_YN'];
	
	$flist = array();
	for($i=0; $i<$fileCnt; $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
	}
	
	if (isset($fileSet))
	{
		for($i=0; $i<count($fileSet); $i++)
		{
			$flist[$i]['num'] = $fileSet[$i]['NUM'];
			$flist[$i]['file_name'] = $fileSet[$i]['FILE_NAME'];
		}
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/board_m/list/setno/'.$setNum.$addUrl;
	$replyUrl = '/manage/board_m/replyform/setno/'.$setNum.'/bno/'.$bNum.$addUrl;
	if (in_array($setNum, array(9100, 9110))) //샵-써커스QNA, 회원-써커스QNA
	{
		//수정시 답변글만 수정가능
		$updateUrl = '/manage/board_m/updateform/setno/'.$setNum.'/bno/'.$replyBoardNum.$addUrl;
	}
	else 
	{
		$updateUrl = '/manage/board_m/updateform/setno/'.$setNum.'/bno/'.$bNum.$addUrl;	
	}
	$deleteUrl = '/manage/board_m/delete/setno/'.$setNum.'/bno/'.$bNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });

	    function boardReply(){
	    	location.href = '<?=$replyUrl?>';		    
	    }

	    function boardUpdate(){
	    	location.href = '<?=$updateUrl?>';
	    }
	    
    	function boardDel(){
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = '<?=$deleteUrl?>';
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
		
		<table class="write1">
			<colgroup><col width="10%" /><col width="24%" /><col width="10%" /><col width="23%" /><col width="10%" /><col width="23%" /></colgroup>
			<?
				if (in_array($setNum, array(9100, 9110, 9130, 9140)))
				{			
					//샵-써커스QNA, 회원-써커스QNA, FAQ, TERMS
			?>				
			<tr>
				<th>분류</th>
				<td colspan="5"><?=$boardCateTitle?></td>
			</tr>
			<?
				}
			?>								
			<tr>
				<th>제목</th>
				<td colspan="5"><?=$title?></td>
			</tr>
			<tr>
				<th>작성자</th>
				<td><?=$name?></td>
				<th>등록일시</th>
				<td><?=$createDate?></td>
				<th>조회수</th>
				<td><?=number_format($readCount)?></td>
			</tr>
			<?if (in_array($setNum, array(9140))){?>
			<tr>
				<th>시행일</th>
				<td colspan="5"><?=$applyDate?></td>
			</tr>			
			<?}?>
			<?if (in_array($setNum, array(9150))){?>
			<tr>
				<th>긴급공지</th>
				<td colspan="5"><?=($urgencyYn == 'Y') ? '예' : '아니오'?></td>
			</tr>			
			<?}?>						
			<tr>
				<th>내용</th>
				<td colspan="5"><?=$content?></td>
			</tr>
			<?
				for($i=0; $i<$fileCnt; $i++)
				{
			?>
			<tr>
				<th>파일첨부</th>
				<td colspan="5">
					<a href="/download/route/fno/<?=$flist[$i]['num']?>"><?=$flist[$i]['file_name']?></a>
				</td>
			</tr>
			<?
				}
			?>			
		</table>
		
		<?
			if (in_array($setNum, array(9100, 9110))) //샵-써커스QNA, 회원-써커스QNA
			{
				for($i=0; $i<$fileCnt; $i++)
				{	//파일배열 초기화
					$flist[$i]['num'] = '';
					$flist[$i]['file_name'] = '';
				}
				
				if (isset($replyFileSet))
				{
					for($i=0; $i<count($replyFileSet); $i++)
					{
						$flist[$i]['num'] = $replyFileSet[$i]['NUM'];
						$flist[$i]['file_name'] = $replyFileSet[$i]['FILE_NAME'];
					}
				}				
		?>				
		<table class="write1 mg_t10">
			<colgroup><col width="10%" /><col width="40%" /><col width="10%" /><col width="40%" /></colgroup>
			<thead>
				<tr>
					<th colspan="4">답변</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>답변작성자</th>
					<td><?=$replyUserName?></td>
					<th>답변등록일시</th>
					<td><?=$replyDate?></td>					
				</tr>			
				<tr>
					<th>내용</th>
					<td colspan="3"><?=$replyContent?></td>
				</tr>
				<?
					for($i=0; $i<$fileCnt; $i++)
					{
				?>
				<tr>
					<th>파일첨부</th>
					<td colspan="3">
						<a href="/download/route/fno/<?=$flist[$i]['num']?>"><?=$flist[$i]['file_name']?></a>
					</td>
				</tr>
				<?
					}
				?>					
			</tbody>
		</table>
		<?
			}
		?>				
		<div class="btn_list">
		<?if ($isAdmin){?>
			<?if (!in_array($setNum, array(9010, 9020, 9130, 9140))){?>
				<?if ($replyCount == 0){?><a href="javascript:boardReply();" class="btn2">답변</a><?}?>
			<?}?>
			<a href="javascript:boardDel();" class="btn3">삭제</a>
			<a href="javascript:boardUpdate();" class="btn2">수정</a>
		<?}?>			
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>
	
	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			