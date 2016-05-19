<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$title = '';
	$name = $sessionData['user_name'];
	$email = '';
	$storyContent = '';
	$storyCnt = 0;
	
	if (isset($pageMethod))
	{
		if ($pageMethod == "updateform")
		{
			//$pageMethod가 replyform인 경우 댓글 달고자 하는 원본글 내용
			$stoNum = $recordSet['NUM'];
			
			$title = $recordSet['TITLE'];
			$name = $recordSet['USER_NAME'];
			$email = $recordSet['USER_EMAIL_DEC'];
			$storyContent = $recordSet['STORY_CONTENT'];
			$orgWriteUserNum = 0;
			
			if ($orgWriteUserNum != $sessionData['user_num'])
			{
				//$this->common->message('작성자만 수정할 수 있습니다.', '', 'self');
			}
		}
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/story_m/list'.$addUrl;
	
	if ($pageMethod == 'writeform')
	{
		$submitUrl = '/manage/story_m/write';
	}
	else if ($pageMethod == 'updateform')
	{
		$submitUrl = '/manage/story_m/update/stono/'.$stoNum;	
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	        CKEDITOR.replace('story_content',
	        {
		        width: '80%',
	            height: '350',
	            toolbar: 'Full'
	        });
	    });

	    var searchIndex; //검색후 결과값 세팅될 index
	    	
		function sendStory(){
			if ($('#title').val() == ''){
				alert('제목을 입력하세요.');
				return;
			}
						
			var content = CKEDITOR.instances.story_content.getData();

			if (content == ''){
				alert('내용을 입력하세요.');
				return;
			}			

			//불필요한 내용 삭제
			var stoDispIndex = $('#storyDsip').children('tbody').length;

			for(i=0; i<=stoDispIndex;i++){
				if (trim($('#userfile'+i).val())=='' && trim($('#userHfile'+i).val())=='' && trim($('#storyurl_'+i).val())=='' && trim($('#storyshop_'+i).val())=='' && trim($('#storyhtml_'+i).val())==''){
					
					$('#storyTbodyDisp_'+i).remove();
				}
			}

			stoDispIndex = $('#storyDsip').children('tbody').length;

			if (stoDispIndex==1){
				alert('최소1개 이상의 모바일앱 내용을 등록하셔야 합니다.');
				return;
			}
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?\n저장하지 않은 정보는 소실됩니다.')){
				var url = '/manage/story_m/filedelete/stono/<?=$stoNum?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}	

		function changeStyle(n, code){
			if (code==1810){
				$('#fileDisp_'+n).show();
				$('#urlDisp_'+n).hide();
				$('#shopDisp_'+n).hide();
				$('#htmlDisp_'+n).hide();
			}else if (code==1820){
				$('#fileDisp_'+n).hide();
				$('#urlDisp_'+n).show();
				$('#shopDisp_'+n).hide();
				$('#htmlDisp_'+n).hide();				
			}else if (code==1830){
				$('#fileDisp_'+n).hide();
				$('#urlDisp_'+n).hide();
				$('#shopDisp_'+n).show();
				$('#htmlDisp_'+n).hide();				
			}else if (code==1840){
				$('#fileDisp_'+n).hide();
				$('#urlDisp_'+n).hide();
				$('#shopDisp_'+n).hide();
				$('#htmlDisp_'+n).show();				
			}			
		}

		function addDispStory(n){
			var stoDispIndex = $('#storyDsip').children('tbody').length;
			var html = '';
			n=parseInt(stoDispIndex)+1;
			
			html += "<tbody id=\"storyTbodyDisp_"+n+"\" style=\"margin-bottom:10px;\">";
			html += "	<input type=\"hidden\" id=\"storyorder_"+n+"\" name=\"story["+n+"][order]\" value=\""+n+"\"/>";
			html += "	<tr>";
			html += "		<th>스타일선택<br /><span class=\"red\">(이미지 가로 OOO pixel)</span></th>";
			html += "		<td class=\"ag_l\">";
			html += "			<ul class=\"board_img_radio\">";
			html += "				<li>";
			html += "					<label><input type=\"radio\" id=\"storystyle_"+n+"_1\" name=\"story["+n+"][style]\" value=\"1810\" checked=\"checked\" class=\"inp_radio\" onclick=\"javascript:changeStyle('"+n+"', '1810');\" /><img src=\"/images/adm/board_01.png\" alt=\"이미지전체\" /></label>";
			html += "					<label><input type=\"radio\" id=\"storystyle_"+n+"_2\" name=\"story["+n+"][style]\" value=\"1820\" class=\"inp_radio\" onclick=\"javascript:changeStyle('"+n+"', '1820');\" /><img src=\"/images/adm/board_03.png\" alt=\"영상url\" /></label>";
			html += "					<label><input type=\"radio\" id=\"storystyle_"+n+"_3\" name=\"story["+n+"][style]\" value=\"1830\" class=\"inp_radio\" onclick=\"javascript:changeStyle('"+n+"', '1830');\" /><img src=\"/images/adm/board_04.png\" alt=\"관련 Craft Shop\" /></label>";
			html += "					<label><input type=\"radio\" id=\"storystyle_"+n+"_4\" name=\"story["+n+"][style]\" value=\"1840\" class=\"inp_radio\" onclick=\"javascript:changeStyle('"+n+"', '1840');\" /><img src=\"/images/adm/board_02.png\" alt=\"관련 Craft Shop\" /></label>";			
			html += "				</li>";
			html += "			</ul>";
			html += "		</td>";
			html += "		<td rowspan=\"4\">";
			html += "			<a href=\"javascript:addDispStory('"+n+"');\" class=\"btn2 mg_b10\">추가</a>";
			html += "			<a href=\"javascript:delDispStory('"+n+"');\" class=\"btn2\">삭제</a>";
			html += "		</td>";
			html += "	</tr>";
			html += "	<tr>";
			html += "		<th rowspan=\"3\">내용</th>";
			html += "		<td id=\"fileDisp_"+n+"\" class=\"ag_l\">";
			html += "			<input type=\"file\" id=\"userfile"+n+"\" name=\"userfile"+n+"\" class=\"inp_file mg_t10\" value=\"\" />";
			html += "			<input type=\"hidden\" id=\"userHfile"+n+"\" name=\"userHfile"+n+"\" value=\"\"/>";
			html += "		</td>";
			html += "	</tr>";
			html += "	<tr id=\"urlDisp_"+n+"\" style=\"display:none;\">";
			html += "		<td class=\"ag_l\">";
			html += "			<input type=\"text\" id=\"storyurl_"+n+"\" name=\"story["+n+"][url]\" value=\"\" class=\"inp_sty80\" />";
			html += "		</td>";
			html += "	</tr>";
			html += "	<tr id=\"shopDisp_"+n+"\" style=\"display:none;\">";
			html += "		<td class=\"ag_l\">";
			html += "			<input type=\"text\" id=\"storyshop_"+n+"\" name=\"story["+n+"][shopname]\" value=\"\" class=\"inp_sty80\" readonly/>";
			html += "			<input type=\"hidden\" id=\"storyshopno_"+n+"\" name=\"story["+n+"][shopno]\" value=\"\"/>";
			html += "			<a href=\"javascript:javascript:shopSearchpop('"+n+"');;\" class=\"btn2\">Shop 찾아보기</a>";
			html += "		</td>";
			html += "	</tr>";
			html += "	<tr id=\"htmlDisp_"+n+"\" style=\"display:none;\">";
			html += "		<td class=\"ag_l\">";
			html += "			<textarea id=\"storyhtml_"+n+"\" name=\"story["+n+"][html]\" rows=\"20\" cols=\"100\"></textarea>";
			html += "		</td>";
			html += "	</tr>";			
			html += "</tbody>";
						
			$('#storyDsip').append(html);			
		}

		function delDispStory(n){
			var stoDispIndex = $('#storyDsip').children('tbody').length;
			$('#storyTbodyDisp_'+n).remove();
		}

		function shopSearchpop(srIndex){
			searchIndex=srIndex;
			shopSearch();
		}
		
		function shopResultSet(shopno, shopname, shopcode){
			var shop=shopname+'('+shopcode+')';
			$('#storyshop_'+searchIndex).val(shop);
			$('#storyshopno_'+searchIndex).val(shopno);
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
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
					<td><input type="text" id="title" name="title" value="<?=$title?>" class="inp_sty80" /></td>
				</tr>
			</tbody>
		</table>

		<table class="write2 mg_t10">
			<thead>
				<tr>
					<th>PC웹 상세내용</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<textarea id="story_content" name="story_content" rows="5" cols="5" class="textarea1"><?=$storyContent?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	
		<table id="storyDsip" class="write2 mg_t10">
			<colgroup><col width="15%" /><col width="75%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">모바일앱 상세내용</th>
				</tr>
			</thead>
			<?
				if (isset($recSubSet)) $storyCnt = count($recSubSet);
 				if ($storyCnt == 0) $storyCnt = 1;
				for($i=0; $i<$storyCnt; $i++)
				{
					$fileNum = 0;					
					$storyStyle = $storyUrl = $storyShopNum = $storyShopName = $storyHtml = '';
					$fileName = $imgUrl = '';
					if (isset($recSubSet[$i]['STORYSTYLECODE_NUM']))
					{
						$storyUrl = $storyShopNum = $storyShopName = $storyHtml = '';
						$storyStyle = $recSubSet[$i]['STORYSTYLECODE_NUM'];
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
						}
					}
			?>
			<tbody id="storyTbodyDisp_<?=$i?>" style="margin-bottom:10px;">
			<input type="hidden" id="storyorder_<?=$i?>" name="story[<?=$i?>][order]" value="<?=$i?>"/>			
				<tr>
					<th>
					<?if ($i == 0){?>
						앱용 배너
					<?}else{?>
						스타일선택<br /><span class="red">(이미지 가로 OOO pixel)</span>
					<?}?>
					
					</th>
					<td class="ag_l">
						<?if ($i == 0){?><span class="red">([필수!]앱에서 사용될 배너 이미지 등록 - 배너등록이외에 추가 버튼클릭하여 상세내용 추가해 주세요)</span><?}?>
						<ul class="board_img_radio" <?if ($i == 0){?>style="display:none;"<?}?>>
							<li>
								<label><input type="radio" id="storystyle_<?=$i?>_1" name="story[<?=$i?>][style]" value="1810" <?if ($storyStyle == 1810 || empty($storyStyle)){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1810');" /><img src="/images/adm/board_01.png" alt="이미지전체" /></label>
								<label><input type="radio" id="storystyle_<?=$i?>_2" name="story[<?=$i?>][style]" value="1820" <?if ($storyStyle == 1820){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1820');" /><img src="/images/adm/board_03.png" alt="영상url" /></label>
								<label><input type="radio" id="storystyle_<?=$i?>_3" name="story[<?=$i?>][style]" value="1830" <?if ($storyStyle == 1830){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1830');" /><img src="/images/adm/board_04.png" alt="관련 Craft Shop" /></label>
								<label><input type="radio" id="storystyle_<?=$i?>_4" name="story[<?=$i?>][style]" value="1840" <?if ($storyStyle == 1840){?>checked="checked"<?}?> class="inp_radio" onclick="javascript:changeStyle('<?=$i?>', '1840');" /><img src="/images/adm/board_02.png" alt="html" /></label>								
							</li>
						</ul>
					</td>
					<td rowspan="4">
						<a href="javascript:addDispStory('<?=$i?>');" class="btn2 mg_b10">추가</a>
						<?if ($i > 0){?>
						<a href="javascript:delDispStory('<?=$i?>');" class="btn2">삭제</a>
						<?}?>
					</td>
				</tr>
				<tr>
					<th rowspan="3">내용</th>
					<td class="ag_l"id="fileDisp_<?=$i?>" <?if ($storyStyle != 1810 && !empty($storyStyle)){?>style="display:none;"<?}?>>
						<a href="/download/route/fno/<?=$fileNum?>" class="alink"><?=$fileName?></a>
						<?if (!empty($fileName)){?><a href="javascript:delFile('<?=$fileNum?>','<?=$i?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
						<br />
						<input type="file" id="userfile<?=$i?>" name="userfile<?=$i?>" value="" class="inp_file mg_t10" />
						<input type="hidden" id="userHfile<?=$i?>" name="userHfile<?=$i?>" value="<?=$fileName?>"/>						
					</td>
				</tr>
				<tr id="urlDisp_<?=$i?>" <?if ($storyStyle != 1820){?>style="display:none;"<?}?>>
					<td class="ag_l">
						<input type="text" id="storyurl_<?=$i?>" name="story[<?=$i?>][url]" value="<?=$storyUrl?>" class="inp_sty80" />
					</td>
				</tr>
				<tr id="shopDisp_<?=$i?>" <?if ($storyStyle != 1830){?>style="display:none;"<?}?>>
					<td class="ag_l">
						<input type="text" id="storyshop_<?=$i?>" name="story[<?=$i?>][shopname]" value="<?=$storyShopName?>" class="inp_sty80" readonly />
						<input type="hidden" id="storyshopno_<?=$i?>" name="story[<?=$i?>][shopno]" value="<?=$storyShopNum?>"/>						
						<a href="javascript:shopSearchpop('<?=$i?>');" class="btn2">Shop 찾아보기</a>
					</td>
				</tr>
				<tr id="htmlDisp_<?=$i?>" <?if ($storyStyle != 1840){?>style="display:none;"<?}?>>
					<td class="ag_l">
						<textarea id="storyhtml_<?=$i?>" name="story[<?=$i?>][html]" rows="20" cols="100"><?=$storyHtml?></textarea>
					</td>
				</tr>				
			</tbody>
			<?
				}
			?>			
		</table>

		<div class="btn_list">
			<a href="javascript:sendStory();" class="btn3">저장</a>
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		