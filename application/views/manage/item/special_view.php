<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
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
	
	$title = $recordSet['TITLE'];
	$name = $recordSet['USER_NAME'];
	$viewYn = $recordSet['VIEW_YN'];		
	$startDate = substr($recordSet['START_DATE'], 0 ,10);
	$endDate = substr($recordSet['END_DATE'], 0 ,10);
	$viewYnTitle = ($viewYn == 'Y') ? '게시' : '미게시';
	$webContent = $recordSet['W_CONTENT'];
	$mobileContent = $recordSet['M_CONTENT'];
	$alwayYn = $recordSet['ALWAYS_YN'];
	$alwayYnTitle = ($alwayYn == 'Y') ? '상시 이벤트' : '';
	$summary = $recordSet['SUMMARY'];	
		
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

	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';

	$listUrl = '/manage/item_m/enlist/evtype/'.$eventType.$addUrl;
	$deleteUrl = '/manage/item_m/endelete/evtype/'.$eventType.'/enno/'.$enNum;
	$fileDeleteUrl = '/manage/item_m/enfiledelete/evtype/'.$eventType.'/enno/'.$enNum;
	$updateUrl = '/manage/item_m/enupdateform/evtype/'.$eventType.'/sno/'.$sNum.'/enno/'.$enNum.$addUrl;
	
	$defaultImg = '/images/adm/@thumb.gif';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script src="/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
		    /*
	        CKEDITOR.replace('brd_content',
	        {
		        width: '80%',
	            height: '350',
	            toolbar: 'Full'
	        });
	        */

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
	    });

		function delFile(fnum, findex){
			if (confirm('파일을 삭제하시겠습니까?\n저장하지 않은 정보는 소실됩니다.')){
				var url = '<?=$fileDeleteUrl?>/fno/'+fnum+'/findex/'+findex;
				hfrm.location.href = url + '/return_url/' + $.base64.encode(location.pathname + location.search);
			}
		}	

		function eventUpdate(){
			location.href = '<?=$updateUrl?>';
		}

		function eventDel(){
			if (confirm('삭제하시겠습니까?')){
				var url = '<?=$deleteUrl?>';
				url += '/return_url/' + $.base64.encode('<?=$listUrl?>');				
				hfrm.location.href = url;					
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
			<colgroup><col width="15%" /><col width="85%" /></colgroup>
			<tbody>
				<tr>
					<th>제목</th>
					<td><?=$title?></td>
				</tr>
				<?if ($eventType != 'g'){?>
				<tr>
					<th>진행기간</th>
					<td><?=$startDate?> ~ <?=$endDate?> <?=$alwayYnTitle?></td>
				</tr>
				<?}?>
				<?if ($eventType == 'e'){?>
				<tr>
					<th>이벤트 개요</th>
					<td><?=$summary?></td>
				</tr>
				<tr>
					<th>상세내용01<br />(PC웹용)</th>
					<td class="ag_l"><?=$webContent?></td>
				</tr>
				<tr>
					<th>상세내용02<br />(모바일용)</th>
					<td class="ag_l"><?=$mobileContent?></td>
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
						</dl>
					</td>
				</tr>
				<?
					if (!empty($flist[2]['file_tmpname']))
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
						</dl>
					</td>
				</tr>
								
				<tr>
					<th>게시여부</th>
					<td><?=$viewYnTitle?></td>
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
						순서:<?=$enItemSet[$i]['ITEM_ORDER']?>
					</td>
				</tr>
			</tbody>
		<?
			}
		?>
		</table>

		<div class="btn_list">
			<a href="" class="btn1 fl_l">미리보기</a>
		</div>
		<?}?>
		
		<div class="btn_list cboth">
			<a href="javascript:eventDel();" class="btn3">삭제</a>
			<a href="javascript:eventUpdate();" class="btn2">수정</a>
			<a href="<?=$listUrl?>" class="btn1">목록</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		