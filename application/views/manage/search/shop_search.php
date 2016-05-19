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

		function shopSet(shopno, shopname, shopcode){
			top.shopResultSet(shopno, shopname, shopcode);
		}
	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title">
		<h3>[Craft Shop 검색]</h3>
	</div>
	
	<form name="srcfrm" method="post" action="<?=$currentUrl?>">
	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th rowspan="2">Craft Shop</th>
				<td>
					<label><input type="radio" id="shopsearchkey1" name="shopsearchkey" value="name" <?if ($shopSearchKey == 'name' || empty($itemSearchKey)){?>checked="checked"<?}?> class="inp_radio" /><span>이름</span></label>
					<label><input type="radio" id="shopsearchkey2" name="shopsearchkey" value="code" <?if ($shopSearchKey == 'code'){?>checked="checked"<?}?> class="inp_radio" /><span>코드</span></label>
				</td>
			</tr>
			<tr>
				<td class="bo_tn pd_tn"><input type="text" id="shopsearchword" name="shopsearchword" value="<?=$shopSearchWord?>" class="inp_sty90" /></td></td>
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
	
	<div class="sub_title"><span class="fl_l">총 <?=number_format($rsTotalCount)?>명</span><!-- <span class="fl_r color_day">2016-01-10 12:30 현재</span> --></div>
	
	<table class="write2">
		<colgroup><col width="10%" /></colgroup>
		<thead>
			<tr>
				<th>No</th>
				<th>Shop 코드</th>
				<th>Shop명</th>
				<th>작가</th>
				<th>승인일</th>
				<th>선택</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = 1;
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
	    		$shopName = $rs['SHOP_NAME'];
	    		$shopNameJs = addslashes(htmlspecialchars($rs['SHOP_NAME']));
		?>			
			<tr>
				<td><?=$no?></td>
				<td><?=$rs['SHOP_CODE']?></td>
				<td><a href="/manage/shop_m/view/sno/<?=$rs['NUM']?>" target="_blank"><?=$shopName?></a></td>
				<td><?=$rs['SHOPUSER_NAME']?></td>
				<td><?=substr($rs['APPROVAL_DATE'], 0, 10)?></td>
				<td><a href="javascript:shopSet('<?=$rs['NUM']?>','<?=$shopNameJs?>','<?=$rs['SHOP_CODE']?>');" class="btn2">선택</a></td>
			</tr>
		<?
				$i++;
			endforeach;
			
			if ($rsTotalCount == 0)
			{
		?>
			<tr>
				<td colspan="6">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
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