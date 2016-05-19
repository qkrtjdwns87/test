<?
	defined('BASEPATH') OR exit('No direct script access allowed');

	$msgViewUserNum = $sessionData['user_num']; //현재 대화를 보고 있는 당사자
	//$msgData
	//$msgType = $msgData['MSGTYPECODE_NUM'];
	//$msgDepth = $msgData['MESSAGE_DEPTH'];
	$sendUserNum = $msgData['USER_NUM'];
	$sendUserName = $msgData['SEND_USER_NAME'].' ('.$msgData['SEND_USER_EMAIL_DEC'].')';
	$toUserNum = $msgData['TOUSER_NUM'];
	$toName = $msgData['TO_USER_NAME'].' ('.$msgData['TO_USER_EMAIL_DEC'].')';
	$targetNum = ($msgViewUserNum == $sendUserNum) ? $toUserNum : $sendUserNum; //현재 대화를 보고 있는 당사자 입장에서는 sender에게 메시지 발송
	if ($msgData['SENDER_TYPE'] == 'S' && !empty($msgData['SHOP_NAME'])) //shop자격으로 송신
	{
		$sendUserName = $msgData['SEND_SHOP_NAME'].' ('.$msgData['SEND_SHOP_CODE'].')';
		$targetNum = $msgData['SEND_SHOP_NUM'];
	}	
	
	if ($msgData['TARGET_TYPE'] == 'S' && !empty($msgData['TO_SHOP_NAME'])) //shop자격으로 수신
	{
		$toName = $msgData['TO_SHOP_NAME'].' ('.$msgData['TO_SHOP_CODE'].')';
	}	
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';

	$addUri = '/msgno/'.$msgNum.'/msggrpno/'.$msgGrpNum.'/msgdepth/'.$msgDepth;
	//$addUri .= '/msgtype/'.$msgType.'/msgtodate/'.$msgToDate;
	
	$pageTitle = '회원과의';
	$listUrl = '/manage/message_m/list';
	$submitUrl = '/manage/message_m/writeview';	
	if ($pageMethod == 'listviewuser')
	{
		$pageTitle = 'Circus,회원과의';		
		$listUrl = '/manage/message_m/listuser';
		$submitUrl = '/manage/message_m/writeviewuser';
	}
	else if ($pageMethod == 'listviewusershop')
	{
		$pageTitle = 'Craft Shop,회원과의';
		$listUrl = '/manage/message_m/listusershop';
		$submitUrl = '/manage/message_m/writeviewusershop';
	}	
	else if ($pageMethod == 'listviewshop')
	{
		$pageTitle = 'Circus,Craft Shop과의';
		$listUrl = '/manage/message_m/listshop';
		$submitUrl = '/manage/message_m/writeviewshop';
	}
		
	
	if(($rsTotalCount % $listCount) == 0)
	{
		$totPage = intval($rsTotalCount / $listCount);
	}
	else
	{
		$totPage = intval($rsTotalCount / $listCount) + 1;
	}	
	
	$submitUrl .= $addUri.$addUrl;
	$listUrl .= $addUrl;
	$prevUrl = $currentUrl;	//.'/msgtodate/'.$msgPrevDate.$addUrl;
	$nextUrl = $currentUrl;	//.'/msgtodate/'.$msgNextDate.$addUrl;	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
    		$("#btnReset").click(function() {
    			 $("form").each(function() {
    			   	this.reset();
    			 });
    		});
	    });

		function sendMessage(){
			if ($('#targetno').val() == ''){
				alert('대상을 선택 하세요.');
				return;
			}
						
			if ($('#msg_content').val() == ''){
				alert('내용을 입력하세요.');
				return;
			}
						
			document.form.target = 'hfrm';
			document.form.action = "<?=$submitUrl?>";
			document.form.submit();			
		}	    
	</script>
