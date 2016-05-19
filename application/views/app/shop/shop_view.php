<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$isLoginJs = ($isLogin) ? 'true' : 'false';
	$loginUserNum = ($isLogin) ? get_cookie('usernum') : 0;
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/item.css">
	<link rel="stylesheet" type="text/css" href="/css/app/swiper.min.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="/js/app/swiper.min.js"></script>
	<script type="text/javascript">
		var isLogin = <?=$isLoginJs?>;
		var sno = <?=$sNum?>;		
		var sino = <?=$siNum?>;		
		$(document).ready(function(){

		});
	</script>
	</head>
<body>	
<div id="wrap">

	<!-- POFF 소개 -->
	<section id="poff_info">
		<table cellpadding="0" cellspacing="0" class="poff_tb">
			<tr>
				<th>작가</th>
				<td><?=$shopSet['SHOPUSER_NAME']?></td>
			</tr>
			<tr>
				<th>Craft Shop런칭</th>
				<td><?=$shopSet['PROFILE_DATE']?></td>
			</tr>
			<?
				$shopNameJs = addslashes(htmlspecialchars($shopSet['SHOP_NAME']));			
			?>
			<tr>
				<th>Craft Shop Flag 수</th>
				<td><a href="javascript:app_showShopFlagUserList('<?=$shopNameJs?>', '<?=$sNum?>');"><?=number_format($shopSet['TOTFLAG_COUNT'])?></a></td>
			</tr>
		</table>
		<?
			$defaultImg = $fileName = '';
			if ($shopSet['MAIN_M_FILE_INFO'])
			{
				$arrFile = explode('|', $shopSet['MAIN_M_FILE_INFO']);
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
			}
		?>
		<p class="img"><img src="<?=$fileName?>" alt="" /></p>
		<div class="text"><?=$shopSet['PROFILE_CONTENT']?></div>
		
	</section>
</div>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		