<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$comtNum = $recordSet['NUM'];
	$thread = $recordSet['THREAD'];
	$depth = $recordSet['DEPTH'];
	$groupNo = $recordSet['GROUPNUM'];
	$name = $recordSet['USER_NAME'];
	$email = $recordSet['USER_EMAIL_DEC'];
	$content = nl2br($recordSet['CONTENT']);
	$createDate = $recordSet['CREATE_DATE'];
	$replyDate = $recordSet['REPLY_UPDATE_DATE'];
	$orgWriteUserNum = $recordSet['USER_NUM'];	//원글쓴이
	$replyCount = $recordSet['REPLYCOUNT'];
	$itemName = $recordSet['ITEM_NAME'];
	$shopName = $recordSet['SHOP_NAME'];
	$itemUrl = '/manage/item_m/updateform/sno/'.$recordSet['SHOP_NUM'].'/sino/'.$recordSet['ITEM_NUM'];
	$shopUrl = '/manage/shop_m/view/sno/'.$recordSet['SHOP_NUM'];
	
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
	
	$addActionUrl = $addUrl = ($tNum > 0) ? '/t_no/'.$tNum.'/t_info/'.$tblInfo : '';
	$addUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/comment_m/list'.$addUrl;
	$replyUrl = '/manage/comment_m/replyform/comtno/'.$comtNum.$addUrl;
	$updateUrl = '/manage/comment_m/updateform/comtno/'.$comtNum.$addUrl;	
	$deleteUrl = '/manage/comment_m/delete'.$addActionUrl.'/comtno/'.$comtNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });

	    function commentReply(){
	    	location.href = '<?=$replyUrl?>';		    
	    }

	    function commentUpdate(){
	    	location.href = '<?=$updateUrl?>';
	    }
	    
    	function commentDel(){
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = '<?=$deleteUrl?>';
			}
    	}
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[댓글관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 댓글관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="10%" /><col width="40%" /><col width="10%" /><col width="40%" /></colgroup>
			<tr>
				<th>작성자</th>
				<td><?=$name?></td>
				<th>등록일시</th>
				<td><?=$createDate?></td>
			</tr>
			<tr>
				<th>Item</th>
				<td><?=$itemName?></td>
				<th>Shop</th>
				<td><?=$shopName?></td>
			</tr>						
			<tr>
				<th>내용</th>
				<td colspan="3"><?=$content?></td>
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
		</table>

		<div class="btn_list">
			<a href="javascript:commentReply();" class="btn2">답변</a>
			<a href="javascript:commentDel();" class="btn3">삭제</a>
			<a href="javascript:commentUpdate();" class="btn2">수정</a>			
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>
	
	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			