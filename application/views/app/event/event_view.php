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
	
	$title = $recordSet['TITLE'];
	$name = $recordSet['USER_NAME'];
	$viewYn = $recordSet['VIEW_YN'];
	$startDate = substr($recordSet['START_DATE'], 0 ,10);
	$endDate = substr($recordSet['END_DATE'], 0 ,10);
	$viewYnTitle = ($viewYn == 'Y') ? '게시' : '미게시';
	$webContent = $recordSet['W_CONTENT'];
	$mobileContent = $recordSet['M_CONTENT'];
	$alwayYn = $recordSet['ALWAYS_YN'];
	$alwayYnTitle = ($alwayYn == 'Y') ? '상시 이벤트' : '';
	$summary = $recordSet['SUMMARY'];

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
	
	$dDay = intval((strtotime(date("Y-m-d",time()))-strtotime($startDate)) / 86400);
	$dDay = ($dDay > 0) ? '+'.abs($dDay) : '-'.abs($dDay);

	$defaultImg = '';
	if (!empty($flist[1]['file_tmpname'])) //이벤트 이미지는 1번 (PC용을 같이 씀)
	{
		$imgUrl = ($flist[1]['thumb_yn'] == 'Y') ? $flist[1]['thumb_file_path'] : $flist[1]['file_path'];
	}
	else
	{
		$imgUrl = $defaultImg;
	}	
	
	$addUrl = (!empty($orderType)) ? '/ordtype/'.$orderType : '';
	$addUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
	
	$listUrl = '/app/item_a/enlist/evtype/'.$eventType.$addUrl;	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/main.css">
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	
	</script>
</head>
<body>	
<div id="wrap">
	
	<section id="section_event">
		<div class="title">
			<span class="dday">D<?=$dDay?></span>
			<p><?=$title?></p>
			<span class="day"><?=$startDate?> ~ <?=$endDate?></span>
		</div>
		
		<?if (!empty($imgUrl)){?>
		<p class="event_img"><img src="<?=$imgUrl?>" alt="" /></p>
		<?}?>
		<div class="event_text"><?=$mobileContent?></div>
	</section>
</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>			