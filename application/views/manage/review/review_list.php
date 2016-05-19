<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$writeNewUrl = '/manage/review_m/writeform';
	$deleteUrl = '/manage/review_m/delete/rvno/'.$rvNum;
	$deleteGrpUrl = '/manage/review_m/grpdelete';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		$(function() {
			
		});

		function search(){
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function grpReviewDel(){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
			
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = '<?=$deleteGrpUrl?>?selval='+sel;
			}			
		}

		function msgItemSearch(){
			/*
			var arrItem = $('#senditemno').val().split(',');
			if (arrItem != '' && arrItem.length >= 1){
				alert('검색은 1건씩만 가능합니다.');
				return;
			}
			*/			
			itemSearch();
		}
		
		function msgShopSearch(){
			/*
			var arrShop = $('#sendshopno').val().split(',');
			if (arrShop != '' && arrShop.length >= 1){
				alert('검색은 1건씩만 가능합니다.');
				return;
			}
			*/
			shopSearch();
		}
		
		function itemResultSet(shopnum, itemnum, itemcode, itemshopcode, itemname, shopname, itemimgpath){
			var targetNo=$('#senditemno').val();
			var targetTxt=$('#senditemtxt').val();
			if (targetNo == ''){
				$('#senditemno').val(itemnum);
				$('#senditemtxt').val(itemname);
			}else{
				$('#senditemno').val(targetNo+','+itemnum);
				$('#senditemtxt').val(targetTxt+','+itemname);
			}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}	

		function shopResultSet(shopno, shopname, shopcode){
			var shop=shopname+'('+shopcode+')';
			var targetNo=$('#sendshopno').val();
			var targetTxt=$('#sendshoptxt').val();
			if (targetNo == ''){
				$('#sendshopno').val(shopno);
				$('#sendshoptxt').val(shop);
			}else{
				$('#sendshopno').val(targetNo+','+shopno);
				$('#sendshoptxt').val(targetTxt+','+shop);
			}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[후기관리]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; 후기관리</div>
		</div>
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="10%" /><col width="40%" /><col width="10%" /><col width="40%" /></colgroup>
			<tbody>
				<tr>
					<th>Craft Shop</th>
					<td>
						<input type="text" id="sendshoptxt" name="sendshoptxt" value="<?=$sendShopTxt?>" class="inp_sty60" readonly/>
						<input type="hidden" id="sendshopno" name="sendshopno" value="<?=$sendShopNum?>"/>
						<a href="javascript:msgShopSearch();" class="btn1">찾아보기</a>
					</td>

					<th>Item</th>
					<td>
						<input type="text" id="senditemtxt" name="senditemtxt" value="<?=$sendItemTxt?>" class="inp_sty60" readonly/>
						<input type="hidden" id="senditemno" name="senditemno" value="<?=$sendItemNum?>"/>
						<a href="javascript:msgItemSearch();" class="btn1">찾아보기</a>
					</td>
				</tr>
				<tr>
					<th>검색어</th>
					<td colspan="3">
			    	<select id="skey" name="skey" class="inp_select">
			    		<option value="">선택</option>
			    		<!-- <option value="title" <?if ($searchKey=='title'){?>selected="selected"<?}?>>제목</option> -->
			    		<option value="content" <?if ($searchKey=='content'){?>selected="selected"<?}?>>내용</option>
			    	</select>
			    	<input type="text" id="sword" name="sword" value="<?=$searchWord?>" onkeydown="javascript:if(event.keyCode==13){searchSend(); return false;}" class="mg_l10 inp_sty40"/>					
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
		
		<div class="sub_title">총 <?=number_format($rsTotalCount)?>개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="5%" /><col width="10%" /><col /><col width="10%" /><col width="8%" /><col width="15%" /><col width="6%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<th>등록일</th>
					<th>내용</th>
					<th>작성자</th>
					<th>IP</th>
					<th>후기가 달린<br />Item/Craft Shop</th>
					<th>별점</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$header = '';
		    	$compDate = date("Y-m-d",strtotime("-1 day"));
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/review_m/view/rvno/'.$rs['NUM'].$addUrl;
					$isNew = (subStr($rs['CREATE_DATE'], 0, 10) > $compDate) ? TRUE : FALSE;
					$content = $this->common->cutStr(nl2br($rs['CONTENT']), 30, '...');
					$itemName = $this->common->cutStr($rs['ITEM_NAME'], 10, '..');
					$shopName = $this->common->cutStr($rs['SHOP_NAME'], 10, '..');
					$itemUrl = '/manage/item_m/updateform/sno/'.$rs['SHOP_NUM'].'/sino/'.$rs['ITEM_NUM'];
					$shopUrl = '/manage/shop_m/view/sno/'.$rs['SHOP_NUM'];
					$userUrl = '/manage/user_m/updateform/uno/'.$rs['USER_NUM'];
			?>			
				<tr>
					<td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td><?=$no?></td>
					<td><?=subStr($rs['CREATE_DATE'], 0, 10)?></td>
					<td class="ag_l"><a href="<?=$url?>" class="alink"><?=$content?></a> <?if ($isNew){?><span class="icn_new"></span><?}?></td>
					<td><a href="<?=$userUrl?>" class="alink" target="_blank"><?=$rs['USER_NAME']?></a></td>
					<td><?=$rs['REMOTEIP']?></td>
					<td class="ag_l"><a href="<?=$itemUrl?>" class="alink" target="_blank"><?=$itemName?></a>/<a href="<?=$shopUrl?>" class="alink" target="_blank"><?=$shopName?></a></td>
					<td><?=$rs['SCORE']?>점</td>
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
		
		<div class="btn_list">
			<a href="javascript:grpReviewDel();" class="btn1">선택삭제</a>
			<a href="<?=$writeNewUrl?>" class="btn1">신규등록</a>
		</div>

		<!-- paging -->
		<div class="pagination cboth"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		