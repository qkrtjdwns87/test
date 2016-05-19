<?
	defined('BASEPATH') OR exit('No direct script access allowed');
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
		<h3>[Item 검색]</h3>
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
		</tbody>
	</table>
	</form>
	
	<div class="btn_list">
		<a href="javascript:searchReset();" class="btn1">초기화</a>
		<a href="javascript:search();" class="btn2">검색</a>
	</div>
	
	<div class="sub_title"><span class="fl_l">총 <?=number_format($rsTotalCount)?>건</span><!-- <span class="fl_r color_day">2016-01-10 12:30 현재</span> --></div>
	
	<table class="write2">
		<colgroup><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>Item 코드</th>
				<th>Item 이름</th>
				<th>Craft Shop</th>
				<th>작가</th>
				<th>승인일</th>
				<th>선택</th>
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
		?>			
			<tr>
				<td><?=$no?></td>
				<td><?=$rs['ITEM_CODE']?></td>
				<td><?=$rs['ITEM_NAME']?></td>
				<td><?=$rs['SHOP_NAME']?></td>
				<td><?=$rs['SHOPUSER_NAME']?></td>
				<td><?=substr($rs['APPROVAL_DATE'], 0, 10)?></td>
				<td><a href="javascript:itemSet('<?=$rs['SHOP_NUM']?>','<?=$rs['NUM']?>','<?=$rs['ITEM_CODE']?>','<?=$rs['SHOP_CODE']?>','<?=$itemNameJs?>','<?=$shopNameJs?>','<?=$fileName?>');" class="btn2">선택</a></td>
			</tr>
		<?
				$i++;
			endforeach;
			
			if ($rsTotalCount == 0)
			{
		?>
			<tr>
				<td colspan="7">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
			</tr>
		<?
			}
		?>				
		</tbody>
	</table>

	<!-- paging -->
	<div class="pagination"><?=$pagination?></div>
	<!--// paging -->
</div>
<!-- //popup -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>	