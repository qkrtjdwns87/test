<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$cateTitle = '';
	$cateCode = 0;
	$cateItemCnt = 0;
	$cateType = '';
	$cateOrder = 0;
	$cateMemo = '';
	$cateUseYn = 'Y';
	$cateShopItemNum = 0;
	$cateUrl = '';
	$itemName = '';
	
	//$ctNum = ($ctNum == 0) ? $ctNum = 1 : $ctNum;
	if ($pageMethod == 'catewriteform') $ctNum = 0;	//신규입력인 경우 ctNum 초기화
	if ($ctNum > 0)
	{
		$findKey = array_search($ctNum, array_column($shopCateSet, 'NUM'));
		if ($findKey >= 0)
		{
			$findResult = $shopCateSet[$findKey];
			if ($findResult)
			{
				$cateTitle = $findResult['CATE_TITLE'];
				$cateCode = $findResult['NUM'];
				$cateItemCnt = $findResult['TOTITEM_COUNT'];
				$cateType = $findResult['CATE_TYPE'];
				$cateOrder = $findResult['CATE_ORDER'];
				$cateMemo = $findResult['CATE_MEMO'];
				$cateUseYn = $findResult['USE_YN'];
				$cateShopItemNum = $findResult['REPRESENT_SHOPITEM_NUM'];
				if (!empty($findResult['ITEM_NAME']))
				{
					$itemName = $findResult['ITEM_NAME'].'<br />'.$findResult['ITEM_CODE'].'('.$findResult['ITEMSHOP_CODE'].')';						
				}
				$cateUrl = $siteDomain;
			}
		}
	}
	else 
	{
		//등록된 카테고리가 없는 경우 폼상태를 신규입력 상태로 변경
		$pageMethod = 'catewriteform';
	}
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/item_m/catelist'.$addUrl;
	$writeNewUrl = '/manage/item_m/catewriteform'.$addUrl;
	$submitUrl = '/manage/item_m/cateupdate';
	
	if ($pageMethod == 'catewriteform')
	{
		$submitUrl = '/manage/item_m/catewrite';		
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
		$(function() {

		});

		function sendCate(){
			if ($('#cate_title').val() == ''){
				alert('카테고리명을 입력하세요.');
				return;
			}

			if ($('#cate_order').val() == ''){
				alert('카테고리 순서를 입력하세요.');
				return;
			}			

			if (!IsNumber($('#cate_order').val())){
				alert('카테고리 순서는 숫자로 입력하세요.');
				return;
			}			

			var listCnt=$('.bo_rn').children('li').length;
			var cate_num='';
			var cate_order='';
			
			for(i = 0; i < listCnt; i++){
				cate_num += $('#cate_list_num'+i).val()+',';
				cate_order += $('#cate_list_order'+i).val()+',';
			}

			if (cate_num.length > 0) cate_num = cate_num.substr(0, cate_num.length-1);
			if (cate_order.length > 0) cate_order = cate_order.substr(0, cate_order.length-1);

			$('#list_num').val(cate_num);
			$('#list_order').val(cate_order);

			document.form.target='hfrm';
			document.form.action='<?=$submitUrl?>';
			document.form.submit();
		}

		function addCate(){
			location.href='<?=$writeNewUrl?>';
		}

		function itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			$('#rep_shopitem_no').val(itemnum);
			$('#itemDisp').html(itemname + '<br />' + itemcode + '('+itemshopcode+')');
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');		
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post">
	<input type="hidden" id="list_order" name="list_order" value=""/>
	<input type="hidden" id="list_num" name="list_num" value=""/>
	<div id="content">

		<div class="title">
			<h2>[카테고리 관리]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 카테고리 관리</div>
		</div>
		
		<div class="fl_l cboth mg_b20" style="width:50%; padding-right:2%;">
			<div class="category" style="width:50%;">
				<div class="tit bo_rn">
					<span class="dp2 fl_l">카테고리 (카테고리 순서)</span>
					<a href="javascript:addCate();" class="btn1 dp2 fl_r">추가</a>
				</div>
				<ul class="bo_rn">
			    <?
			    	$i = 0;
			    	foreach ($shopCateSet as $rs):
						//$url = '/manage/item_m/updateform/sno/'.$rs['SHOP_NUM'].'/sino/'.$rs['NUM'].$addUrl;
				?>						
					<li>
						<span><a href="/manage/item_m/catelist/ctno/<?=$rs['NUM']?>"><?=$rs['CATE_TITLE']?></a></span>
						<div>
							<!-- 
							<a href=""><img src="/images/adm/btn_up.gif" alt="위" /></a> 
							<a href=""><img src="/images/adm/btn_down.gif" alt="아래" /></a>
							 -->
							<input type="text" id="cate_list_order<?=$i?>" name="cate_list_order<?=$i?>" value="<?=$rs['CATE_ORDER']?>" class="inp_sty30 mg_t10" />							
							<input type="hidden" id="cate_list_num<?=$i?>" name="cate_list_num<?=$i?>" value="<?=$rs['NUM']?>"/>
						</div>
					</li>
			<?
						$i++;
					endforeach;
			?>	
				</ul>
			</div>
		</div>

		<table class="write1 fl_l" style="width:48%;">
			<colgroup><col width="30%" /><col width="20%" /><col width="50%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2"><?if ($pageMethod == 'catewriteform'){?>추가할<?}else{?>선택된<?}?> 카테고리 정보</th>
					<th class="ag_r"><span class="important">*</span> 은 필수 입력사항입니다</th>
				</tr>
			</thead>
			<tbody>
				<?if ($pageMethod != 'catewriteform'){?>
				<tr>
					<th>현재 카테고리</th>
					<td colspan="2"><?=$cateTitle?></td>
				</tr>
				<tr>
					<th>카테고리 코드</th>
					<td colspan="2"><?=$cateCode?></td>
				</tr>
				<?}?>				
				<tr>
					<th><span class="important">*</span>카테고리명</th>
					<td colspan="2">
						<input type="text" id="cate_title" name="cate_title" value="<?=$cateTitle?>" class="inp_sty30" />
						<input type="hidden" id="cate_no" name="cate_no" value="<?=$cateCode?>"/>						
					</td>
				</tr>
				<tr>
					<th>카테고리 설명</th>
					<td colspan="2"><input type="text" id="cate_memo" name="cate_memo" value="<?=$cateMemo?>" class="inp_sty60" /></td>
				</tr>
				<tr>
					<th>카테고리 순서</th>
					<td colspan="2"><input type="text" id="cate_order" name="cate_order" value="<?=$cateOrder?>" class="inp_sty60" /></td>
				</tr>				
				<tr>
					<th>카테고리의 Item 수</th>
					<td colspan="2"><?=number_format($cateItemCnt)?> 개</td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 Item 선택</th>
					<td colspan="2">
						<span id="itemDisp"><?=$itemName?></span>
						<a href="javascript:itemSearch();" class="btn1 fl_r" style="margin-bottom:10px;">Item 찾아보기</a>
						<input type="hidden" id="rep_shopitem_no" name="rep_shopitem_no" value="<?=$cateShopItemNum?>"/>
					</td>
				</tr>
				<tr>
					<th>고유주소</th>
					<td colspan="2"><?=$cateUrl?></td>
				</tr>
				<tr>
					<th>사용여부</th>
					<td colspan="2">
						<label><input type="radio" id="use_yn1" name="use_yn" value="Y" <?if ($cateUseYn == 'Y' || empty($cateUseYn)){?>checked="checked"<?}?> class="inp_radio" /><span>사용</span></label>
						<label><input type="radio" id="use_yn2" name="use_yn" value="N" <?if ($cateUseYn == 'N'){?>checked="checked"<?}?> class="inp_radio" /><span>사용안함</span></label>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">
						<a href="javascript:sendCate();" class="btn2">저장</a>					
					</th>
				</tr>			
			</tfoot>
		</table>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		