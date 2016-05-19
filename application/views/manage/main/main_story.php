<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/manage/main_m/storywrite';
	$storyCnt = 2;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var searchIndex; //검색후 결과값 세팅될 index
		$(function() {
			
		});

		var searchIndex; //검색후 결과값 세팅될 index
		
		function shopSearchpop(srIndex){
			searchIndex=srIndex;
			shopSearch();
		}

		function storySearchpop(srIndex){
			searchIndex=srIndex;
			storySearch();
		}		

		function itemSearchpop(srIndex){
			searchIndex=srIndex;
			itemSearch();
		}		

		function shopResultSet(shopno, shopname, shopcode){
			var shop=shopname+'('+shopcode+')';
			$('#storyshop_'+searchIndex).val(shop);
			$('#storyshopno_'+searchIndex).val(shopno);
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}
				
		function storyResultSet(stono, storytitle){
			$('#storyno_'+searchIndex).val(stono);
			$('#storytitle_'+searchIndex).val(storytitle);
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}

		function itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			$('#itemimg_'+searchIndex).attr('src',itemimgpath);
			$('#itemno_'+searchIndex).val(itemnum);
			$('#itemname_'+searchIndex).val(itemname);
			$('#layer_pop').hide();		
			$('#popfrm').attr('src', '');		
		}

		function sendStoryMain(){
			if (trim($('#userfile0').val()) == '' && trim($('#userHfile0').val()) == ''){
				alert('첫번째 웹용 이미지는 꼭 등록하셔야 합니다.');
				return;
			}	

			if (trim($('#userfile1').val()) == '' && trim($('#userHfile1').val()) == ''){
				alert('첫번째 모바일앱용 이미지는 꼭 등록하셔야 합니다.');
				return;
			}			
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();	
		}

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?\n저장하지 않은 정보는 소실됩니다.')){
				var url = '/manage/item_m/filedelete/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}

		function deleteContent(type, no, order){
			var url = '/manage/main_m/storycontentdelete/mmno/<?=$mmNum?>';
			url += '/return_url/' + $.base64.encode(location.pathname + location.search);
			url += '?type='+type+'&no='+no+'&cnorder='+order;
			
			if (type=='file'){
				$('#userHfile'+order).val('');
			}else if (type=='story'){
				$('#storyno_'+order).val('');
			}else if (type=='shop'){

			}else if (type=='item'){
				$('#itemno_'+order).val('');
			}				

			if (confirm('삭제하시겠습니까?')){
				location.href = url;
			} 
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<div id="content">

		<div class="title">
			<h2>[Story]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 메인 관리 &gt; Story</div>
		</div>

		<div class="sub_title">
			<span class="important">*</span>은 필수 입력사항입니다.
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="10%" /><col width="10%" /><col width="70%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>구분</th>
					<th colspan="2">메인 비주얼 이미지</th>
				</tr>
			</thead>
			<tbody>
			<?
				$defaultImg = '/images/adm/@thumb.gif';
				for($i=0; $i<$storyCnt; $i++)
				{
					$fi = $i * 2;
					$mmsNum = $storyNum = $itemNum = $shopNum = '';
					$storyTitle = $itemName = $itemCode = '';
					$shopName = $shopCode = $storyContent = '';
					$fileNum = $mobFileNum = '';
					$fileName = $mobFileName = $imgUrl = $mobImgUrl = '';
					if (isset($recordSet) && isset($recStorySet))
					{
						$mmsNum = $recStorySet[$i]['NUM'];
						$storyTitle = $recStorySet[$i]['STORY_TITLE'];
						$storyNum = $recStorySet[$i]['STORY_NUM'];
						$itemName = $recStorySet[$i]['ITEM_NAME'];
						$itemCode = $recStorySet[$i]['ITEM_CODE'];
						$itemNum = $recStorySet[$i]['SHOPITEM_NUM'];
						$shopName = $recStorySet[$i]['SHOP_NAME'];
						$shopCode = $recStorySet[$i]['SHOP_CODE'];
						$shopNum = $recStorySet[$i]['SHOP_NUM'];
						$storyContent = $recStorySet[$i]['CONTENT'];
						
						if (!empty($fileSet[$fi]['FILE_NAME']))
						{
							$fileNum = $fileSet[$fi]['NUM'];
							$fileName = $fileSet[$fi]['FILE_NAME'];
								
							if ($fileSet[$fi]['THUMB_YN'] == 'Y')
							{
								$imgUrl = str_replace('.', '_s.', $fileSet[$fi]['FILE_PATH'].$fileSet[$fi]['FILE_TEMPNAME']);
							}
							else
							{
								$imgUrl = $fileSet[$fi]['FILE_PATH'].$fileSet[$fi]['FILE_TEMPNAME'];
							}
						}
						
						if (!empty($fileSet[$fi+1]['FILE_NAME']))
						{
							$mobFileNum = $fileSet[$fi+1]['NUM'];
							$mobFileName = $fileSet[$fi+1]['FILE_NAME'];
						
							if ($fileSet[$fi+1]['THUMB_YN'] == 'Y')
							{
								$mobImgUrl = str_replace('.', '_s.', $fileSet[$fi+1]['FILE_PATH'].$fileSet[$fi+1]['FILE_TEMPNAME']);
							}
							else
							{
								$mobImgUrl = $fileSet[$fi+1]['FILE_PATH'].$fileSet[$fi+1]['FILE_TEMPNAME'];
							}
						}						
					}
					
					$imgUrl = (!empty($imgUrl)) ? $imgUrl : $defaultImg;
					$mobImgUrl = (!empty($mobImgUrl)) ? $mobImgUrl : $defaultImg;
			?>
				<tr>
					<td rowspan="5">
						<?=$i+1?>
						<input type="hidden" name="storymn[<?=$i?>][order]" value="<?=$i?>" />
						<input type="hidden" name="storymn[<?=$i?>][num]" value="<?=$mmsNum?>" />
					</td>
					<td rowspan="2"><span class="important">*</span>섬네일</td>
					<td>PC 웹용<br /><span class="red">(000*000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$fileNum?>" class="alink"><?=$fileName?></a>
								<?if (!empty($fileName) && $fi > 1){?><a href="javascript:deleteContent('file', '<?=$fileNum?>', '<?=$fi?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>								
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi?>" name="userfile<?=$fi?>" class="inp_file" value="" />
								<input type="hidden" id="userHfile<?=$fi?>" name="userHfile<?=$fi?>" value="<?=$fileName?>" />
							</dd>
						</dl>
					</td>
				</tr>

				<tr>
					<td>모바일앱용<br /><span class="red">(000*000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=$mobImgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$mobFileNum?>" class="alink"><?=$mobFileName?></a>
								<?if (!empty($mobFileName) && $fi > 1){?><a href="javascript:deleteContent('file', '<?=$mobFileNum?>', '<?=$fi+1?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi+1?>" name="userfile<?=$fi+1?>" class="inp_file" value="" />
								<input type="hidden" id="userHfile<?=$fi+1?>" name="userHfile<?=$fi+1?>" value="<?=$mobFileName?>" />
							</dd>
						</dl>
					</td>
				</tr>

				<tr>
					<td colspan="2"><span class="important">*</span>Story 게시물</td>
					<td class="ag_l">
						<?if (!empty($storyTitle)){?>
						<a href="/manage/story_m/view/stono/<?=$storyNum?>" class="alink" target="_blank"><?=$storyTitle?></a> <a href="javascript:deleteContent('story', '<?=$mmsNum?>', '<?=$i?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a>
						<?}?>
						<p class="mg_t10">
							<input type="text" id="storytitle_<?=$i?>" name="storymn[<?=$i?>][storytitle]" class="inp_sty40" readonly/>
							<input type="hidden" id="storyno_<?=$i?>" name="storymn[<?=$i?>][storyno]" value="<?=$storyNum?>"/>
							<a href="javascript:storySearchpop('<?=$i?>');" class="btn2">찾아보기</a>
						</p>
					</td>
				</tr>

				<tr>
					<td colspan="2"><span class="important">*</span>요약글</td>
					<td>
						<span class="ex fl_l">*(최대 300자)</span><br />
						<textarea id="storycontent_<?=$i?>" name="storymn[<?=$i?>][storycontent]" rows="5" cols="5" class="textarea1"><?=$storyContent?></textarea>
					</td>
				</tr>
				<?
					$img = $imgUrl = '';
					if (isset($recStorySet[$i]['FILE_INFO']))
					{
						$arrFile = explode('|', $recStorySet[$i]['FILE_INFO']);
						if (count($arrFile) > 0)
						{
							if ($arrFile[4] == 'Y')	//썸네일생성 여부
							{
								$imgUrl = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
							}
							else
							{
								$imgUrl = $arrFile[2].$arrFile[3];
							}
						}
					}
					$imgUrl = (!empty($imgUrl)) ? $imgUrl : $defaultImg;
				?>
				<tr>
					<td colspan="2">관련 Item</td>
					<td>
						<dl class="dl_img">
							<dt><img id="itemimg_<?=$i?>" src="<?=$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<?if (!empty($itemName)){?>
								<a href="/manage/item_m/updateform/sno/<?=$shopNum?>/sino/<?=$itemNum?>" class="alink" target="_blank"><?=$itemName?></a> <span>(<?=$itemCode?>)</span><a href="javascript:deleteContent('item', '<?=$mmsNum?>', '<?=$i?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a>
								<?}?>
							</dd>
							<dd>
								<input type="text" id="itemname_<?=$i?>" name="storymn[<?=$i?>][itemname]" value="" class="inp_sty40" readonly/>
								<input type="hidden" id="itemno_<?=$i?>"  name="storymn[<?=$i?>][itemno]" value="<?=$itemNum?>" class="inp_sty40" />
								<a href="javascript:itemSearchpop('<?=$i?>');" class="btn2">찾아보기</a>
							</dd>
						</dl>
					</td>
				</tr>
			<?
				}
			?>
			</tbody>
		</table>

		<div class="btn_list ov_fl">
			<a href="" class="btn1 fl_l">미리보기</a>
			<!-- <a href="" class="btn1">이전으로 되돌리기</a> -->
			<a href="javascript:sendStoryMain();" class="btn2">저장</a>
		</div>
	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		