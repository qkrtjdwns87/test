<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$rvNum = $recordSet['NUM'];
	$name = $recordSet['USER_NAME'];
	$email = $recordSet['USER_EMAIL_DEC'];
	$content = nl2br($recordSet['CONTENT']);
	$createDate = $recordSet['CREATE_DATE'];
	$orgWriteUserNum = $recordSet['USER_NUM'];	//원글쓴이
	$score = $recordSet['SCORE'];
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
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/review_m/list'.$addUrl;
	$updateUrl = '/manage/review_m/updateform/rvno/'.$rvNum.$addUrl;	
	$deleteUrl = '/manage/review_m/delete/rvno/'.$rvNum;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });

	    function reviewUpdate(){
	    	location.href = '<?=$updateUrl?>';
	    }
	    
    	function reviewDel(){
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = '<?=$deleteUrl?>';
			}
    	}
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[후기관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 후기관리</div>
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
			<tr>
				<th>별점</th>
				<td colspan="3"><?=$score?></td>
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
			<a href="javascript:reviewDel();" class="btn3">삭제</a>
			<a href="javascript:reviewUpdate();" class="btn2">수정</a>			
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>
	
	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			