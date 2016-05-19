<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$flist = array();
	for($i=0; $i<($fileCnt+1); $i++)
	{	//파일배열 초기화
		$flist[$i]['num'] = '';
		$flist[$i]['file_name'] = '';
		$flist[$i]['file_path'] = '';
		$flist[$i]['file_tmpname'] = '';
		$flist[$i]['thumb_yn'] = 'N';
		$flist[$i]['thumb_file_path'] = '';
	}
	
	$itemName = $baseSet['ITEM_NAME'];
	$itemShopCode = $baseSet['ITEMSHOP_CODE'];
	$itemCode = $baseSet['ITEM_CODE'];
	$pictureYn = $baseSet['PICTURE_YN'];
	$solesaleYn = $baseSet['SOLESALE_YN'];
	$optContent = $baseSet['OPTION_CONTENT'];
	$expContent = $baseSet['EXPLAIN_CONTENT'];
	$makContent = $baseSet['MAKING_CONTENT'];
	$refContent = $baseSet['REFPOLICY_CONTENT'];
	$refPolCodeNum = $baseSet['REFPOLICYCODE_NUM'];
	$orgShopNum = $baseSet['SHOP_NUM'];	//아이템 작성 샵고유번호
	$optionYn = $baseSet['OPTION_YN'];
	$rePresentYn = $baseSet['REPRESENT_YN'];
	$viewYn = $baseSet['VIEW_YN'];
	$itemPrice = $baseSet['ITEM_PRICE'];
	$discountYn = $baseSet['DISCOUNT_YN'];
	$discountPrice = $baseSet['DISCOUNT_PRICE'];
	$approvalDate = $baseSet['APPROVAL_DATE'];
	$approvalUserName = $baseSet['APPROVALUSER_NAME'];
	$itemStatCodeNum = $baseSet['ITEMSTATECODE_NUM'];
	$itemStatCodeTitle = $baseSet['ITEMSTATECODE_TITLE'];
	$itemStatMemo = $baseSet['ITEMSTATE_MEMO'];
	$maxBuyCount = $baseSet['MAXBUY_COUNT'];
	$stockFreeYn = $baseSet['STOCKFREE_YN'];
	$stockCount = $baseSet['STOCK_COUNT'];
	$payAfterCancelYn = $baseSet['PAYAFTER_CANCEL_YN'];
	$payafterCancelMemo = $baseSet['PAYAFTER_CANCEL_MEMO'];
	$madeAfterRefundYn = $baseSet['MADEAFTER_REFUND_YN'];
	$madeAfterRefundMemo = $baseSet['MADEAFTER_REFUND_MEMO'];
	$madeAfterChangeYn = $baseSet['MADEAFTER_CHANGE_YN'];
	$madeAfterChangeMemo = $baseSet['MADEAFTER_CHANGE_MEMO'];
	$chargeType = $baseSet['CHARGE_TYPE'];
	$itemCharge = intval($baseSet['ITEM_CHARGE']);
	$payCharge = intval($baseSet['PAY_CHARGE']);
	$taxCharge = intval($baseSet['TAX_CHARGE']);
	$chargeTypeUpdateDate = $baseSet['CHARGETYPE_UPDATE_DATE'];
	$apprFirstReqDate = $baseSet['APPROVAL_FIRSTREQ_DATE'];
	$adYn = $baseSet['AD_YN'];
	$modiReason = $baseSet['MODIFY_REASON'];
	$originalItemNum = $baseSet['ORIGINAL_ITEM_NUM'];

	$shopRefContent = $polSet['REFPOLICY_CONTENT'];
	$mallRefContent = $stdPolSet['REFPOLICY_CONTENT'];

	if (empty($refPolCodeNum)) $refPolCodeNum = '12040'; //Mall 정책 사용
	if ($refPolCodeNum == '12020')
	{
		//아이템 개별
		$isRefContentView = TRUE;	//textarea 보이게
	}

	if ($refPolCodeNum == '12030')
	{
		//shop 정책 사용
		$isRefContentView = FALSE;	//textarea 보이지 않게
		$refContent = $shopRefContent;
	}

	if ($refPolCodeNum == '12040')
	{
		//circus 정책 사용
		$isRefContentView = FALSE;	//textarea 보이지 않게
		$refContent = $mallRefContent;
	}

	if (isset($fileSet))
	{
		for($i=0; $i<count($fileSet); $i++)
		{
			$flist[$i]['num'] = $fileSet[$i]['NUM'];
			$flist[$i]['file_name'] = $fileSet[$i]['FILE_NAME'];
			$flist[$i]['file_tmpname'] = $fileSet[$i]['FILE_TEMPNAME'];
			$flist[$i]['file_path'] = $fileSet[$i]['FILE_PATH'].$fileSet[$i]['FILE_TEMPNAME'];
			$flist[$i]['thumb_yn'] = $fileSet[$i]['THUMB_YN'];
			$flist[$i]['thumb_file_path'] = ($fileSet[$i]['THUMB_YN'] == 'Y') ? str_replace('.', '_s.', $flist[$i]['file_path']) : '';
		}
	}

	$fileCnt = (count($fileSet) == 0) ? 1 : (count($fileSet) / 2);

	$tag = '';
	if (isset($tagSet))
	{
		for($i=0; $i<count($tagSet); $i++)
		{
			$tag .= $tagSet[$i]['TAG'].',';
		}

		$tag = (count($tagSet) > 0) ? substr($tag, 0, -1) : '';
	}

	//if (!isset($cateSet)) $cateSet = array();	

	//SNS에 공유할 내용
	$snsImgUrl = '';
	$fullUrl = $siteDomain."/app/item_a/viewshare/sno/".$sNum."/sino/".$siNum;
	$snsShortUrl = $fullUrl;//$this->common->getShortURL($fullUrl);
	$snsSet = array(
		'facebook_appId' => $this->config->item('facebook_appid'),
		'twitter_key' => $this->config->item('twitter_consumer_key'),
		'kakao_Key' => $this->config->item('kakao_javascript_key'),
		'insta_clientId' => $this->config->item('insta_client_id'),
		'snsTitle' => $itemName,
		'snsMsg' => $this->common->cutStr($this->common->stripHtmlTags($expContent), 80, '...'),
		'snsLink' => $fullUrl,
		'snsDomain' => $snsShortUrl	//$fullUrl
	);
	if (!empty($flist[0]['file_tmpname']))
	{
		$snsImgUrl = ($flist[0]['thumb_yn'] == 'Y') ? $flist[0]['thumb_file_path'] : $flist[0]['file_path'];
		$snsImgUrl = $siteDomain.$snsImgUrl;
		$snsSet['snsImgUrl'] = $snsImgUrl;
	}	
	
	$addUrl = (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';	
	
	$commentWriteUrl = '/app/item_a/commentwrite/sno/'.$sNum.'/sino/'.$siNum.$addUrl;
	$commentDeleteUrl = '/app/item_a/commentdelete';
	$isLoginJs = ($isLogin) ? 'true' : 'false';
	$loginUserNum = ($isLogin) ? get_cookie('usernum') : 0;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/item.css">
	<link rel="stylesheet" type="text/css" href="/css/app/swiper.min.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="/js/app/swiper.min.js"></script>
	<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>	
	<script src='http://connect.facebook.net/en_US/all.js'></script>	
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
		var isLogin = <?=$isLoginJs?>;
	    //sns 공유될 내용
        var snsTitle = "<?=$snsSet['snsTitle']?>";
        var snsMsg = "<?=$snsSet['snsMsg']?>";
        var snsImgUrl = "<?=$snsSet['snsImgUrl']?>";
        var snsLink = "<?=$snsSet['snsLink']?>";
        var snsDomain = "<?=$snsSet['snsDomain']?>";
		var fbAppId = "<?=$snsSet['facebook_appId']?>";
		var kakaoKey = "<?=$snsSet['kakao_Key']?>";
		var sno = <?=$sNum?>;		
		var sino = <?=$siNum?>;
	    $(document).ready(function () {
	    	//$(".expDisp").toggle();     				    
	    });	
	</script>
</head>
<body>
 <div id="instafeed"></div> 
<div id="wrap">
	<!-- <a href="#wrap" class="btn_top"><img src="/images/app/common/btn_top.png" alt="top" /></a> -->

	<!-- item_view_detail -->
	<section id="item_view_detail">

		<!-- 제품정보 -->
		<div class="view_detail">
		<?if ($optSet || !empty($optContent)){?>
			<dl class="option">
				<dt>Item 옵션</dt>
		    <?
		    	$i = 1;
		    	foreach ($optSet as $rs):
		    		$optTitle = $rs['OPT_TITLE'];
			?>					
				<dd>
					<span class="tit"><?=$optTitle?> :</span>
					<span class="cont">
			    <?
			    	$t = 1;
			    	foreach ($rs['optSubSet'] as $srs):
			    		$buyCount = (!empty($srs['BUY_COUNT'])) ? $srs['BUY_COUNT'] : 0;
			    		$optSubTitle = $srs['OPTSUB_TITLE'];
			    		$optSubPrice = $srs['OPTION_PRICE'];
			    		$soldOutYn = ($srs['SOLDOUT_YN'] == 'Y') ? '(품절)' : '';
			    		echo $optSubTitle.' '.$soldOutYn.'<br />';
						$t++;
					endforeach;
				?>											
					</span>
				</dd>
			<?
					$i++;
				endforeach;
				
				if (!empty($optContent))
				{
			?>		
				<dd><?=nl2br($optContent)?></dd>	
			<?
				}
			?>	
			</dl>
		<?}?>
		<?if (!empty($expContent)){?>
			<dl class="explanation">
				<dt>Item 설명</dt>
				<dd><?=nl2br($expContent)?></dd>
				<!-- <a href="javascript:;" id="exp_more" class="btn_more"><img src="/images/app/main/bestitem/btn_more.png" alt="더 보기" /></a> -->
			</dl>
		<?}?>
		<?if (!empty($makContent)){?>
			<dl>
				<dt>제작 및 예상도착일</dt>
				<dd><?=nl2br($makContent)?></dd>
			</dl>
		<?}?>			
		<?if (!empty($refContent)){?>			
			<dl>
				<dt>교환 및 환불 정책</dt>
				<dd><?=nl2br($refContent)?></dd>
			</dl>
		<?}?>			
		</div>
		
		<!-- 구매후기 -->
		<section id="item_view_comment_buy">
			<div class="item_comment_buy">
				<a href="javascript:app_showPopUpWindow('구매후기', '<?=$siteDomain?>/app/item_a/reviewlist/sno/<?=$sNum?>/sino/<?=$siNum?>');" class="btn_more"><span>구매후기 (<?=number_format($reviewRsTotCnt)?>)</span></a>
				<ul>
				<?
					if ($reviewRsTotCnt == 0)
					{				
				?>
					<li class="first_comment">
						<!-- <p class="img"><img src="/images/app/main/first_comment.png" alt="circus" /></p> -->
						<p class="img"><img src="/images/app/common/no_content_big.png" alt="circus" /></p>
						<!-- <p class="title"><span>첫번째 발견자</span>가 되어 보세요.</p> -->
						<a href="javascript:app_showPopUpWindow('구매후기', '<?=$siteDomain?>/app/item_a/reviewlist/sno/<?=$sNum?>/sino/<?=$siNum?>');" class="btn_comment">구매후기 남기기<span>&gt;</span></a>
					</li>
				<?
					}
					else 
					{
						$i = 1;
						$defaultImg = '/images/app/main/photo.jpg';
						$compDate = date("Y-m-d",strtotime("-1 day"));
						foreach ($reviewRsSet as $rs):
							$no = (($reviewRsTotCnt - $i ) + 1) - (( $currentPage -1) * $listCount);
							$score = $rs['SCORE'];
							$content = nl2br($rs['CONTENT']);
							//$itemUrl = '/manage/item_m/updateform/sno/'.$rs['SHOP_NUM'].'/sino/'.$rs['ITEM_NUM'];
							//$shopUrl = '/manage/shop_m/view/sno/'.$rs['SHOP_NUM'];
							//$userUrl = '/manage/user_m/updateform/uno/'.$rs['USER_NUM'];
							$img = '';
							$arrFile = explode('|', $rs['PROFILE_FILE_INFO']);
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
							
							if ($rs['DEL_YN'] == 'Y')
							{
				?>
					<li class="reple_del">
						<p><img src="/images/app/main/icn_reple_del.png" alt="circus" /></p>
						<span>관리자에 의해 삭제된 흔적입니다.</span>
					</li>					
				<?
							}
							else
							{
								$userNum = $rs['USER_NUM'];
								$userName = $this->common->ucn($rs['USER_EMAIL_DEC']);
								
								$isSelf = 0;
								//본인인 경우
								if ($loginUserNum == $userNum) $isSelf = 1;								
				?>
					<li>
						<div class="img"><a href="javascript:app_showUserFlagPage('<?=$userName?>', '<?=$userNum?>', '<?=$isSelf?>');"><img src="<?=$fileName?>" alt="" /></a></div>
						<div class="name"><span><?=$userName?></span></div>
						<span class="star star<?=$score?>"></span>
						<div class="text"><?=$content?></div>
					</li>						
				<?
							}				
							$i++;
						endforeach;				
					}
				?>
				</ul>
			</div>
		</section>
		<?
			$shopCode = $shopBaseSet['SHOP_CODE'];
			$shopNum = $shopBaseSet['NUM'];
			$shopName = $shopBaseSet['SHOP_NAME'];
			$shopUserName = $shopBaseSet['SHOPUSER_NAME'];
			$TodayAuthorYn = $shopBaseSet['TODAYAUTHOR_YN'];
			$popAuthorYn = $shopBaseSet['POPAUTHOR_YN'];
			$shopProfile = $shopBaseSet['PROFILE_CONTENT'];
			$shopItemCount = $shopBaseSet['TOTITEM_COUNT'];
			$arrFile = explode('|', $shopBaseSet['PROFILE_FILE_INFO']);
			$defaultImg = '/images/app/main/bestitem/sample3.jpg';
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


		?>
		<!-- CRAFT SHOP -->
		<div id="item_view_shop">
			<a href="javascript:app_showCraftShopPage('<?=$sNum?>');" onclick="showCraftShopPage('<?=$sNum?>')"><div class="top">Craft Shop</div></a>
			<dl>
				<dt><img class="craft_thumbnail" src="<?=$fileName?>" alt="" /></dt>
				<dd class="title"><?=$shopName?> </dd>
				<dd class="author">
					<span class="name">작가 <?=$shopUserName?></span>
					<?if ($popAuthorYn == 'Y'){?><span class="popularity">인기</span><?}?>
					<?if ($TodayAuthorYn == 'Y'){?><span class="today_author">오늘의 작가</span><?}?>
				</dd>
				<dd class="text"><?=$shopProfile?></dd>
			</dl>

			<ul class="thumbnail">
			<?

				$i = 1;
				$defaultImg = '/images/app/main/photo.jpg';
				foreach ($shopBestItemSet as $rs):
					$img = $url = '';
					$arrFile = explode('|', $rs['M_FILE_INFO']);
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

					log_message('debug', '[CircusLog] ' . $fileName);

					if (!empty($fileName))
					{
						$url = '/app/item_a/view/sno/'.$sNum.'/sino/'.$rs['SHOPITEM_NUM'];
			?>
				<li><a href="<?=$url?>"><img src="<?=$fileName?>" alt="" /></a></li>
			<?
						$i++;			
					}
				endforeach;
				
				$shopItemCount = ($shopItemCount > 0) ? $shopItemCount - ($i - 1) : 0;
			?>				
			</ul>
			<?if ($shopItemCount > 0){?>
			<p class="btn_385more"><a href="javascript:app_showPopUpWindow('Craft Shop Item', '<?=$siteDomain?>/app/shop_a/bestlist/sno/<?=$sNum?>');"><?=number_format($shopItemCount)?> more <span>&gt;</span></a></p>
			<?}?>

		</div>
	</section>
	<!-- //item_view_detail -->
		
	<!-- 이 ITEM과 함께 보는 추천 ITEM -->
	<?if ($recommItemSet){?>	
	<section id="item_view_more">
		<h2 class="title">이 Item과 함께 보는 추천 Item</h2>

		<!-- touchSlider -->
		<div class="img_silde">
			<div class="top-swiper-container">
				<ul class="swiper-wrapper">
					<?if (isset($recommItemSet[0])){?>
					<li class="swiper-slide">
						<!-- 제품리스트 type1 -->
						<ul class="product_type1">
						<?
							$i = 1;
							$defaultImg = '/images/app/main/photo.jpg';
							foreach ($recommItemSet[0] as $rs):
								$url = '/app/item_a/view/sno/'.$sNum.'/sino/'.$rs['SHOPITEM_NUM'];
								$img = '';
								$arrFile = explode('|', $rs['M_FILE_INFO']);
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
								$flagCss = ($rs['ITEM_FLAG']) ? ' on' : '';
								$itemName = $this->common->cutStr($rs['ITEM_NAME'], 30, '..');
								$shopName = $rs['SHOP_NAME'];
								$stockFreeYn = $rs['STOCKFREE_YN'];
								$stockCount = $rs['STOCK_COUNT'];
								$itemStateCodeNum = $rs['ITEMSTATECODE_NUM'];
								$soldOutCss = (($stockFreeYn == 'N' && $stockCount) || $itemStateCodeNum == 8070) ? ' on' : '';
						?>
							<li>
								<span class="sale"></span>
								<span id="item_<?=$rs['SHOPITEM_NUM']?>" class="flag<?=$flagCss?>" onclick="webFlaging('item', '<?=$rs['SHOPITEM_NUM']?>', '<?=$sNum?>');"></span>
								<a href="javascript:app_moveToItemDetail('<?=$sNum?>', '<?=$rs['SHOPITEM_NUM']?>');"><img src="<?=$fileName?>" class="img_box" /></a>
								<p class="soldout<?=$soldOutCss?>"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name"><?=$itemName?></p>
								<p class="shop"><?=$shopName?></p>
							</li>
						<?
								$i++;			
							endforeach;
						?>								
						</ul>
						<!-- //제품리스트 type1 -->
					</li>
					<?}?>
					<?if (isset($recommItemSet[1])){?>
					<li class="swiper-slide">
						<!-- 제품리스트 type2 -->
						<ul class="product_type1">
						<?
							$i = 1;
							$defaultImg = '/images/app/main/photo.jpg';
							foreach ($recommItemSet[1] as $rs):
								$url = '/app/item_a/view/sno/'.$sNum.'/sino/'.$rs['SHOPITEM_NUM'];
								$img = '';							
								$arrFile = explode('|', $rs['M_FILE_INFO']);
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
								$flagCss = ($rs['ITEM_FLAG']) ? ' on' : '';
								$itemName = $this->common->cutStr($rs['ITEM_NAME'], 30, '..');
								$shopName = $rs['SHOP_NAME'];
								$stockFreeYn = $rs['STOCKFREE_YN'];
								$stockCount = $rs['STOCK_COUNT'];
								$itemStateCodeNum = $rs['ITEMSTATECODE_NUM'];
								$soldOutCss = (($stockFreeYn == 'N' && $stockCount) || $itemStateCodeNum == 8070) ? ' on' : '';
						?>
							<li>
								<span class="sale"></span>
								<span class="flag<?=$flagCss?>" onclick="webFlaging('item', '<?=$rs['SHOPITEM_NUM']?>', '<?=$sNum?>');"></span>
								<a href="javascirpt:;" onclick="javascript:app_moveToItemDetail('<?=$sNum?>', '<?=$rs['SHOPITEM_NUM']?>');"><img src="<?=$fileName?>" class="img_box" /></a>
								<p class="soldout<?=$soldOutCss?>"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name"><?=$itemName?></p>
								<p class="shop"><?=$shopName?></p>
							</li>
						<?
								$i++;			
							endforeach;
						?>								
						</ul>
						<!-- //제품리스트 type2 -->
					</li>
					<?}?>					
					<?if (isset($recommItemSet[2])){?>
					<li class="swiper-slide">
						<!-- 제품리스트 type3 -->
						<ul class="product_type1">
						<?
							$i = 1;
							$defaultImg = '/images/app/main/photo.jpg';
							foreach ($recommItemSet[2] as $rs):
								$url = '/app/item_a/view/sno/'.$sNum.'/sino/'.$rs['SHOPITEM_NUM'];
								$img = '';							
								$arrFile = explode('|', $rs['M_FILE_INFO']);
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
								$flagCss = ($rs['ITEM_FLAG']) ? ' on' : '';
								$itemName = $this->common->cutStr($rs['ITEM_NAME'], 30, '..');
								$shopName = $rs['SHOP_NAME'];
								$stockFreeYn = $rs['STOCKFREE_YN'];
								$stockCount = $rs['STOCK_COUNT'];
								$itemStateCodeNum = $rs['ITEMSTATECODE_NUM'];
								$soldOutCss = (($stockFreeYn == 'N' && $stockCount) || $itemStateCodeNum == 8070) ? ' on' : '';
						?>
							<li>
								<span class="sale"></span>
								<span class="flag<?=$flagCss?>" onclick="webFlaging('item', '<?=$rs['SHOPITEM_NUM']?>', '<?=$sNum?>');"></span>
								<a href="javascirpt:;" onclick="javascript:app_moveToItemDetail('<?=$sNum?>', '<?=$rs['SHOPITEM_NUM']?>');"><img src="<?=$fileName?>" class="img_box" /></a>
								<p class="soldout<?=$soldOutCss?>"><img src="/images/app/main/soldout.png" class="img_box" /></p>
								<p class="name"><?=$itemName?></p>
								<p class="shop"><?=$shopName?></p>
							</li>
						<?
								$i++;			
							endforeach;
						?>								
						</ul>
						<!-- //제품리스트 type3 -->
					</li>
					<?}?>					
				</ul>
			</div>
			<div class="swiper-pagination"></div>
		</div>
		<!-- //touchSlider -->
	</section>
	<?}?>
	
	<!-- 한줄 흔적 남기기 -->
	<section id="item_view_comment">
		<div class="item_comment">
			<a href="javascript:app_showPopUpWindow('한 줄 댓글', '<?=$siteDomain?>/app/item_a/commentlist/sno/<?=$sNum?>/sino/<?=$siNum?>');" class="btn_more"><span id="totalnum">한 줄 댓글 (<span style="padding-left:0px;"><?=number_format($commentRsTotCnt)?></span>)</span></a>
			<ul>
			<?
				if ($commentRsTotCnt == 0)
				{				
			?>
				<li class="first_comment">
					<!-- <p class="img"><img src="/images/app/main/first_comment.png" alt="circus" /></p> -->
					<p class="img"><img src="/images/app/common/no_content_big.png" alt="circus" /></p>
					<!-- <p class="title"><span>한 줄 댓글</span>을 남겨 보세요.</p> -->
					<a href="javascript:app_showPopUpWindow('한 줄 댓글', '<?=$siteDomain?>/app/item_a/commentlist/sno/<?=$sNum?>/sino/<?=$siNum?>');" class="btn_comment">한 줄 댓글 남기기<span>&gt;</span></a>
				</li>
			<?
				}
				else 
				{	
					$i = 1;
					$defaultImg = '/images/app/main/photo.jpg';
					$compDate = date("Y-m-d",strtotime("-1 day"));
					foreach ($commentRsSet as $rs):
						$replyCss = ($rs['DEPTH'] > 0) ? ' class="reple"' : '';
						$content = nl2br($rs['CONTENT']);
						$img = '';
						$arrFile = explode('|', $rs['PROFILE_FILE_INFO']);
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
						
						$isWriter = FALSE; //본인여부
						if ($isLogin)
						{
							if ($rs['USER_NUM'] == $loginUserNum) $isWriter = TRUE;
						}
						
						if ($rs['DEL_YN'] == 'Y')
						{						
			?>			
				<li class="reple_del">
					<p><img src="/images/app/main/icn_reple_del.png" alt="circus" /></p>
					<span>관리자에 의해 삭제된 흔적입니다.</span>
				</li>
			<?
						}
						else 
						{
							$userNum = $rs['USER_NUM'];
							$userName = $this->common->ucn($rs['USER_EMAIL_DEC']);	
							
							$isSelf = 0;
							//본인인 경우 
							if ($loginUserNum == $userNum) $isSelf = 1;
			?>
				<li<?=$replyCss?> id="cindex_<?=$rs['NUM']?>">
					<div class="img"><a href="javascript:app_showUserFlagPage('<?=$userName?>', '<?=$userNum?>', '<?=$isSelf?>');"><img src="<?=$fileName?>" alt="" /></a></div>
					<div class="name"><span><?=$this->common->ucn($rs['USER_EMAIL_DEC'])?></span></div>
					<span class="time"><?=substr($rs['CREATE_DATE'], 0, 16)?></span>
					<div class="text">
						<?=$content?>
						<?if ($isWriter){?>
						<p class="btn_del"><a href="javascript:commentDel('<?=$rs['NUM']?>');"><span>삭제</span></a></p>
						<?}?>
					</div>
				</li>
			<?
						}
						$i++;			
					endforeach;
				}
			?>					
			</ul>
		</div>
		<!-- 
		<form name="form" method="post">
		<input type="hidden" id="itemno" name="itemno" value="<?=$siNum?>"/>
		<div class="item_view_comment_write">
			<dl>
				<dt><textarea id="brd_content" name="brd_content" class="inp_write" placeholder="최대 100자 댓글 입력"></textarea></dt>
				<dd><a href="javascript:sendComment();" class="btn_write"><img src="/images/app/main/bestitem/btn_write.png" alt="등록" /></a></dd>
			</dl>
		</div>
		</form>
		 -->
	</section>
</div>

<!-- <p><a href="javascript:;" onclick="snsShareOpen();">sns 공유하기 레이어</a></p> -->
<!-- SNS 공유하기 레이어 -->
<div id="layer_sns" class="pop">
	<div class="bg"></div>
	<div class="popup_box">
		<div class="top">
			<p>공유하기</p>
			<a href="javascript:;" onclick="$('.pop').hide();" class="btn_close"><img src="/images/app/main/bestitem/layer_sns_btn_close.png" alt="close" /></a>
		</div>
		<ul>
			<li>
				<p class="img"><img src="/images/app/common/no_content_big.png" alt="circus" /></p>
				<p class="title"><span>준비중 입니다.</span>
			</li> 
			<!-- li><a href="javascript:snsShare('kakao');"><img src="/images/app/main/bestitem/sns_ka.png" alt="카카오톡" /><span>카카오톡</span></a></li>
			<li><a href="javascript:snsShare('facebook');"><img src="/images/app/main/bestitem/sns_fb.png" alt="페이스북" /><span>페이스북</span></a></li>
			<li><a href="javascript:snsShare('twitter');"><img src="/images/app/main/bestitem/sns_tw.png" alt="트위터" /><span>트위터</span></a></li>
			<li><a href="javascript:snsShare('kakaostory');"><img src="/images/app/main/bestitem/sns_ks.png" alt="카카오스토리" /><span>카카오스토리</span></a></li-->
			 
		</ul>
	</div>
</div>
<!-- //SNS 공유하기 레이어 -->

<!-- <script src="/js/app/ui.js"></script> -->
<script>
	circus.initItemView();
</script>


<!-- Initialize Swiper -->
<script>
var swiper = new Swiper('.top-swiper-container', {
	paginationClickable: true,
	pagination: '.swiper-pagination'
});

function showCraftShopPage(no) {
	
    var IOSframe = document.createElement('iframe');
    IOSframe.style.display = 'none';
    IOSframe.src = 'jscall://showCrafeShopPage/' + no;
    document.documentElement.appendChild(IOSframe);
}

</script>


<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		