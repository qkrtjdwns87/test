<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$submitUrl = '/manage/main_m/visualwrite';
	$visualCnt = 5;

	$linkType_arr = array("STORY", "SPECIAL", "ITEM", "SHOP");

?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var searchIndex; //검색후 결과값 세팅될 index
		$(function() {
			
		});

		function sendVisualMain(){
			if (trim($('#userfile0').val()) == '' && trim($('#userHfile0').val()) == ''){
				alert('첫번째 웹용 이미지는 꼭 등록하셔야 합니다.');
				return;
			}	

			if (trim($('#userfile1').val()) == '' && trim($('#userHfile1').val()) == ''){
				alert('첫번째 모바일앱용 이미지는 꼭 등록하셔야 합니다.');
				return;
			}			
			
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();	
		}

		function deleteContent(type, no, order){
			var url = '/manage/main_m/visualcontentdelete/mmno/<?=$mmNum?>';
			url += '/return_url/' + $.base64.encode(location.pathname + location.search);
			url += '?type='+type+'&no='+no+'&cnorder='+order;
			
			if (type=='file'){
				$('#userHfile'+order).val('');
			}
			
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = url;
			} 
		}
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<div id="content">

		<div class="title">
			<h2>[메인비주얼]</h2>
			<div class="location">Home &gt; 시스템관리 &gt; 메인 관리 &gt; 메인비주얼</div>
		</div>
		
		<table class="write2">
			<colgroup><col width="10%" /><col width="10%" /><col width="70%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th>노출순서</th>
					<th colspan="2">메인 비주얼 이미지</th>
					<th>순서변경</th>
				</tr>
			</thead>
			<tbody>
			<?
				$defaultImg = '/images/adm/@thumb.gif';
				for($i=0; $i<$visualCnt; $i++)
				{
					$fi = $i * 2;
					$mmvNum = $vsOrder = 0;
					$link = '';
					$blankYn = 'N';
					$fileNum = $mobFileNum = '';
					$fileName = $mobFileName = $imgUrl = $mobImgUrl = '';
					if (isset($recordSet) && isset($recVisualSet))
					{
						$mmvNum = $recVisualSet[$i]['NUM'];
						$vsOrder = $recVisualSet[$i]['VISUAL_ORDER'];
						$link = $recVisualSet[$i]['VISUAL_LINK'];
						$blankYn = $recVisualSet[$i]['BLANK_YN'];
						if (!empty($fileSet[$fi]['FILE_NAME']))
						{
							$fileNum = $fileSet[$fi]['NUM'];
							$fileName = $fileSet[$fi]['FILE_NAME'];
								
							if ($fileSet[$fi]['THUMB_YN'] == 'Y')
							{
								$imgUrl = str_replace('.', '_s.', $fileSet[$fi]['FILE_PATH'].$fileSet[$fi]['FILE_TEMPNAME']);
							}
							else
							{
								$imgUrl = $fileSet[$fi]['FILE_PATH'].$fileSet[$fi]['FILE_TEMPNAME'];
							}
						}
						
						if (!empty($fileSet[$fi+1]['FILE_NAME']))
						{
							$mobFileNum = $fileSet[$fi+1]['NUM'];
							$mobFileName = $fileSet[$fi+1]['FILE_NAME'];
						
							if ($fileSet[$fi+1]['THUMB_YN'] == 'Y')
							{
								$mobImgUrl = str_replace('.', '_s.', $fileSet[$fi+1]['FILE_PATH'].$fileSet[$fi+1]['FILE_TEMPNAME']);
							}
							else
							{
								$mobImgUrl = $fileSet[$fi+1]['FILE_PATH'].$fileSet[$fi+1]['FILE_TEMPNAME'];
							}
						}						
					}
					
					$vsOrder = ($vsOrder == 0) ? $i : $vsOrder;
					$imgUrl = (!empty($imgUrl)) ? $imgUrl : $defaultImg;
					$mobImgUrl = (!empty($mobImgUrl)) ? $mobImgUrl : $defaultImg;
			?>
				<tr>
					<td rowspan="5"><?=$i+1?></td>
					<td>PC 웹용<br /><span class="red">(000*000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=$imgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$fileNum?>" class="alink"><?=$fileName?></a>
								<?if (!empty($fileName) && $fi > 1){?><a href="javascript:deleteContent('file', '<?=$fileNum?>', '<?=$fi?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>								
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi?>" name="userfile<?=$fi?>" class="inp_file" value="" />
								<input type="hidden" id="userHfile<?=$fi?>" name="userHfile<?=$fi?>" value="<?=$fileName?>" />
							</dd>
						</dl>
					</td>
					<td rowspan="5">
						<input type="text" id="vsorder_<?=$i?>" name="visualmn[<?=$i?>][order]" value="<?=$vsOrder?>" class="inp_sty60" />
						<input type="hidden" name="visualmn[<?=$i?>][num]" value="<?=$mmvNum?>" />						
					</td>
				</tr>
				<tr>
					<td>모바일용<br /><span class="red">(000*000)</span></td>
					<td>
						<dl class="dl_img">
							<dt><img src="<?=$mobImgUrl?>" width="100" height="100" alt="" /></dt>
							<dd>
								<a href="/download/route/fno/<?=$mobFileNum?>" class="alink"><?=$mobFileName?></a>
								<?if (!empty($mobFileName) && $fi > 1){?><a href="javascript:deleteContent('file', '<?=$mobFileNum?>', '<?=$fi+1?>');" class="close"><img src="/images/adm/btn_close.gif" alt="닫기" /></a><?}?>
							</dd>
							<dd>
								<input type="file" id="userfile<?=$fi+1?>" name="userfile<?=$fi+1?>" class="inp_file" value="" />
								<input type="hidden" id="userHfile<?=$fi+1?>" name="userHfile<?=$fi+1?>" value="<?=$mobFileName?>" />
							</dd>
						</dl>					
					</td>
				</tr>
				<tr>
					<td>링크</td>
					<td class="ag_l">
						<input type="text" id="vslink_<?=$i?>" name="visualmn[<?=$i?>][link]" value="<?=$link?>" class="inp_sty60" />
						<label><input type="checkbox" id="vsblankyn_<?=$i?>" name="visualmn[<?=$i?>][blankyn]" value="Y" <?if ($blankYn == 'Y'){?>checked="checked"<?}?> class="inp_check" />새창으로</label>
					</td>
				</tr>
				<tr>
					<!-- 20160511 yong mod -->
					<td>iphone data</td>
					<td class="ag_l">
						<select>
							<option value="default">--Link Type--</option>
							<? for ($link_index = 0 ; $link_index < count($linkType_arr) ; $link_index++) { ?>

								<<option value="<?=$linkType_arr[$link_index]?>"><?=$linkType_arr[$link_index]?></option>

							<? } ?>
						</select>
						<input type="text" id="" name="" value="" class="inp_sty60" />
					</td>
				</tr>
				<tr>
					<!-- 20160511 yong mod -->
					<td>android data</td>
					<td class="ag_l">
						<select>
							<option value="default">--Link Type--</option>
							<? for ($link_index = 0 ; $link_index < count($linkType_arr) ; $link_index++) { ?>

								<<option value="<?=$linkType_arr[$link_index]?>"><?=$linkType_arr[$link_index]?></option>

							<? } ?>
						</select>
						<input type="text" id="" name="" value="" class="inp_sty60" />
					</td>
				</tr>
			<?
				}
			?>
			</tbody>
		</table>

		<div class="btn_list">
			<!-- <a href="" class="btn1">이전으로 되돌리기</a> -->
			<a href="javascript:sendVisualMain();" class="btn2">저장</a>
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			