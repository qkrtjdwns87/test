<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$statsDate = '';
	if ($recordSet)
	{
		$statsDate = date('Y-m-d H:i:s', strtotime($recordSet[0]['UPDATE_DATE'])); 
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		function search(){
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function itemSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			top.itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath);
		}
	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title">
		<h3>[인기 급상승 Item 검색]</h3>
	</div>
	
	<form name="srcfrm" method="post" action="<?=$currentUrl?>">
	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>Item 카테고리</th>
				<td>
					<select id="itemcate" name="itemcate">
						<option value="" selected="selected">카테고리</option>
						<?
							foreach ($itCateSet as $crs):
								$sel_chk = ($crs['NUM'] == $itemCate) ? 'selected="selected"' : '';	//$cateSet[$findKey]['DEL_YN'];								
						?>
						<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['CATE_TITLE']?></option>					
						<?
							endforeach;
						?>
					</select>	
				</td>
			</tr>
			<tr>
				<th rowspan="2">Item</th>
				<td>
					<label><input type="radio" id="itemsearchkey1" name="itemsearchkey" value="name" class="inp_radio" <?if ($itemSearchKey == 'name' || empty($itemSearchKey)){?>checked="checked"<?}?> /><span>이름</span></label>
					<label><input type="radio" id="itemsearchkey2" name="itemsearchkey" value="code" class="inp_radio" <?if ($itemSearchKey == 'code'){?>checked="checked"<?}?> /><span>코드</span></label>
				</td>
			</tr>
			<tr>
				<td class="bo_tn pd_tn"><input type="text" id="itemsearchword" name="itemsearchword" value="<?=$itemSearchWord?>" class="inp_sty90" /></td>
			</tr>
			
			<tr>
				<th rowspan="2">Craft Shop</th>
				<td>
					<label><input type="radio" id="shopsearchkey1" name="shopsearchkey" value="name" class="inp_radio" <?if ($shopSearchKey == 'name' || empty($itemSearchKey)){?>checked="checked"<?}?> /><span>이름</span></label>
					<label><input type="radio" id="shopsearchkey2" name="shopsearchkey" value="code" class="inp_radio" <?if ($shopSearchKey == 'name'){?>checked="checked"<?}?> /><span>코드</span></label>
				</td>
			</tr>
			<tr>
				<td class="bo_tn pd_tn"><input type="text" id="shopsearchword" name="shopsearchword" value="<?=$shopSearchWord?>" class="inp_sty90" /></td>
			</tr>
			<tr>
				<th>작가</th>
				<td><input type="text" id="shopusername" name="shopusername" value="<?=$shopUserName?>" class="inp_sty90" /></td>
			</tr>
			<tr>
				<th>정렬기준</th>
				<td>
					<label><input type="radio" id="orderby" name="orderby" value="sell" class="inp_radio" <?if (empty($orderBy) || $orderBy == 'sell'){?>checked="checked"<?}?>/>판매순위 급상승</label>
					<label><input type="radio" id="orderby" name="orderby" value="flag" class="inp_radio" <?if ($orderBy == 'flag'){?>checked="checked"<?}?>/>Flag순위 급상승</label>				
				</td>
			</tr>			
		</tbody>
	</table>
	</form>	

	<div class="btn_list">
		<a href="javascript:searchReset();" class="btn1">초기화</a>
		<a href="javascript:search();" class="btn2">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_r color_day"><?=$statsDate?> 현재</span></div>
	
	<table class="write2">
		<colgroup><col width="9%" /><col width="9%" /><col width="9%" /><col width="9%" /><col width="10%" /><col /><col width="12%" /><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>판매<br />순위</th>
				<th>판매<br />추세</th>
				<th>Flag<br />순위</th>
				<th>Flag<br />추세</th>				
				<th>Item 코드</th>
				<th>Item 이름</th>
				<th>Craft Shop</th>
				<th>담기</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = 1;
	    	$defaultImg = '/images/adm/@thumb.gif';
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
	    	
				$img = '';
				$arrFile = explode('|', $rs['FILE_INFO']);
				if (count($arrFile) > 0)
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
	    		$itemName = $rs['ITEM_NAME'];
	    		$shopName = $rs['SHOP_NAME'];
	    		$itemNameJs = addslashes(htmlspecialchars($itemName));
	    		$shopNameJs = addslashes(htmlspecialchars($shopName));
	    		$itemName = $this->common->cutStr($itemName, 15, '..');
	    		$shopName = $this->common->cutStr($shopName, 10, '..');
	    		$sellRank = $rs['SELLAMOUNT_RANK'];
	    		$flagRank = $rs['FLAGCOUNT_RANK'];
	    		
	    		if ($rs['SELLAMOUNT_RANK_GAP'] > 0)
	    		{
	    			$sellGap = '<span class="icn_up">'.$rs['SELLAMOUNT_RANK_GAP'].'</span>';
	    		}
	    		else if ($rs['SELLAMOUNT_RANK_GAP'] < 0)
	    		{
	    			$sellGap = '<span class="icn_down">'.$rs['SELLAMOUNT_RANK_GAP'].'</span>';
	    		}
	    		else 
	    		{
	    			$sellGap = '-';
	    		}
	    		
	    		if ($rs['FLAG_RANK_GAP'] > 0)
	    		{
	    			$flagGap = '<span class="icn_up">'.$rs['FLAG_RANK_GAP'].'</span>';
	    		}
	    		else if ($rs['FLAG_RANK_GAP'] < 0)
	    		{
	    			$flagGap = '<span class="icn_down">'.$rs['FLAG_RANK_GAP'].'</span>';
	    		}
	    		else
	    		{
	    			$flagGap = '-';
	    		}	    		
		?>				
			<tr>
				<td><?=number_format($sellRank)?></td>
				<td><?=$sellGap?></td>				
				<td><?=number_format($flagRank)?></td>
				<td><?=$flagGap?></td>				
				<td><?=$rs['ITEM_CODE']?></td>
				<td class="ag_l"><a href="/manage/item_m/updateform/sno/<?=$rs['SHOP_NUM']?>/sino/<?=$rs['SHOPITEM_NUM']?>" class="alink" target="_blank"><?=$itemName?></a></td>
				<td><?=$shopName?></td>
				<td><a href="javascript:itemSet('<?=$rs['SHOP_NUM']?>','<?=$rs['SHOPITEM_NUM']?>','<?=$rs['ITEM_CODE']?>','<?=$rs['SHOP_CODE']?>','<?=$itemNameJs?>','<?=$shopNameJs?>','<?=$fileName?>');" class="btn2">선택</a></td>
			</tr>
		<?
				$i++;
			endforeach;
			
			if ($rsTotalCount == 0)
			{
		?>
			<tr>
				<td colspan="8">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
			</tr>
		<?
			}
		?>	
		</tbody>
	</table>

	<!-- paging -->
	<div class="pagination"><?=$pagination?></div>
	<!--// paging -->
	
	<!-- cart -->
	<!-- 
	<div class="cart">
		<p class="title">선택된 Item</p>
		<ul>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href="" class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href=""class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href=""class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
			<li><label><input type="checkbox" class="inp_check" />크리스마스한정 블랙 클러치 </label><a href=""class="btn_close"><img src="..//images/adm/btn_close.gif" alt="삭제" /></a></li>
		</ul>
		<div class="btn_list">
			<a href="" class="btn2">모두삭제</a>
			<a href="" class="btn2">모두선택</a>
		</div>
	</div>
	 -->
	<!-- //cart -->

</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>		