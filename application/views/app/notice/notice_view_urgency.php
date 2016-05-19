<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$title = '';
	$content = '';	
	if ($appNoticeSet)
	{
		$title = $appNoticeSet['TITLE'];
		$content = $appNoticeSet['CONTENT'];
	}
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/main.css">
	<script type="text/javascript">
	    $(document).ready(function () {
	    	$(document).on('click', '.faq_list dt', function(){
		    	
	    	});
	</script>
</head>
<body>	
<div id="wrap">

	<section id="section_notice_pop">
		<p class="title"><?=$title?></p>
		<p class="text"><?=$content?></p>
	</section>

</div>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		