<!-- container -->
<div id="container">
	<form name="form" method="post" enctype="multipart/form-data">
	<input type="hidden" id="targetno" name="targetno" value="<?=$targetNum?>"/>
	<input type="hidden" name="msgfrom" value="listview"/>
	<div id="content">

		<div class="title">
			<h2>[<?=$pageTitle?> 대화]</h2>
			<div class="location">Home &gt; 게시물관리 &gt; <?=$pageTitle?> 대화</div>
		</div>
		
		<!-- comment -->
		<div class="comment">
			<div class="sub_title">
				<span class="fl_l font15 bold"><?=$sendUserName?> 님과의 대화</span>
				<a href="<?=$listUrl?>" class="btn1 fl_r">목록</a>
			</div>

			<?if ($currentPage > 1){?>
			<p class="cboth ag_c"><a href="<?=$prevUrl?>/page/<?=($currentPage-1)?><?=$addUrl?>" class="btn2">이전 대화 보기 ▲</a></p>
			<?}?>
			<ul class="comment_ul">
		    <?
		    	$i = 1;
		    	$css = '';
		    	$defaultImg = '/images/adm/default_img.gif';
		    	foreach ($recordSet as $rs):
					$content = nl2br($rs['CONTENT']);
					$css = ($rs['ORG_USER_NUM'] == $rs['USER_NUM']) ? '' : 'class="comment_craftshop"';
					$arrFile = ($rs['SENDER_TYPE'] == 'S') ? explode('|', $rs['SEND_SHOP_FILE_INFO']) : explode('|', $rs['SEND_FILE_INFO']);
					
					$img = ''; //프로필 이미지
					if (!empty($arrFile[0]))
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
					$fileName = (!empty($img)) ? $img : $defaultImg;
										
					if ($rs['MSGCONTENT_TYPE'] == 'F') //첨부한 파일이 있는 경우
					{
						$arrFile = explode('|', $rs['FILE_INFO']);
						if (!empty($arrFile[0]))
						{
							if ($arrFile[4] == 'Y')	//썸네일생성 여부
							{
								$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
							}
							else
							{
								$img = $arrFile[2].$arrFile[3];
							}
							
							$content = $content.'<a href="/download/route/fno/'.$arrFile[0].'">';
							$content .= '<img src="'.$img.'" width="100"/>';
							$content .= '</a>';
						}
					}
					else if ($rs['MSGCONTENT_TYPE'] == 'I') //아이템 문의시 관련아이템
					{
						$arrTxt = explode('|', $content);
						$content = '<img src="'.$arrTxt[0].'" width="100"/>'.$arrTxt[1];
					}
					else if ($rs['MSGCONTENT_TYPE'] == 'O') //주문 문의시 관련 주문
					{
						$arrTxt = explode('|', $content);
						$content = '주문일자 : '.$arrTxt[0].'<br />주문번호 : '.$arrTxt[1];
					}					

					/*
					$sendName = $rs['SEND_USER_NAME'].'<br />('.$rs['SEND_USER_EMAIL_DEC'].')';
					$toName = $rs['TO_USER_NAME'].'<br />('.$rs['TO_USER_EMAIL_DEC'].')';
					if ($rs['SENDER_TYPE'] == 'S' && !empty($rs['SEND_SHOP_NAME'])) //shop자격으로 발송
					{
						$sendName = $rs['SEND_SHOP_NAME'].'<br />('.$rs['SEND_SHOP_CODE'].')';
					}
					if ($rs['TARGET_TYPE'] == 'S' && !empty($rs['TO_SHOP_NAME'])) //shop자격으로 수신
					{
						$toName = $rs['TO_SHOP_NAME'].'<br />('.$rs['TO_SHOP_CODE'].')';
					}
					*/	
			?>
				<li <?=$css?>>			
					<dl class="comment_dl">
						<dt><img src="<?=CDN.$fileName?>" width="80" alt="이미지" /></dt>
						<dd class="day"><?=$rs['CREATE_DATE']?></dd>
						<dd>
							<?=$content?>
						</dd>
					</dl>
				</li>
			<?
					//$tmpUserNum = $rs['USER_NUM'];
					$i++;
				endforeach;
			?>
			</ul>			
			<br /><br />
			<?if ($totPage > $currentPage){?>
			<p class="cboth ag_c"><a href="<?=$nextUrl?>/page/<?=($currentPage+1)?><?=$addUrl?>" class="btn2">이후 대화 보기 ▼</a></p>
			<?}?>			
		</div>
		<!-- //comment -->
		
		<table class="write1 mg_t10">
			<colgroup><col width="10%" /></colgroup>
			<tr>
				<th>메시지 쓰기</th>
				<td>
					<textarea id="msg_content" name="msg_content" rows="5" cols="5" class="textarea1"></textarea>
				</td>
			</tr>
			<tr>
				<th>이미지 첨부</th>
				<td><input type="file" id="userfile0" name="userfile0" class="inp_file" /></td>
			</tr>
		</table>

		<div class="btn_list">
			<a href="#" id="btnReset" class="btn1">내용지우기</a>
			<a href="javascript:sendMessage();" class="btn2">메시지 보내기</a>
			<a href="<?=$listUrl?>" class="btn1 fl_r">목록</a>			
		</div>

	</div>
	</form>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			