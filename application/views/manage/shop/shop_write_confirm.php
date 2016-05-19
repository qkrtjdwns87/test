<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$listUrl = '/manage/shop_m/apprlist';
	$listUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$listUrl .= (!empty($currentParam)) ? '?cs=shop'.$currentParam : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<script src="/js/jquery.browser.js"></script>	
	<script src="/js/jquery.iframe-auto-height.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
      
	    });	

		function sendShop(){
			if (trim($('#shop_history_content').val()) == ''){
				alert('승인요청 메시지를 입력하세요.');
				return;
			}
			
			document.form.target = 'hfrm';
			document.form.action = "/manage/shop_m/requestappr/sno/<?=$sNum?>";
			document.form.submit();
		}

		function shopInfoModify(sno){
			if (confirm('수정하시겠습니까?')){
				location.href = '/manage/shop_m/updateform/sno/'+sno;				
			}
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post">
	<div id="content">

		<div class="title">
			<h2>[신규신청]</h2>
			<div class="location">Home &gt; Craft Shop 관리 &gt; 신규신청</div>
		</div>
		
		<div class="sub_title">신규신청이 완료되어 ‘승인대기’ 상태로 전환되면 수정 및 추가를 하실 수 없습니다. <br />정확하게 입력되었는지 다시 한번 내용을 확인해 후 ‘승인요청＇을 진행해 주십시오.</div>
		<table class="write1">
			<colgroup><col width="12%" /><col /></colgroup>
			<thead>
				<tr>
					<th colspan="2">Craft Shop 기본정보</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>판매자 구분</th>
					<td><?=$baseSet['SELLERTYPECODE_TITLE']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 명</th>
					<td><?=$baseSet['SHOP_NAME']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가</th>
					<td><?=$baseSet['SHOPUSER_NAME']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>계정신청 (이메일)</th>
					<td><?=$baseSet['USER_EMAIL_DEC']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 이메일</th>
					<td><?=$baseSet['SHOP_EMAIL_DEC']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>작가 휴대폰 번호</th>
					<td><?=$baseSet['SHOP_MOBILE_DEC']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>Shop 대표 번호</th>
					<td><?=$baseSet['SHOP_TEL_DEC']?></td>
				</tr>				
				<tr>
					<th>CIRCUS 관리 담당자</th>
					<td>이름:<?=$baseSet['MANAGERUSER_NAME']?> / 전화번호:<?=$baseSet['MANAGER_TEL_DEC']?> / 휴대폰 번호:<?=$baseSet['MANAGER_MOBILE_DEC']?></td>
				</tr>
			</tbody>
		</table>

		<table class="write1 mg_t10">
			<colgroup><col width="12%" /><col width="38%" /><col width="12%" /><col width="38%" /></colgroup>
			<thead>
				<tr>
					<th colspan="3">사업자 정보</th>
					<th class="ag_r">
						<!-- <label><input type="checkbox" id="" name="" class="inp_check" /><span>Craft Shop & 작가 정보와 동일</span></label> -->
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><span class="important">*</span>사업자 번호</th>
					<td colspan="3"><?=$infoSet['CO_NUM']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자 형태</th>
					<td colspan="3"><?=$baseSet['SELLERTYPECODE_TITLE']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>사업자명(상호)</th>
					<td><?=$infoSet['CO_NAME']?></td>
					<th><span class="important">*</span>대표자명</th>
					<td><?=$infoSet['CO_CEONAME']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>업태</th>
					<td><?=$infoSet['CO_BIZTYPE']?></td>
					<th><span class="important">*</span>종목</th>
					<td><?=$infoSet['CO_BIZEVENT']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 이메일</th>
					<td colspan="3"><?=$infoSet['CO_CEOEMAIL_DEC']?></td>
				</tr>
				<tr>
					<th><span class="important">*</span>대표 전화</th>
					<td colspan="3"><?=$infoSet['CO_TEL_DEC']?></td>
				</tr>
				<tr>
					<th rowspan="3"><span class="important">*</span>사업장 소재지</th>
					<td colspan="3"><?=$infoSet['CO_ZIP_DEC']?></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><?=$infoSet['CO_ADDR1_DEC']?> <?=$infoSet['CO_ADDR2_DEC']?></td>
				</tr>
				<tr>
					<td colspan="3" class="bo_tn pd_tn"><?=$infoSet['CO_ADDR_JIBUN_DEC']?></td>
				</tr>
				<tr>
					<th>통신판매업 번호</th>
					<td colspan="3"><?=$infoSet['CO_MAILORDER_NO']?></td>
				</tr>
			</tbody>
		</table>
		<div class="btn_list">
			<a href="javascript:shopInfoModify('<?=$sNum?>');" class="btn1">입력정보수정</a>
		</div>

		<div class="shop_comment">
			<h3>[신규 Craft Shop 승인요청]</h3>
			<p>필요한 정보와 서류는 모두 입력 및 전달하셨나요? <br />모두 처리가 완료되었다면 승인요청 메시지와 함께 승인요청을 진행해 주십시오.</p>
			<p><textarea id="shop_history_content" name="shop_history_content" rows="5" cols="5" class="textarea1"></textarea></p>
			
			<div class="btn_list">
				<a href="javascript:sendShop();" class="btn1">승인요청</a>
			</div>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		