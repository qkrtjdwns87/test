<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	$returnUrl = '/app/order_a/partview/ordno/'.$ordNum.'/ordptno/'.$ordPtNum;
	$submitUrl = '/app/order_a/cancelreq/ordno/'.$ordNum.'/ordptno/'.$ordPtNum;	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/popup.css">
	<script src="/js/jquery.base64.min.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	
	    
	    function sendRequest(){
			if (trim($('#reason_cd').val()) == ''){
				alert('사유를 선택해 주세요.');
				return;
			}		    

			if (trim($('#reason_content').val()) == ''){
				alert('상세사유를 입력해 주세요.');
				return;
			}			

			document.form.target = 'hfrm';
			document.form.action = '<?=$submitUrl?>/return_url/'+$.base64.encode('<?=$returnUrl?>');
			document.form.submit();
	    }	    
	</script>
</head>
<body>
<div id="wrap">
	<form name="form" method="post">
	<input type="hidden" name="orderstate" value="5130"/>
	<!-- 환불신청 -->
	<section id="order_popup">
		<ul class="title">
			<li>환불신청 전에 ‘교환 및 환불정책＇을 살펴봐주세요.</li>
			<li>Item 제작상태와 Item 성격에 따라 환불이 어려울 수 있습니다.</li>
		</ul>

		<div class="select_box">
			<select id="reason_cd" name="reason_cd">
				<option value="" selected="selected">환불신청 사유선택</option>
		<?
			$i = 1;
			foreach ($reasonCdSet as $crs):
				if ($crs['NUM'] < 6350 || $crs['NUM'] == 6410)
				{
		?>
				<option value="<?=$crs['NUM']?>"><?=$crs['TITLE']?></option>
		<?
					$i++;					
				}
			endforeach;					
		?>			
			</select>
		</div>

		<div class="comment">
			<textarea id="reason_content" name="reason_content" rows="5" cols="5" placeholder="상세 사유를 입력해 주세요"></textarea>
		</div>
	</section>
	</form>
	<!-- 메뉴바 -->
	<div class="order_btn_list">
		<a href="javascript:sendRequest();">환불신청</a></li>
	</div>
	<!-- //메뉴바 -->
</div>


<script src="/js/app/ui.js"></script>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>	