<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/manage/main_m/recommsearchwrite';
	$searchWordCnt = 10;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var searchIndex; //검색후 결과값 세팅될 index
		$(function() {
			
		});

		function sendRecommSearchMain(){
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();	
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post">
	<div id="content">

		<div class="title">
			<h2>[시즌추천검색어]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 시즌추천검색어</div>
		</div>

		<div class="sub_title">
			- Item 및 브랜드 검색 시 사용되는 추천 검색어관리입니다.
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="70%" /><col width="20%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th>추천검색어</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
			<?
				for($i=0; $i<$searchWordCnt; $i++)
				{
					$mmvNum = $searchOrder = 0;
					$searchWord = '';
					if (isset($recordSet) && isset($recommSearchSet))
					{
						$mmvNum = $recommSearchSet[$i]['NUM'];
						$searchOrder = $recommSearchSet[$i]['SEARCHWORD_ORDER'];
						$searchWord = $recommSearchSet[$i]['SEARCHWORD'];
					}
					
					$searchOrder = ($searchOrder == 0) ? $i : $searchOrder;
			?>			
				<tr>
					<td><?=$i+1?></td>
					<td class="ag_l"><input type="text" id="searchword_<?=$i?>" name="searchmn[<?=$i?>][word]" value="<?=htmlentities($searchWord)?>" class="inp_sty80" /></td>
					<td>
						<input type="text" id="searchorder_<?=$i?>" name="searchmn[<?=$i?>][order]" value="<?=$searchOrder?>" class="inp_sty40" />
						<input type="hidden" name="searchmn[<?=$i?>][num]" value="<?=$mmvNum?>" />
					</td>
				</tr>
			<?
				}
			?>				
			</tbody>
		</table>

		<div class="btn_list">
			<!-- <a href="" class="btn1">이전으로 되돌리기</a> -->
			<a href="javascript:sendRecommSearchMain();" class="btn2">저장</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>		