<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>	
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
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[전체 Craft Shop 현황]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 전체 Craft Shop 현황</div>
		</div>
		
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>Shop 상태</th>
					<td colspan="3">
						<label><input type="radio" id="shopstate1" name="shopstate" value="" class="inp_radio" <?if ($shopState==''){?>checked="checked"<?}?> /><span>전체</span></label>
					<?
						$i = 2;
						foreach ($spStatCdSet as $crs):
							if ($crs['NUM'] >= 3060)
							{
								$sel_chk = ($crs['NUM'] == $shopState) ? 'checked="checked"' : '';								
					?>
						<label><input type="radio" id="shopstate<?=$i?>" name="shopstate" value="<?=$crs['NUM']?>" <?=$sel_chk?> class="inp_radio" /><span><?=$crs['TITLE']?></span></label>
					<?
							}
							$i++;
						endforeach;					
					?>						
					</td>
				</tr>
				<?if ($isAdmin){?>
				<tr>
					<th>Craft Shop명</th>
					<td><input type="text" id="shopname" name="shopname" value="<?=$shopName?>" class="inp_sty90" /></td>
					<th>Craft Shop코드</th>
					<td><input type="text" id="shopcode" name="shopcode" value="<?=$shopCode?>" class="inp_sty90" placeholder="코드 10자리 입력" maxlength="8" /></td>
				</tr>
				<tr>
					<th>계정(이메일)</th>
					<td colspan="3"><input type="text" id="useremail" name="useremail" value="<?=$shopEmail?>" class="inp_sty40" /> <span class="ex">* 예시) abc@abc.co.kr</span></td>
				</tr>
				<tr>
					<th>작가명</th>
					<td colspan="3"><input type="text" id="shopusername" name="shopusername" value="<?=$shopUserName?>" class="inp_sty40" /></td>
				</tr>
				<?}?>
				<tr>
					<th>승인일</th>
					<td colspan="3">
						<input type="text" id="sdate" name="sdate" value="<?=$sDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="sdateImg" class="calendar"></a><span class="to">~</span><input type="text" id="edate" name="edate" value="<?=$eDate?>" class="inp_sty10" readonly /><a href="javascript:void(0);" id="edateImg" class="calendar"></a>
						<a href="javascript:dateCal(0,'sdate','edate');" class="btn2 on">오늘</a><a href="javascript:dateCal(1,'sdate','edate');" class="btn2">1개월</a><a href="javascript:dateCal(6,'sdate','edate');" class="btn2">6개월</a><a href="javascript:dateCal(12,'sdate','edate');" class="btn2">1년</a>
					</td>
				</tr>
				<?if ($isAdmin){?>
				<tr>
					<th>배지</th>
					<td colspan="3">
						<label><input type="radio" id="authortype1" name="authortype" value="" <?if (empty($authortype)){?>checked="checked"<?}?> class="inp_radio" /><span>전체</span></label>
						<label><input type="radio" id="authortype2" name="authortype" value="today" <?if ($authortype=='"today"'){?>checked="checked"<?}?> class="inp_radio" /><span>오늘의 작가</span></label>
						<label><input type="radio" id="authortype3" name="authortype" value="pop" <?if ($authortype=='pop'){?>checked="checked"<?}?> class="inp_radio" /><span>인기작가</span></label>
					</td>
				</tr>
				<?}?>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>
	
		<div class="sub_title">총 <?=number_format($rsTotalCount)?>개</div>
		<table class="write2">
			<colgroup><col width="5%" /><col width="8%" /><col /><col width="8%" /><col width="10%" /><col width="8%" /><col width="6%" /><col width="8%" /><col width="6%" /><col width="7%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>No</th>
					<th>Shop 코드</th>
					<th>Shop명</th>
					<th>작가</th>
					<th>계정(이메일)</th>
					<th>대표연락처</th>
					<th>item수</th>
					<th>승인일</th>
					<th>배지</th>
					<th>상태</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
					$url = '/manage/shop_m/view/sno/'.$rs['NUM'].$addUrl;
					
					$authorStat = '';
					if ($rs['TODAYAUTHOR_YN'] == 'Y') $authorStat = '오늘의 작가';
					if ($rs['POPAUTHOR_YN'] == 'Y') $authorStat .= ' 인기 작가';
					
					if ($rs['SHOPSTATECODE_NUM'] == '3040' || $rs['SHOPSTATECODE_NUM'] == '3050')
					{
						//승인거부
						$css = 'class="red"';
					}
					else if ($rs['SHOPSTATECODE_NUM'] == '3020')
					{
						//승인요청
						$css = 'class="blue"';
					}
					else
					{
						$css = '';
					}
					$shopName = $rs['SHOP_NAME'];
					$shopNameJs = addslashes(htmlspecialchars($rs['SHOP_NAME']));					
			?>			
				<tr>
					<td><?=$no?></td>
					<td><a href="<?=$url?>" class="alink"><?=$rs['SHOP_CODE']?></a></td>
					<td><a href="<?=$url?>" class="alink"><?=$shopName?></a></td>
					<td><?=$rs['SHOPUSER_NAME']?></td>
					<td><?=$rs['SHOP_EMAIL_DEC']?></td>
					<td><?=$rs['SHOP_TEL_DEC']?></td>
					<td><?=number_format($rs['TOTITEM_COUNT'])?></td>
					<td><?=subStr($rs['APPROVAL_DATE'], 0, 10)?></td>
					<td><?=$authorStat?></td>
					<td><span <?=$css?>><?=$rs['SHOPSTATECODE_TITLE']?></span></td>
					<td><a href="javascript:messageSend('<?=$shopNameJs?>', '<?=$rs['NUM']?>', 'shop');" class="btn2">메시지</a><a href="javascript:smsSend('<?=$shopNameJs?>', '<?=$rs['SHOP_MOBILE_DEC']?>');" class="btn2">SMS</a></td>
				</tr>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>
				<tr>
					<td colspan="11">검색된 결과가 없습니다. <br />검색조건을 바꾸어 검색해 주시기 바랍니다</td>
				</tr>
			<?
				}
			?>
			</tbody>
		</table>

		<a href="#" class="btn1 mg_t10">엑셀다운로드</a>

		<!-- paging -->
		<div class="pagination"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>