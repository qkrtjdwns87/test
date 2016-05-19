<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
	    $(document).ready(function () {
	    	//app_showMenuWindow('가입완료_14세이상', '<?=$currentUrl?>');
	    });
	</script>
</head>
<body>	
<div id="wrap">
	<input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>
	<section id="section_join_ok">
		<p><img src="/images/app/login/join_ok.jpg" alt="" /></p>
		<ul class="btn_list">
			<li><a href="javascript:app_closeWindow();"><img src="/images/app/login/btn_prev.png" alt="이전 페이지로 가기" /></a></li>
			<li style="float:none;text-align:center;"><a href="/"><img src="/images/app/login/btn_main.png" alt="메인으로 가기" /></a></li>
		</ul>
	</section>

</div>	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		