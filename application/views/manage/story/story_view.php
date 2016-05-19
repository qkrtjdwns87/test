<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$storyCnt = 0;
	$stoNum = $recordSet['NUM'];
	$title = $recordSet['TITLE'];
	$name = $recordSet['USER_NAME'];
	$email = $recordSet['USER_EMAIL_DEC'];
	$storyContent = $recordSet['STORY_CONTENT'];
	$orgWriteUserNum = 0;
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/story_m/list'.$addUrl;
	$deleteUrl = '/manage/story_m/delete/stono/'.$stoNum;
	$updateUrl = '/manage/story_m/updateform/stono/'.$stoNum.$addUrl;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?')){
				var url = '/manage/story_m/filedelete/stono/<?=$stoNum?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}	

		function deleteStory(){
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href='<?=$deleteUrl?>';
			}
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<div id="content">

		<div class="title">
			<h2>[Story]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; Story</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col /></colgroup>
			<tbody>
				<tr>
					<th>제목</th>
					<td><?=$title?></td>
				</tr>
				<tr>
					<th>PC웹 상세내용</th>
					<td><?=$storyContent?></td>
				</tr>				
			</tbody>
		</table>
		
		<table id="storyDsip" class="write2 mg_t10">
			<colgroup><col width="15%" /><col width="75%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2">모바일앱 상세내용</th>
				</tr>
			</thead>
			<?
				$defaultImg = '/images/adm/@thumb.gif';
				if (isset($recSubSet)) $storyCnt = count($recSubSet);
 				if ($storyCnt == 0) $storyCnt = 1;
				for($i=0; $i<$storyCnt; $i++)
				{
					$fileNum = 0;
					$storyStyle = $storyUrl = $storyShopNum = $storyShopName = $storyHtml = '';
					$fileName = $imgUrl = '';					
					if (isset($recSubSet[$i]['STORYSTYLECODE_NUM']))
					{
						$storyStyle = $recSubSet[$i]['STORYSTYLECODE_NUM'];
						$storyStyleTitle = $recSubSet[$i]['STORYSTYLECODE_TITLE'];
						if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1820)
						{
							$storyUrl = $recSubSet[$i]['CONTENT'];
						}
						else if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1830)
						{
							$arrShopInfo = explode('|', $recSubSet[$i]['SHOP_INFO']);
							$storyShopName = $arrShopInfo[1].'('.$arrShopInfo[0].')';
							$storyShopNum = $recSubSet[$i]['CONTENT'];							
						}
						else if ($recSubSet[$i]['STORYSTYLECODE_NUM'] == 1840)
						{
							$storyHtml = $recSubSet[$i]['HTML_CONTENT'];
						}						
						else 
						{
							//파일정보
							if (!empty($fileSet[$i]['FILE_NAME']))
							{
								$fileNum = $fileSet[$i]['NUM'];
								$fileName = $fileSet[$i]['FILE_NAME'];
									
								if ($fileSet[$i]['THUMB_YN'] == 'Y')
								{
									$imgUrl = str_replace('.', '_s.', $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME']);
								}
								else
								{
									$imgUrl = $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME'];
								}
							}
							$imgUrl = (!empty($imgUrl)) ? $imgUrl : $defaultImg;
						}
					}
			?>			
			<tbody id="storyTbodyDisp_<?=$i?>" style="margin-bottom:10px;">
				<tr>
					<th>
					<?if ($i == 0){?>
						앱용 배너
					<?}else{?>
						스타일선택<br /><span class="red">(이미지 가로 OOO pixel)</span>
					<?}?>					
					</th>
					<td class="ag_l">
						<?if ($i == 0){?><span class="red">(앱에서 사용될 배너 이미지 입니다.)</span><?}?>
						<ul class="board_img_radio" <?if ($i == 0){?>style="display:none;"<?}?>>
							<li>
								<label><input type="radio" id="storystyle_<?=$i?>_1" name="story[<?=$i?>][style]" value="1810" <?if ($storyStyle == 1810 || empty($storyStyle)){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1810');" disabled/><img src="/images/adm/board_01.png" alt="이미지전체" /></label>
								<label><input type="radio" id="storystyle_<?=$i?>_2" name="story[<?=$i?>][style]" value="1820" <?if ($storyStyle == 1820){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1820');" disabled/><img src="/images/adm/board_03.png" alt="영상url" /></label>
								<label><input type="radio" id="storystyle_<?=$i?>_3" name="story[<?=$i?>][style]" value="1830" <?if ($storyStyle == 1830){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1830');" disabled/><img src="/images/adm/board_04.png" alt="관련 Craft Shop" /></label>							
								<label><input type="radio" id="storystyle_<?=$i?>_4" name="story[<?=$i?>][style]" value="1840" <?if ($storyStyle == 1840){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1840');" disabled/><img src="/images/adm/board_02.png" alt="html" /></label>
								<!-- <label><?=$storyStyleTitle?></label> -->
							</li>
						</ul>
					</td>
				</tr>
				<tr>
					<th rowspan="3">내용</th>
					<td class="ag_l"id="fileDisp_<?=$i?>" <?if ($storyStyle != 1810 && !empty($storyStyle)){?>style="display:none;"<?}?>>
						<img src="<?=$imgUrl?>" width="100" height="100" alt="" />
						<a href="/download/route/fno/<?=$fileNum?>" class="alink"><?=$fileName?></a>
					</td>
				</tr>
				<tr id="urlDisp_<?=$i?>" <?if ($storyStyle != 1820){?>style="display:none;"<?}?>>
					<td class="ag_l"><?=$storyUrl?></td>
				</tr>
				<tr id="shopDisp_<?=$i?>" <?if ($storyStyle != 1830){?>style="display:none;"<?}?>>
					<td class="ag_l"><?=$storyShopName?></td>
				</tr>
				<tr id="htmlDisp_<?=$i?>" <?if ($storyStyle != 1840){?>style="display:none;"<?}?>>
					<td class="ag_l"><?=$storyHtml?></td>
				</tr>				
			</tbody>
			<?
				}
			?>			
		</table>

		<div class="btn_list">
			<a href="javascript:deleteStory();" class="btn3">삭제</a>
			<a href="<?=$updateUrl?>" class="btn2">수정</a>
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		