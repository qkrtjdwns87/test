<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/manage/item_m/list'.$addUrl;
	$writeNewUrl = '/manage/item_m/writeform'.$addUrl;
	$deleteUrl = '/manage/item_m/grpdelete';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<script src="/js/jquery.base64.min.js"></script>	
	<script src="/js/jquery.battatech.excelexport.js"></script>
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

		function grpItemChange(method, methodTitle){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
						
			if (confirm('선택한 아이템을 ' + methodTitle + ' 처리 하시겠습니까?')){
				var url = '/manage/item_m/change';
				url += '/return_url/' + $.base64.encode(location.pathname + location.search);				
				hfrm.location.href = url + '?selval='+sel+'&method='+method;	
			}
		}
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 Item 현황]</h2>
			<div class="location">Home &gt; Item 관리 &gt; 전체 Item 현황</div>
		</div>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>진열상태</th>
					<td>
						<label><input type="radio" id="viewyn" name="viewyn" value="" class="inp_radio" <?if (empty($viewYn)){?>checked="checked"<?}?> /><span>전체</span></label>
						<label><input type="radio" id="viewyn" name="viewyn" value="Y" class="inp_radio" <?if ($viewYn == 'Y'){?>checked="checked"<?}?> /><span>진열 중</span></label>
						<label><input type="radio" id="viewyn" name="viewyn" value="N" class="inp_radio" <?if ($viewYn == 'N'){?>checked="checked"<?}?> /><span>진열 안함</span></label>
					</td>
					<th>판매상태</th>
					<td>
						<label><input type="radio" id="itemstate1" name="itemstate" value="" class="inp_radio" <?if (empty($itemState)){?>checked="checked"<?}?> /><span>전체</span></label>
					<?
						$i = 2;
						foreach ($itemStCdSet as $crs):
							if ($crs['NUM'] >= 8060)
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
				<tr>
					<th>작가명</th>
					<td><input type="text" id="shopusername" name="shopusername" value="<?=$shopUserName?>" class="inp_sty90" /></td>
					<th>태그</th>
					<td><input type="text" id="itemtag" name="itemtag" value="<?=$itemTag?>" class="inp_sty90" /></td>
				</tr>
				<?}?>
				<tr>
					<th>승인일</th>
					<td colspan="3">
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
			<?if (!$isAdmin){?><a href="<?=$writeNewUrl?>" class="btn1">아이템 신규등록</a><?}?>
		</div>
		
		<div class="sub_title2 fl_l">총 <?=number_format($rsTotalCount)?>개</div> 

		<table class="write2">
			<colgroup><col width="3%" /><col width="5%" /><col width="9%" /><col width="31%" /><col width="11%" /><col width="7%" /><col width="10%" /><col width="10%" /><col width="7%" /><col width="7%" /></colgroup>
			<thead>
				<tr>
					<th><label class="mgn"><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /><span class="blind">선택</span></label></th>
					<th>No</th>
					<th>Item코드</th>
					<th>Item</th>
					<th>Craft Shop(코드)</th>
					<th>작가</th>
					<th>가격(원)</th>
					<th>승인일</th>
					<th>진열상태</th>
					<th>판매상태</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1; 
		    	$defaultImg = '/images/adm/@thumb.gif';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/item_m/updateform/sno/'.$rs['SHOP_NUM'].'/sino/'.$rs['NUM'].$addUrl;
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
						
							//log_message('ERROR', );
						}						
					}
					$fileName = (!empty($img)) ? $img : $defaultImg;

					if ($rs['ITEMSTATECODE_NUM'] == 8040 || $rs['ITEMSTATECODE_NUM'] == 8050)
					{
						//승인거부
						$css = 'class="red"';
					}
					else if ($rs['ITEMSTATECODE_NUM'] == 8020)
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
					<td><label class="mgn"><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/><span class="blind">선택</span></label></td>
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
					<td><?=subStr($rs['APPROVAL_DATE'], 0, 10)?></td>
					<td><?=$viewTitle?></td>
					<td><span <?=$css?>><?=$rs['ITEMSTATECODE_TITLE']?></span></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="10">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>				
			</tbody>
		</table>

		<table  style="display: none" class="write3" id="tblExport">
		<!-- <table class="write3" id="tblExport"> -->
			<colgroup><col width="3%" /><col width="5%" /><col width="9%" /><col width="31%" /><col width="11%" /><col width="7%" /><col width="10%" /><col width="10%" /><col width="7%" /><col width="7%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>Item코드</th>
					<!-- <th>Image</th> -->
					<th>Item</th>
					<th>Craft Shop(코드)</th>
					<th>작가</th>
					<th>가격(원)</th>
					<th>승인일</th>
					<th>진열상태</th>
					<th>판매상태</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1; 
		    	$defaultImg = 'http://api.circusflag.com/images/adm/@thumb.gif';
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = 'http://api.circusflag.com/manage/item_m/updateform/sno/'.$rs['SHOP_NUM'].'/sino/'.$rs['NUM'].$addUrl;
					$shopUrl = 'http://api.circusflag.com/manage/shop_m/view/sno/'.$rs['SHOP_NUM'];
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
						
							//log_message('ERROR', );
						}						
					}
					$fileName = (!empty($img)) ? $img : $defaultImg;

					if ($rs['ITEMSTATECODE_NUM'] == 8040 || $rs['ITEMSTATECODE_NUM'] == 8050)
					{
						//승인거부
						$css = 'class="red"';
					}
					else if ($rs['ITEMSTATECODE_NUM'] == 8020)
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
					<td><?=$no?></td>
					<td><?=$rs['ITEM_CODE']?></td>
					<!-- <td><img src="<?='http://api.circusflag.com'.$img?>" width="100" height="100" alt="" /></td> -->
					<td>
						<dl class="dl_img">
							<dt><img src="<?='http://api.circusflag.com'.$img?>" width="100" height="100" alt="" /></dt>
							<dd><?=$rs['ITEM_NAME']?></dd>
						</dl>
					</td>
					<!-- <td><a href="<?=$shopUrl?>" class="alink" target="_blank"><?=$rs['SHOP_NAME']?></a><div>(<?=$rs['SHOP_CODE']?>)</div></td> -->
					<td><?=$rs['SHOP_NAME']?><div>(<?=$rs['SHOP_CODE']?>)</div></td>
					<td><?=$rs['SHOPUSER_NAME']?></td>
					<td><?=number_format($rs['ITEM_PRICE'])?></td>
					<td><?=subStr($rs['APPROVAL_DATE'], 0, 10)?></td>
					<td><?=$viewTitle?></td>
					<td><span <?=$css?>><?=$rs['ITEMSTATECODE_TITLE']?></span></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="10">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>				
			</tbody>
		</table>

		<a id='btnExport' href="#" class="btn1 mg_t10" download="">엑셀다운로드</a>
		<!-- <button class="btn1 mg_t10" id='btnExport' type='button'>엑셀다운로드</button> -->
		<script type="text/javascript">
		    $(document).ready(function () {
		 
		        function itoStr($num)
		        {
		            $num < 10 ? $num = '0'+$num : $num;
		            return $num.toString();
		        }
		         
		        var btn = $('#btnExport');
		        var tbl = 'tblExport';
		        //var tbl = 'tblExport :not(checkbox)'; // 이친구를 Jquery Selector로 만져줘야됨 

		        btn.on('click', function () {
		            var dt = new Date();
		            var year =  itoStr( dt.getFullYear() );
		            var month = itoStr( dt.getMonth() + 1 );
		            var day =   itoStr( dt.getDate() );
		            var hour =  itoStr( dt.getHours() );
		            var mins =  itoStr( dt.getMinutes() );
		 
		            var postfix = year + month + day + "_" + hour + mins;
		            var fileName = "ItemList_"+ postfix + ".xls";
		 
		            var uri = $("#"+tbl).battatech_excelexport({
		                containerid: tbl
		                , datatype: 'table'
		                , returnUri: true
		            });
		 
		            $(this).attr('download', fileName).attr('href', uri).attr('target', '_blank');
		        });
		    });


	    </script>

		<div class="btn_list mg_t10">
			<?if (!$isAdmin){?><a href="<?=$writeNewUrl?>" class="btn1">아이템 신규등록</a><?}?>
			<span class="tdline fl_l">선택한 아이템을 </span>
			<span class="btn1 fl_l">
			<a href="javascript:grpItemChange('show', '진열');" class="btn1">진열중</a> 
			<a href="javascript:grpItemChange('hide', '진열안함');" class="btn1">진열안함</a> 
			<!-- <a href="javascript:grpItemChange('sell', '판매중');" class="btn1">판매 중</a> 승인과 동일-->
			<a href="javascript:grpItemChange('soldout', '품절');" class="btn1">품절</a>
			<a href="javascript:grpItemChange('runstop', '판매중지');" class="btn1">판매중지</a>
			<a href="javascript:grpItemChange('delete', '삭제');" class="btn3">삭제</a>
			</span>
			<span class="tdline fl_l">처리</span>
		</div>

		<!-- paging -->
		<div class="pagination"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>	
