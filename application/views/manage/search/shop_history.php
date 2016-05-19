<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$shopName = ($recordSet) ? $recordSet[0]['SHOP_NAME'] : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">

	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title">
		<h3>[<?=$shopName?> Craft Shop 승인 진행 내역]</h3>
	</div>

	<table class="write2">
		<colgroup><col width="13%" /><col width="13%" /><col width="13%" /><col width="13%" /><col width="48%" /></colgroup>
		<thead>
			<tr>
				<th>상태 <br />변경일자</th>
				<th>상태</th>
				<th>내역 <br />처리자</th> <!-- <th>CIRCUS <br />승인담당자</th> -->
				<th>CIRCUS <br />관리담당자</th>
				<th>사유 /메시지</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = 1;
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
	    	
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
		?>			
			<tr>
				<td><?=$rs['CREATE_DATE']?></td>
				<td><span <?=$css?>><?=$rs['SHOPSTATECODE_TITLE']?></span></td>
				<td><?=$rs['USER_NAME']?></td>
				<td><?=$rs['MANAGERUSER_NAME']?></td>
				<td><?=$rs['CONTENT']?></td>
			</tr>
		<?
				$i++;
			endforeach;
			
			if ($rsTotalCount == 0)
			{
		?>
			<tr>
				<td colspan="5">등록된 내용이 없습니다.</td>
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