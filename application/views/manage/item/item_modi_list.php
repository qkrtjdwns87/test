<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/item_m/modilist'.$addUrl;
	$pageTitle = '수정요청현황';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
		$(function() {
			$( "#sdate, #edate" ).datepicker({
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
				$("#sdate").datepicker("show");
			});
			$("#edateImg").click(function() { 
				$("#edate").datepicker("show");
			});			
		});

		function search(){
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function grpModiItemChange(method, methodTitle){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
						
			if (confirm('선택한 아이템을 ' + methodTitle + ' 처리 하시겠습니까?')){
				var url = '/manage/item_m/modichange';
				url += '/return_url/' + $.base64.encode(location.pathname + location.search);				
				hfrm.location.href = url + '?selval='+sel+'&method='+method;	
			}
		}		
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[<?=$pageTitle?>]</h2>
			<div class="location">Home &gt; Item 관리 &gt; <?=$pageTitle?></div>
		</div>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>Item 카테고리</th>
					<td colspan="3">
						<select id="itemcate" name="itemcate" class="inp_select">
							<option value="">카테고리</option>
							<?
								foreach ($mallCateSet as $crs):
									$sel_chk = ($crs['NUM'] == $itemCate) ? 'selected="selected"' : '';
							?>
							<option value="<?=$crs['NUM']?>" <?=$sel_chk?>><?=$crs['CATE_TITLE']?></option>
							<?
								endforeach;
							?>							
						</select>
					</td>
				</tr>
				<tr>
					<th>Item 명</th>
					<td><input type="text" id="itemname" name="itemname" value="<?=$itemName?>" class="inp_sty90" /></td>
					<th>Item 코드</th>
					<td><input type="text" id="itemcode" name="itemcode" value="<?=$itemCode?>" class="inp_sty90" /></td>
				</tr>
				<?if ($isAdmin){?>
				<tr>
					<th>Craft Shop 명</th>
					<td><input type="text" id="shopname" name="shopname" value="<?=$shopName?>" class="inp_sty90" /></td>
					<th>Craft Shop 코드</th>
					<td><input type="text" id="shopcode" name="shopcode" value="<?=$shopCode?>" class="inp_sty90" /></td>
				</tr>
				<?}?>
				<tr>
					<th>승인요청일</th>
					<td colspan="3">
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>
					</td>
				</tr>
				<tr>
					<th>승인상태</th>
					<td colspan="3">
						<label><input type="radio" id="itemstate" name="itemstate" value="" class="inp_radio" /><span>전체</span></label>
					<?
						$i = 2;
						foreach ($itemStCdSet as $crs):
							$isListUp = FALSE;
							if ($crs['NUM'] <= 7960) $isListUp = TRUE;
					
							if ($isListUp)
							{
								$sel_chk = ($crs['NUM'] == $itemState) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="itemstate<?=$i?>" name="itemstate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
								$i++;					
							}
						endforeach;					
					?>							
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>			
		</div>
		
		<div class="sub_title2">총 <?=number_format($rsTotalCount)?>개</div> 
		<table class="write2">
			<colgroup><col width="3%" /><col width="5%" /><col width="10%" /><col width="27%" /><col width="16%" /><col width="9%" /><col width="10%" /><col width="10%" /><col width="12%" /></colgroup>
			<thead>
				<tr>
					<th><label class="mgn"><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /><span class="blind">선택</span></label></th>
					<th>No</th>
					<th>Item코드</th>
					<th>Item</th>
					<th>Craft Shop(코드)</th>
					<th>작가</th>
					<th>가격(원)</th>
					<th>승인요청일</th>
					<th>승인상태</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	$url = '/manage/item_m/modiupdateform';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url .= '/sno/'.$rs['SHOP_NUM'].'/sino/'.$rs['NUM'].$addUrl;
					$shopUrl = '/manage/shop_m/view/sno/'.$rs['SHOP_NUM'];
					$viewTitle = ($rs['VIEW_YN'] == 'Y') ? '진열중' : '진열안함';

					$img = '';	
					if ($rs['FILE_INFO'])
					{
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
					}
					$fileName = (!empty($img)) ? $img : $defaultImg;

					if ($rs['ITEMSTATECODE_NUM'] == 7940 || $rs['ITEMSTATECODE_NUM'] == 7950)
					{
						//승인거부
						$css = 'class="red"';
					}
					else if ($rs['ITEMSTATECODE_NUM'] == 7920)
					{
						//승인요청
						$css = 'class="blue"';
					}
					else
					{
						$css = '';
					}					
			?>				
				<tr>
					<td><label class="mgn"><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>|<?=$rs['ORIGINAL_ITEM_NUM']?>" class="inp_check"/><span class="blind">선택</span></label></td>
					<td><?=$no?></td>
					<td><?=$rs['ITEM_CODE']?></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=CDN.$fileName?>" width="100" height="100" alt="" /></dt>
							<dd><a class="link1" href="<?=$url?>"><?=$rs['ITEM_NAME']?></a></dd>
						</dl>
					</td>
					<td><a href="<?=$shopUrl?>" class="alink" target="_blank"><?=$rs['SHOP_NAME']?></a><div>(<?=$rs['SHOP_CODE']?>)</div></td>
					<td><?=$rs['SHOPUSER_NAME']?></td>
					<td><?=number_format($rs['ITEM_PRICE'])?></td>
					<td><?=subStr($rs['APPROVAL_REQ_DATE'], 0, 10)?></td>
					<td><span <?=$css?>><?=$rs['ITEMSTATECODE_TITLE']?></span></td>
				</tr>				
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="9">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>	
			</tbody>
		</table>

		<a href="" class="btn1 mg_t10">엑셀다운로드</a>
		<a href="javascript:grpModiItemChange('1000', '삭제');" class="btn3 mg_t10">삭제</a>

		<?if ($isAdmin){?>
		<div class="btn_list mg_t10">
			<span class="tdline fl_l">선택한 수정 아이템을 </span>
			<span class="btn1 fl_l">
			<a href="javascript:grpModiItemChange('7920', '수정승인요청');" class="btn1">수정승인요청</a> 
			<a href="javascript:grpModiItemChange('7930', '수정승인대기');" class="btn1">수정승인대기</a> 
			<a href="javascript:grpModiItemChange('7940', '수정승인보류');" class="btn1">수정승인보류</a>
			<a href="javascript:grpModiItemChange('7950', '수정승인거부');" class="btn1">수정승인거부</a>
			<a href="javascript:grpModiItemChange('7960', '수정승인');" class="btn1">수정승인</a>
			</span>
			<span class="tdline fl_l">처리</span>
		</div>
		<?}?>
		
		<!-- paging -->
		<div class="pagination"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		