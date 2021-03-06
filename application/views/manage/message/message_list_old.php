<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$pageTitle = '전체';	
	$viewUrl = '/manage/message_m/view';
	$writeNewUrl = '/manage/message_m/writeform';
	$deleteUrl = '/manage/message_m/grpdelete';
	//써커스입장에서 새글작성은 일반대화	
	if ($pageMethod == 'listuser')
	{
		$pageTitle = '회원';
		$viewUrl = '/manage/message_m/viewuser';		
		$writeNewUrl = '/manage/message_m/writeformuser/msgtype/17150';
		$deleteUrl = '/manage/message_m/grpdeleteuser';
	}
	else if ($pageMethod == 'listshop')
	{
		$pageTitle = 'Craft Shop';		
		$viewUrl = '/manage/message_m/viewshop';
		$writeNewUrl = '/manage/message_m/writeformshop/msgtype/17140';
		$deleteUrl = '/manage/message_m/grpdeleteshop';
	}
	else if ($pageMethod == 'listmall')
	{
		$pageTitle = '전체';		
		$viewUrl = '/manage/message_m/viewmall';
		$writeNewUrl = '/manage/message_m/writeformmall/msgtype/17150';
		$deleteUrl = '/manage/message_m/grpdeletemall';
	}
	
	//$writeNewUrl .= $addUrl;	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script type="text/javascript">
		$(function() {
			
		});

		function search(){
			document.srcfrm.submit();
		}

		function searchReset(){
			location.href = '<?=$currentUrl?>';
		}

		function grpMessageDel(){
			var sel = getCheckboxSelectedValue('chkCheck');
			if (sel == ''){
				alert('선택된 내용이 없습니다.');
				return;
			}
			
			if (confirm('삭제하시겠습니까?')){
				hfrm.location.href = '<?=$deleteUrl?>?selval='+sel;
			}			
		}

		function msgUserSearch(){
			/*
			var arrUser = $('#senduserno').val().split(',');
			if (arrUser != '' && arrUser.length >= 1){
				alert('검색은 1건씩만 가능합니다.');
				return;
			}
			*/			
			userSearch();
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
		
		function userResultSet(uno, uname){
			var targetNo=$('#senduserno').val();
			var targetTxt=$('#sendusertxt').val();
			//if (targetNo == ''){
				$('#senduserno').val(uno);
				$('#sendusertxt').val(uname);
			//}else{
			//	$('#senduserno').val(targetNo+','+uno);
			//	$('#sendusertxt').val(targetTxt+','+uname);
			//}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}	

		function shopResultSet(shopno, shopname, shopcode){
			var shop=shopname+'('+shopcode+')';
			var targetNo=$('#sendshopno').val();
			var targetTxt=$('#sendshoptxt').val();
			//if (targetNo == ''){
				$('#sendshopno').val(shopno);
				$('#sendshoptxt').val(shop);
			//}else{
			//	$('#sendshopno').val(targetNo+','+shopno);
			//	$('#sendshoptxt').val(targetTxt+','+shop);
			//}
			$('#layer_pop').hide();	
			$('#popfrm').attr('src', '');
		}	

		function sendNewMessage(){
			location.href='<?=$writeNewUrl?>';
		}		
	</script>
<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[<?=$pageTitle?>메시지현황]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; <?=$pageTitle?>메시지현황</div>
		</div>
		<form name="srcfrm" method="post" action="<?=$currentUrl?>">
		<table class="write1">
			<colgroup><col width="15%" /><col width="35%" /><col width="15%" /><col width="35%" /></colgroup>
			<tbody>
				<tr>
					<th>확인여부</th>
					<td>
						<label><input type="radio" id="read_yn1" name="read_yn" value="" <?if (empty($readYn)){?>checked="checked"<?}?> class="inp_radio" />전체</label>
						<label><input type="radio" id="read_yn2" name="read_yn" value="Y" <?if ($readYn == 'Y'){?>checked="checked"<?}?> class="inp_radio" />확인</label>
						<label><input type="radio" id="read_yn3" name="read_yn" value="N" <?if ($readYn == 'N'){?>checked="checked"<?}?> class="inp_radio" />미확인</label>
					</td>
					<th>검색어</th>
					<td>
						<input type="text" id="sword" name="sword" class="inp_sty60" value="" placeholder="내용에서 검색"/>
						<input type="hidden" name="skey" value="content"/>
					</td>
				</tr>
				<tr>
					<th>Craft Shop</th>
					<td>
						<input type="text" id="sendshoptxt" name="sendshoptxt" value="<?=$sendShopTxt?>" class="inp_sty60" readonly/>
						<input type="hidden" id="sendshopno" name="sendshopno" value="<?=$sendShopNum?>"/>
						<a href="javascript:msgShopSearch();" class="btn1">찾아보기</a>
					</td>
					<th>회원</th>
					<td>
						<input type="text" id="sendusertxt" name="sendusertxt" value="<?=$sendUserTxt?>" class="inp_sty60" readonly/>
						<input type="hidden" id="senduserno" name="senduserno" value="<?=$sendUserNum?>"/>					
						<a href="javascript:msgUserSearch();" class="btn1">찾아보기</a>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
		<div class="btn_list">
			<a href="javascript:sendNewMessage();" class="btn1">메세지보내기</a>
			<a href="javascript:searchReset();" class="btn1">초기화</a>
			<a href="javascript:search();" class="btn2">검색</a>
		</div>

		<div class="sub_title">총 <?=number_format($rsTotalCount)?>개</div>

		<table class="write2" class="cboth">
			<colgroup><col width="3%" /><col width="5%" /><col width="8%" /><col width="18%" /><col width="18%" /><col /><col width="6%" /><col width="8%" /></colgroup>
			<thead>
				<tr>
					<th><input type="checkbox" id="checkall" onclick="javascript:AllCheckBoxCheck('chkCheck',this.id);" class="inp_check" /></th>
					<th>No</th>
					<th>발송일시</th>
					<th>송신</th>
					<th>수신</th>
					<th>내용</th>
					<th>상세</th>
					<th>수신자<br />확인여부</th>
				</tr>
			</thead>
			<tbody>
		    <?
		    	$i = 1;
		    	$listUrl = '/manage/message_m/listview'.str_replace('list', '', $pageMethod);
		    	foreach ($recordSet as $rs):
					$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
		    		$createDate = $rs['CREATE_DATE'];
					$url = $viewUrl.'/msgno/'.$rs['NUM'];
					$url .= '/msggrpno/'.$rs['MESSAGE_GROUPNUM'].$addUrl;
					$listViewUrl = $listUrl.'/msgno/'.$rs['NUM']; 
					$listViewUrl .= '/msggrpno/'.$rs['MESSAGE_GROUPNUM'];
					$listViewUrl .= '/msgtype/'.$rs['MSGTYPECODE_NUM'];
					$listViewUrl .= '/msgtodate/'.substr($createDate, 0, 10);
					if ($rs['READ_YN'] == 'N')
					{
						$css = 'red';
						$readTitle = "미확인";
					}
					else
					{
						$css = '';
						$readTitle = "확인";
					}
					$css = ($rs['READ_YN'] == 'N') ? 'red' : '';
					$content = preg_replace('/\r\n|\r|\n/','',$rs['CONTENT']);
					$content = $this->common->cutStr($content, 40, '...');
					$sendName = $rs['SEND_USER_NAME'].'<br />('.$rs['SEND_USER_EMAIL_DEC'].')';
					$toName = $rs['TO_USER_NAME'].'<br />('.$rs['TO_USER_EMAIL_DEC'].')';
					$sendUrl = '/manage/user_m/updateform/uno/'.$rs['USER_NUM'];
					$toUrl = '/manage/user_m/updateform/uno/'.$rs['TOUSER_NUM'];
					if ($rs['SENDER_TYPE'] == 'S' && !empty($rs['SEND_SHOP_NAME'])) //shop자격으로 발송
					{
						$sendUrl = '/manage/shop_m/view/sno/'.$rs['SEND_SHOP_NUM'];
						$sendName = $rs['SEND_SHOP_NAME'].'<br />('.$rs['SEND_SHOP_CODE'].')';
					}
					if ($rs['TARGET_TYPE'] == 'S' && !empty($rs['TO_SHOP_NAME'])) //shop자격으로 수신
					{
						$toUrl = '/manage/shop_m/view/sno/'.$rs['TO_SHOP_NUM'];
						$toName = $rs['TO_SHOP_NAME'].'<br />('.$rs['TO_SHOP_CODE'].')';
					}					
			?>					
				<tr>
					<td><input type="checkbox" id="chkCheck<?=$rs['NUM']?>" name="chkCheck" value="<?=$rs['NUM']?>" class="inp_check"/></td>
					<td><?=$no?></td>
					<td><?=$createDate?></td>
					<td><a href="<?=$sendUrl?>" class="alink" target="_blank"><?=$sendName?></a></td>
					<td><a href="<?=$toUrl?>" class="alink" target="_blank"><?=$toName?></a></td>
					<td class="ag_l"><a href="<?=$listViewUrl?>" class="alink"><?=$content?></a></td>
					<td><a href="<?=$url?>" class="btn1">보기</a></td>
					<td><span class="bold <?=$css?>"><?=$readTitle?></span></td>
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
			<a href="" class="btn1 fl_l">엑셀다운로드</a>
			<a href="javascript:grpMessageDel();" class="btn1 fl_r">선택삭제</a>
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