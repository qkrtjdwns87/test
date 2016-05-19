<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$itemName = ($recordSet) ? $recordSet[0]['ITEM_NAME'] : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">

	</script>
<!-- popup -->
<div id="popup">
	
	<div class="title mg_t10">
		<h3><span class="blue"><?=$itemName?></span> 승인 진행 내역</h3>
	</div>

	<table class="write2">
		<colgroup><col width="13%" /><col width="13%" /><col width="13%" /><col width="13%" /><col width="48%" /></colgroup>		
		<thead>
			<tr>
				<th>상태변경일시</th>
				<th>상태</th>
				<th>처리한<br />작성자</th>
				<th>Craft Shop</th>
				<th>사유/메시지</th>
			</tr>
		</thead>
		<tbody>
	    <?
	    	$i = 1;
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
	    	
				if ($rs['ITEMSTATECODE_NUM'] == '8040' || $rs['ITEMSTATECODE_NUM'] == '8050')
				{
					//승인거부
					$css = 'class="red"';
				}
				else if ($rs['ITEMSTATECODE_NUM'] == '8020')
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
				<td><span <?=$css?>><?=$rs['ITEMSTATECODE_TITLE']?></span></td>
				<td><?=$rs['ADMINUSER_NAME']?></td>
				<td><?=$rs['SHOP_NAME']?></td>
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