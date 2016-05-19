<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	if (empty($orderType) || $orderType == 'create_date')
	{
		$ordcrCss = ' class="on"';
		$ordenCss = '';
	}
	else 
	{
		$ordcrCss = '';		
		$ordenCss = ' class="on"';
	}

	$addUrl = (!empty($orderType)) ? '/ordtype/'.$orderType : '';
	$addUrl .= (!empty($currentPage)) ? '/page/'.$currentPage : '';
	$addUrl .= (!empty($currentParam)) ? $currentParam : '';
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/main.css">
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	

	    function evOrderChange(ordtype){
		    location.href='<?=$currentUrl?>/ordtype/'+ordtype;
	    }
	</script>
</head>
<body>	
<div id="wrap">
	<?if ($rsTotalCount > 0){?>
	<section id="section_event_list">
		<div class="btn_list">
			<ul>
				<li><a href="javascript:evOrderChange('create_date');"<?=$ordcrCss?>>등록일순</a></li>
				<li><a href="javascript:evOrderChange('end_date');"<?=$ordenCss?>>종료일순</a></li>
			</ul>
		</div>

		<ul class="event_list">
	    <?
	    	$i = 1;
	    	$header = '';
	    	$compDate = date("Y-m-d",strtotime("-1 day"));
	    	foreach ($recordSet as $rs):
				$no = (($rsTotalCount - $i ) + 1) - (( $currentPage -1) * $listCount);
				$url = '/app/item_a/enview/evtype/'.$eventType.'/enno/'.$rs['NUM'].$addUrl;
	    		$createDate = $rs['CREATE_DATE'];
				$isNewCss = (subStr($createDate, 0, 10) > $compDate) ? 'on' : 'off';
				$title = $this->common->cutStr($rs['TITLE'], 30, '..');
				$startDate = subStr($rs['START_DATE'], 0, 10);
				$endDate = subStr($rs['END_DATE'], 0, 10);
				$dDay = intval((strtotime(date("Y-m-d",time()))-strtotime($startDate)) / 86400);
				$dDay = ($dDay > 0) ? '+'.abs($dDay) : '-'.abs($dDay);
		?>			
			<li>
				<a href="<?=$url?>">
					<span class="dday">D<?=$dDay?></span>
					<p><?=$title?></p>
					<span class="day"><?=$createDate?></span>
				</a>
			</li>
		<?
				$i++;
			endforeach;
		?>				
		</ul>
	</section>
	
	<?}else{?>
	<!-- 진행중인 이벤트 없음 -->
	<section id="pop_event_noting">
		<dl>
			<dt>
				<p class="sorry_img"><img src="/images/app/common/no_content_big.png" alt="" /></p>
				<p><strong>현재 진행중인 이벤트가 없습니다.</strong> <!-- <br />당신이 당첨되는 그 날까지 이벤트는 계속됩니다.</p> -->
			</dt>
			<!-- <dd>멈칫하는 순간, 이벤트는 종료!
				<span>- Written by CIRCUS Master</span>
			</dd> -->

		</dl>
	</section>	
	<?}?>
</div>	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>			