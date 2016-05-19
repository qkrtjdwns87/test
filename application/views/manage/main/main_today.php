<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/manage/main_m/todaywrite';
	$storyCnt = 8;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var searchIndex; //검색후 결과값 세팅될 index
		$(function() {
			
		});

		var searchIndex; //검색후 결과값 세팅될 index
		
		function itemSearchpop(srIndex){
			searchIndex=srIndex;
			itemSearch();
		}	

		function itemRankSearchpop(srIndex){
			searchIndex=srIndex;
			itemRankSearch();
		}	

		function itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			$('#itemimg_'+searchIndex).attr('src',itemimgpath);
			$('#itemno_'+searchIndex).val(itemnum);
			$('#itemname_'+searchIndex).val(itemname);
			$('#layer_pop').hide();		
			$('#popfrm').attr('src', '');		
		}

		function sendTodayMain(){
			if (trim($('#itemno_0').val()) == ''){
				alert('첫번째 아이템은 꼭 등록하셔야 합니다.');
				return;
			}	

			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();	
		}
		
		function deleteContent(type, no, order){
			var url = '/manage/main_m/todaycontentdelete/mmno/<?=$mmNum?>';
			url += '/return_url/' + $.base64.encode(location.pathname + location.search);
			url += '?type='+type+'&no='+no+'&cnorder='+order;
			
			if (type=='file'){
				$('#userHfile'+order).val('');
			}else if (type=='item'){
				$('#itemno_'+order).val('');
			}				

			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = url;
			} 
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<div id="content">

		<div class="title">
			<h2>[Today’s Pick]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 메인 관리 &gt; Today’s Pick</div>
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="80%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>Item</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
			<?
				$defaultImg = '/images/adm/@thumb.gif';
				for($i=0; $i<$storyCnt; $i++)
				{
					$fi = $i * 2;
					$mmtNum = $todayOrder = 0;
					$itemNum =$itemName = $itemCode = '';
					$shopNum = $shopName = $shopCode = '';
					$fileNum = $mobFileNum = '';
					$fileName = $mobFileName = $imgUrl = $mobImgUrl = '';
					if (isset($recordSet) && isset($recTodaySet))
					{
						$mmtNum = $recTodaySet[$i]['NUM'];
						$todayOrder = $recTodaySet[$i]['TODAY_ORDER'];
						$itemName = $recTodaySet[$i]['ITEM_NAME'];
						$itemCode = $recTodaySet[$i]['ITEM_CODE'];
						$itemNum = $recTodaySet[$i]['SHOPITEM_NUM'];
						$shopName = $recTodaySet[$i]['SHOP_NAME'];
						$shopCode = $recTodaySet[$i]['SHOP_CODE'];
						$shopNum = $recTodaySet[$i]['SHOP_NUM'];
					}
					
					$img = $imgUrl = '';
					if (isset($recTodaySet[$i]['FILE_INFO']))
					{
						$arrFile = explode('|', $recTodaySet[$i]['FILE_INFO']);
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
					
					$todayOrder = ($todayOrder == 0) ? $i : $todayOrder;					
					$imgUrl = (!empty($imgUrl)) ? $imgUrl : $defaultImg;					
			?>
				<tr>
					<td><?=$i+1?></td>
					<td>
						<dl class="dl_img">
							<dt><img id="itemimg_<?=$i?>" src="<?=$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<?if (!empty($itemName)){?>
								<a href="/manage/item_m/updateform/sno/<?=$shopNum?>/sino/<?=$itemNum?>" class="alink" target="_blank"><?=$itemName?></a> <span>(<?=$itemCode?>)</span><?if ($i > 0){?><a href="javascript:deleteContent('item', '<?=$mmtNum?>', '<?=$i?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
								<?}?>
							</dd>
							<dd>
								<?if (!empty($shopName)){?>
								<a href="/manage/shop_m/view/sno/<?=$shopNum?>" class="alink" target="_blank"><img src="/images/adm/ico_shop.gif" alt="ico_shop" class="icn_shop" /><?=$shopName?></a>
								<?}?>
							</dd>							
							<dd>
								<input type="text" id="itemname_<?=$i?>" name="todaymn[<?=$i?>][itemname]" value="" class="inp_sty40" readonly/>
								<input type="hidden" id="itemno_<?=$i?>"  name="todaymn[<?=$i?>][itemno]" value="<?=$itemNum?>" class="inp_sty40" />
								<a href="javascript:itemSearchpop('<?=$i?>');" class="btn2">찾아보기</a>
								<a href="javascript:itemRankSearchpop('<?=$i?>');" class="btn2">판매/Flag 급상승 Item에서 찾아보기</a>
							</dd>
						</dl>
					</td>
					<td>
						<input type="text" id="todayorder_<?=$i?>" name="todaymn[<?=$i?>][order]" value="<?=$todayOrder?>" class="inp_sty60" />
						<input type="hidden" name="todaymn[<?=$i?>][num]" value="<?=$mmtNum?>" />						
					</td>
				</tr>

			<?
				}
			?>
			</tbody>
		</table>

		<div class="btn_list">
			<!-- <a href="" class="btn1">이전으로 되돌리기</a> -->
			<a href="javascript:sendTodayMain();" class="btn2">저장</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			