<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$title = '';
	$name = '';
	$content = '';
	$webContent = '';
	$mobileContent = '';
	$startDate = '';
	$endDate = '';
	$viewYn = '';
	$alwayYn = '';
	$summary = '';
	
	$flist = array();
	for($i=0; $i<($fileCnt+1); $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
		$flist[$i]['file_path'] = '';
		$flist[$i]['file_tmpname'] = '';
		$flist[$i]['thumb_yn'] = 'N';
		$flist[$i]['thumb_file_path'] = '';	
	}
	
	if ($pageMethod == "enupdateform")
	{
		$title = $recordSet['TITLE'];
		$name = $recordSet['USER_NAME'];
		$viewYn = $recordSet['VIEW_YN'];		
		$startDate = substr($recordSet['START_DATE'], 0 ,10);
		$endDate = substr($recordSet['END_DATE'], 0 ,10);
		$viewYnTitle = ($viewYn == 'Y') ? '게시' : '미게시';
		$webContent = $recordSet['W_CONTENT'];
		$mobileContent = $recordSet['M_CONTENT'];
		$alwayYn = $recordSet['ALWAYS_YN'];
		$summary = $recordSet['SUMMARY'];
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
				$flist[$i]['file_tmpname'] = $fileSet[$i]['FILE_TEMPNAME'];
				$flist[$i]['file_path'] = $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME'];
				$flist[$i]['thumb_yn'] = $fileSet[$i]['THUMB_YN'];			
				$flist[$i]['thumb_file_path'] = ($fileSet[$i]['THUMB_YN'] == 'Y') ? str_replace('.', '_s.', $flist[$i]['file_path']) : ''; 
			}
		}
	}
	else 
	{
		$enItemSet = array();
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/item_m/enlist/evtype/'.$eventType.$addUrl;	
	$fileDeleteUrl = '/manage/item_m/enfiledelete/evtype/'.$eventType.'/enno/'.$enNum;
	if ($pageMethod == 'enwriteform')
	{
		$submitUrl = '/manage/item_m/enwrite/evtype/'.$eventType;
	}
	else if ($pageMethod == 'enupdateform')
	{
		$submitUrl = '/manage/item_m/enupdate/evtype/'.$eventType.'/sno/'.$sNum.'/enno/'.$enNum.$addUrl;
	}
	
	$defaultImg = '/images/adm/@thumb.gif';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	    	<?if ($eventType == 'e'){?>
	        CKEDITOR.replace('w_content',
	        {
		        width: '80%',
	            height: '350',
	            toolbar: 'Full'
	        });

	        CKEDITOR.replace('m_content',
	    	{
		    	width: '80%',
		    	height: '350',
		    	toolbar: 'Full'
	    	});	        
			<?}?>
			
			$( "#start_date, #end_date" ).datepicker({
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
				$("#start_date").datepicker("show");
			});
			$("#edateImg").click(function() { 
				$("#end_date").datepicker("show");
			});	

		    $('#always_yn').change(function() {
		        if($(this).is(":checked")) {
					$('#dateDisp').hide();
					$('#start_date').val('');
					$('#end_date').val('');
		        }else{
		        	$('#dateDisp').show();
		        }
		    });
	    });
	    	
		function sendEvent(){
			if ($('#title').val() == ''){
				alert('제목을 입력하세요.');
				return;
			}

			if ($('#start_date').val() == ''){
				alert('시작일을 입력하세요.');
				return;
			}			

			if ($('#end_date').val() == ''){
				alert('종료일을 입력하세요.');
				return;
			}	

			<?if ($eventType != 'e'){?>
			var itemIndex = $('#itemDisp').children('tbody').length;
			if (itemIndex <= 0){
				alert('최소 1개 이상의 아이템을 등록하셔야 합니다.');
				return;
			}
			<?}?>

			<?if ($eventType == 'e'){?>
			if ($('#summary').val() == ''){
				alert('이벤트 개요를 입력하세요.');
				return;
			}	
			
			var wcontent = CKEDITOR.instances.w_content.getData();

			if (wcontent == ''){
				alert('내용을 입력하세요.');
				return;
			}

			var mcontent = CKEDITOR.instances.m_content.getData();

			if (mcontent == ''){
				alert('내용을 입력하세요.');
				return;
			}
			<?}?>
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}

		function itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			var itemIndex = $('#itemDisp').children('tbody').length;

			for(i = 0; i < itemIndex; i++){
				if ($('#item\\['+i+'\\]\\[item_num\\]').val() == itemnum){
					alert('동일한 상품이 이미 있습니다.');
					return;
				}
			}

			html = "<tbody id=\"itemDisp_"+itemIndex+"\">";
			html += "	<tr>";
			html += "		<td>"+(itemIndex+1)+"</td>";
			html += "		<td>";
			html += "			<dl class=\"dl_img\">";
			html += "				<dt><img src=\""+itemimgpath+"\" width=\"100\" height=\"100\" alt=\"\"/></dt>";
			html += "				<dd>";
			html += "					<span>"+itemcode+"</span> <span>("+itemshopcode+")</span>";
			html += "				</dd>";
			html += "				<dd><a href=\"/manage/shop_m/view/sno/"+shopnum+"\" class=\"alink\" target=\"_blank\"><img src=\"/images/adm/ico_shop.gif\" alt=\"ico_shop\" class=\"icn_shop\" />"+shopname+"</a></dd>";
			html += "				<dd>"+itemname+"</dd>";
			html += "			</dl>";
			html += "		</td>";
			html += "		<td>";
			html += "			순서:<input type=\"text\" id=\"item["+itemIndex+"][item_order]\" name=\"item["+itemIndex+"][item_order]\" value=\""+(itemIndex+1)+"\" class=\"inp_sty30\" />";
			html += "			<br /><br /><a href=\"javascript:itemDispDel('"+itemIndex+"');\" class=\"btn1\">아이템 삭제</a>";
			html += "			<input type=\"hidden\" id=\"item["+itemIndex+"][item_num]\" name=\"item["+itemIndex+"][item_num]\" value=\""+itemnum+"\" />";
			html += "		</td>";
			html += "	</tr>";
			html += "</tbody>";

			$('#itemDisp').append(html);
			$('#layer_pop').hide();
			$('#popfrm').attr('src', '');
		}

		function itemDispDel(n){
			if (confirm('아이템을 삭제하시겠습니까?\n삭제후 저장하여야만 반영됩니다.')){
				$('#itemDisp_'+n).remove();
				html = "<tbody id=\"itemDisp_"+n+"\"  style=\"display:none;\">";
				html += "	<tr>";
				html += "		<td>"+(n+1)+"</td>";
				html += "		<td></td>";
				html += "		<td>";
				html += "			<input type=\"hidden\" id=\"item["+n+"][item_order]\" name=\"item["+n+"][item_order]\" value=\""+(n+1)+"\" />";
				html += "			<input type=\"hidden\" id=\"item["+n+"][item_num]\" name=\"item["+n+"][item_num]\" value=\"0\" />";
				html += "		</td>";
				html += "	</tr>";
				html += "</tbody>";		
				$('#itemDisp').append(html);
			}
		}

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?\n저장하지 않은 정보는 소실됩니다.')){
				var url = '<?=$fileDeleteUrl?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}		
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<div id="content">

		<div class="title">
			<h2>[<?=$eventTypeTitle?> 관리]</h2>
			<div class="location">Home &gt; Item 관리 &gt; <?=$eventTypeTitle?> 관리</div>
		</div>
		
		<table class="write1">
			<colgroup><col width="15%" /><col /></colgroup>
			<tbody>
				<tr>
					<th>제목</th>
					<td><input type="text" id="title" name="title" value="<?=$title?>" class="inp_sty60" /></td>
				</tr>
				<?if ($eventType != 'g'){?>
				<tr>
					<th>진행기간</th>
					<td>
						<span id="dateDisp" <?if ($alwayYn == 'Y'){?>style="display:none;"<?}?>><input type="text" id="start_date" name="start_date" value="<?=$startDate?>" class="inp_sty10" readonly/><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="end_date" name="end_date" value="<?=$endDate?>" class="inp_sty10" readonly/><a href="javascript:void(0);" id="edateImg" class="calendar"></a></span>
						<?if ($eventType == 'e'){?>
						<label><input type="checkbox" id="always_yn" name="always_yn" value="Y" <?if ($alwayYn == 'Y'){?>checked="checked"<?}?> class="inp_check" />상시</label>
						<?}?>
					</td>
				</tr>
				<?}?>
				<?if ($eventType == 'e'){?>
				<tr>
					<th>이벤트 개요</th>
					<td><input type="text" id="summary" name="summary" value="<?=$summary?>" class="inp_sty90" /></td>
				</tr>
				<tr>
					<th>상세내용01<br />(PC웹용)</th>
					<td class="ag_l">
						<textarea id="w_content" name="w_content" rows="5" cols="5" class="textarea1"><?=$webContent?></textarea>
					</td>
				</tr>
				<tr>
					<th>상세내용02<br />(모바일용)</th>
					<td class="ag_l">
						<textarea id="m_content" name="m_content" rows="5" cols="5" class="textarea1"><?=$mobileContent?></textarea>
					</td>
				</tr>
				<?}?>				
				<?
					if (!empty($flist[0]['file_tmpname']))
					{
						$imgUrl = ($flist[0]['thumb_yn'] == 'Y') ? $flist[0]['thumb_file_path'] : $flist[0]['file_path'];
					}
					else
					{
						$imgUrl = $defaultImg;
					}				
				?>
				<tr <?if ($eventType == 'e'){?>style="display:none;"<?}?>>
					<th class="ag_c va_m"><div><?=$eventTypeTitle?> 상단<br /> 꾸미기 이미지<br /> PC 웹용</div><span class="red">(000 x 000)</span></th>
					<td colspan="5">
						<dl class="dl_img">
							<dt><img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$flist[0]['num']?>" class="alink"><?=$flist[0]['file_name']?></a>
								<?if (!empty($flist[0]['file_name'])){?><a href="javascript:delFile(<?=$flist[0]['num']?>,'0');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile0" name="userfile0" class="inp_file" value="파일찾기" />
								<input type="hidden" id="userHfile0" name="userHfile0" value="<?=$flist[0]['file_name']?>"/>
							</dd>
						</dl>
					</td>
				</tr>
				<?
					if (!empty($flist[1]['file_tmpname']))
					{
						$imgUrl = ($flist[1]['thumb_yn'] == 'Y') ? $flist[1]['thumb_file_path'] : $flist[1]['file_path'];
					}
					else
					{
						$imgUrl = $defaultImg;
					}				
				?>								
				<tr>
					<th class="ag_c va_m"><div>썸네일 이미지01<br /> PC 웹용</div><span class="red">(000 x 000)</span></th>
					<td colspan="5">
						<dl class="dl_img">
							<dt><img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$flist[1]['num']?>" class="alink"><?=$flist[1]['file_name']?></a>
								<?if (!empty($flist[1]['file_name'])){?><a href="javascript:delFile(<?=$flist[1]['num']?>,'1');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile1" name="userfile1" class="inp_file" value="파일찾기" />
								<input type="hidden" id="userHfile1" name="userHfile1" value="<?=$flist[1]['file_name']?>"/>								
							</dd>
						</dl>
					</td>
				</tr>
				<?
					if (!empty($flist[2]['file_tmpname']) && $eventType != 'e')
					{
						$imgUrl = ($flist[2]['thumb_yn'] == 'Y') ? $flist[2]['thumb_file_path'] : $flist[2]['file_path'];
					}
					else
					{
						$imgUrl = $defaultImg;
					}				
				?>				
				<tr>
					<th class="ag_c va_m"><div>썸네일 이미지02<br /> 모바일앱용</div><span class="red">(000 x 000)</span></th>
					<td colspan="5">
						<dl class="dl_img">
							<dt><img src="<?=CDN.$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$flist[2]['num']?>" class="alink"><?=$flist[2]['file_name']?></a>
								<?if (!empty($flist[2]['file_name'])){?><a href="javascript:delFile(<?=$flist[2]['num']?>,'2');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile2" name="userfile2" class="inp_file" value="파일찾기" />
								<input type="hidden" id="userHfile2" name="userHfile2" value="<?=$flist[2]['file_name']?>"/>								
							</dd>
						</dl>
					</td>
				</tr>
				<tr>
					<th>게시여부</th>
					<td>
						<label><input type="radio" id="view_yn" name="view_yn" value="Y" class="inp_radio" <?if($viewYn == 'Y' || empty($viewYn)){?>checked="checked"<?}?> /><span>게시</span></label>
						<label><input type="radio" id="view_yn" name="view_yn" value="N" class="inp_radio" <?if($viewYn == 'N'){?>checked="checked"<?}?> /><span>미게시</span></label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<?if ($eventType != 'e'){?>
		<div class="mg_t20 mg_b10">
 			<!-- 
			<a href="" class="btn1">신상품순으로 정렬</a>
			<a href="" class="btn1">판매량순으로 정렬</a>
			<a href="" class="btn1">Flag건순으로 정렬</a>
 			-->
			<span class="ex">* 광고 Item은 노출순서 지정과 상관없이 최상단에 노출됩니다.</span>
			<a href="javascript:itemSearch();" class="btn1 fl_r" style="margin-bottom:10px;">Item 추가</a>
		</div>

		<table id="itemDisp" class="write2">
			<colgroup><col width="10%" /><col width="80%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>Item</th>
					<th>순서지정</th>
				</tr>
			</thead>
		<?
			$orgItemList = '';
			for($i=0; $i<count($enItemSet); $i++)
			{
				$itemOrder = $i+1;
				
				$img = '';
				$arrFile = explode('|', $enItemSet[$i]['FILE_INFO']);
				if (isset($enItemSet[$i]['FILE_INFO']))
				{
					if ($arrFile[4] == 'Y')	//썸네일생성 여부
					{
						$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
					}
					else
					{
						$img = $arrFile[2].$arrFile[3];
					}
				}
				$fileName = (!empty($img)) ? $img : $defaultImg;				
		?>
			<tbody id="itemDisp_<?=$i?>">
				<tr>
					<td><?=$itemOrder?></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=CDN.$fileName?>" width="100" height="100" alt=""/></dt>
							<dd>
								<span><?=$enItemSet[$i]['ITEM_CODE']?></span> <span>(<?=$enItemSet[$i]['SHOP_CODE']?>)</span>
							</dd>
							<dd><a href="/manage/shop_m/view/sno/<?=$enItemSet[$i]['SHOP_NUM']?>" class="alink" target="_blank"><img src="/images/adm/ico_shop.gif" alt="ico_shop" class="icn_shop" /><?=$enItemSet[$i]['SHOP_NAME']?></a></dd>
							<dd><a href="/manage/item_m/updateform/sno/<?=$enItemSet[$i]['SHOP_NUM']?>/sino/<?=$enItemSet[$i]['SHOPITEM_NUM']?>" class="alink" target="_blank"><?=$enItemSet[$i]['ITEM_NAME']?></a></dd>
						</dl>
					</td>
					<td>
						<!-- 
						<a href="#"><img src="/images/adm/btn_up.gif" alt="위" /></a>
						<a href="#"><img src="/images/adm/btn_down.gif" alt="아래" /></a>
						 -->
						순서:<input type="text" id="item[<?=$i?>][item_order]" name="item[<?=$i?>][item_order]" value="<?=$enItemSet[$i]['ITEM_ORDER']?>" class="inp_sty30" />
						<br /><br /><a href="javascript:itemDispDel('<?=$i?>');" class="btn1">아이템 삭제</a>
						<input type="hidden" id="item[<?=$i?>][item_num]" name="item[<?=$i?>][item_num]" value="<?=$enItemSet[$i]['SHOPITEM_NUM']?>" />						
					</td>
				</tr>
			</tbody>
		<?
				$orgItemList .= $enItemSet[$i]['SHOPITEM_NUM'].',';
			}
			
			$orgItemList = (strlen($orgItemList) > 0) ? substr($orgItemList, 0, -1) : '';
		?>
			<input type="hidden" name="org_item" value="<?=$orgItemList?>" />
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">미리보기</a>
			<a href="javascript:itemSearch();" class="btn1 fl_r">Item 추가</a>
		</div>
		<?}?>

		<div class="btn_list cboth">
			<a href="<?=$listUrl?>" class="btn1">취소</a>
			<a href="javascript:sendEvent();" class="btn3">저장</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		