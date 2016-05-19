<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$content = nl2br($recordSet['CONTENT']);
	$sendName = $recordSet['SEND_USER_NAME'].' ('.$recordSet['SEND_USER_EMAIL_DEC'].')';
	$toName = $recordSet['TO_USER_NAME'].' ('.$recordSet['TO_USER_EMAIL_DEC'].')';
	$sendUrl = '/manage/user_m/updateform/uno/'.$recordSet['USER_NUM'];
	$toUrl = '/manage/user_m/updateform/uno/'.$recordSet['TOUSER_NUM'];
	if ($recordSet['SENDER_TYPE'] == 'S' && !empty($recordSet['SEND_SHOP_NAME'])) //shop자격으로 발송
	{
		$sendUrl = '/manage/shop_m/view/sno/'.$recordSet['SEND_SHOP_NUM'];
		$sendName = $recordSet['SEND_SHOP_NAME'].' ('.$recordSet['SEND_SHOP_CODE'].')';
	}
	if ($recordSet['TARGET_TYPE'] == 'S' && !empty($recordSet['TO_SHOP_NAME'])) //shop자격으로 수신
	{
		$toUrl = '/manage/shop_m/view/sno/'.$recordSet['TO_SHOP_NUM'];
		$toName = $recordSet['TO_SHOP_NAME'].' ('.$recordSet['TO_SHOP_CODE'].')';
	}
	$createDate = $recordSet['CREATE_DATE'];
	if ($recordSet['READ_YN'] == 'N')
	{
		$css = 'red';
		$readTitle = "미확인";
	}
	else
	{
		$css = '';
		$readTitle = "확인";
	}
	
	$flist = array();
	for($i=0; $i<$fileCnt; $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
		$flist[$i]['file_tempname'] = '';
		$flist[$i]['file_path'] = '';
		$flist[$i]['file_img_yn'] = 'N';
		$flist[$i]['thumb_yn'] = 'N';
		$flist[$i]['thumb_file_path'] = '';
	}
	
	if (isset($fileSet))
	{
		for($i=0; $i<count($fileSet); $i++)
		{
			$flist[$i]['num'] = $fileSet[$i]['NUM'];
			$flist[$i]['file_name'] = $fileSet[$i]['FILE_NAME'];
			$flist[$i]['file_tmpname'] = $fileSet[$i]['FILE_TEMPNAME'];
			$flist[$i]['file_path'] = $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME'];
			$flist[$i]['file_img_yn'] = $fileSet[$i]['IMAGE_YN'];
			$flist[$i]['thumb_yn'] = $fileSet[$i]['THUMB_YN'];			
			$flist[$i]['thumb_file_path'] = ($fileSet[$i]['THUMB_YN'] == 'Y') ? str_replace('.', '_s.', $flist[$i]['file_path']) : ''; 
		}
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/message_m/list';
	if ($pageMethod == 'viewuser')
	{
		$listUrl = '/manage/message_m/listuser';
	}
	else if ($pageMethod == 'viewshop')
	{
		$listUrl = '/manage/message_m/listshop';
	}
	else if ($pageMethod == 'viewusershop')
	{
		$listUrl = '/manage/message_m/listusershop';
	}	
	else if ($pageMethod == 'viewmall')
	{
		$listUrl = '/manage/message_m/listmall';
	}	
	
	$listUrl .= $addUrl;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[메시지 상세]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 메시지상세</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th>송신</th>
					<td><a href="<?=$sendUrl?>" class="alink" target="_blank"><?=$sendName?></a></td>
				</tr>
				<tr>
					<th>수신</th>
					<td><a href="<?=$toUrl?>" class="alink" target="_blank"><?=$toName?></a></td>
				</tr>
				<tr>
					<th>발송일시</th>
					<td><?=$createDate?></td>
				</tr>
				<tr>
					<th>내용</th>
					<td><?=$content?></td>
				</tr>
				<?
					$imgUrl = '';
					$defaultImg = "/images/adm/@thumb.gif";
					for($i=0; $i<$fileCnt; $i++)
					{
						if ($flist[$i]['file_img_yn'] == 'Y')
						{
							if (!empty($flist[$i]['file_tmpname']))
							{
								$imgUrl = ($flist[$i]['thumb_yn'] == 'Y') ? $flist[$i]['thumb_file_path'] : $flist[$i]['file_path'];
							}
							else
							{
								$imgUrl = $defaultImg;
							}							
						}
						
						if (!empty($flist[$i]['file_tmpname']))
						{
				?>
				<tr>
					<th>파일첨부</th>
					<td colspan="5">
						<?if ($flist[$i]['file_img_yn'] == 'Y'){?>
						<img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" />
						<?}?>
						<a href="/download/route/fno/<?=$flist[$i]['num']?>" class="alink"><?=$flist[$i]['file_name']?></a>
					</td>
				</tr>
				<?
						}
					}
				?>						
				<tr>
					<th>수신자 확인여부</th>
					<td><span class="bold <?=$css?>"><?=$readTitle?></span></td>
				</tr>
			</tbody>
		</table>

		<div class="btn_list">
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>				