<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header_search.php"; ?>
	<script type="text/javascript">
		function memoSend(){
			if (trim($('#memo_content').val()) == ''){
				alert('내용을 입력하세요.');
				return;
			}

			document.mmfrm.target = 'hfrm';
			document.mmfrm.submit();
		}
	</script>
<!-- container -->
<div id="container">
	<!-- width 변경 금지 -->
	<div id="content" style="width:700px;">
		<form name="mmfrm" method="post" action="/manage/memo_m/write/t_no/<?=$tNum?>/t_info/<?=$tblInfo?>">
		<div>
			<label><input type="checkbox" id="priority_yn" name="priority_yn" value="Y" class="inp_check" />중요메모</label><br /><br />
			<textarea id="memo_content" name="memo_content" rows="5" cols="5" class="textarea1"></textarea>
		</div>
		</form>

		<div class="btn_list">
			<a href="javascript:memoSend();" class="btn3">등록</a>
		</div>

		<div class="reply">
			<ul>
		    <?
		    	$i = 1;
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
		    		$priorityTitle = ($rs['PRIORITY_YN'] == 'Y') ? '[중요!]' : '';
			?>				
				<li>
					<span class="name">- <?=$rs['USER_NAME']?> <span class="day"><?=$rs['CREATE_DATE']?></span></span>
					<span class="txt"><?=$priorityTitle?> <?=nl2br($rs['CONTENT'])?></span>
				</li>
			<?
					$i++;
				endforeach;
				
				if ($rsTotalCount == 0)
				{
			?>				
				<li>
					<span class="txt" style="text-align: center;vertical-align: middle;">등록된 내용이 없습니다.</span>
				</li>
			<?
				}
			?>				
			</ul>
		</div>

		<!-- paging -->
		<div class="pagination"><?=$pagination?></div>
		<!--// paging -->

	</div>
</div>
<!--// container -->
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer_search.php"; ?>
</body>
</html>