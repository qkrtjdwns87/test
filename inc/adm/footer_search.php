<?
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- footer -->
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/hidden_frame.php"; ?>
<div id="layer_pop_s" class="pop" style="display:none;">
	<div class="bg"></div>
	<div class="popup_box">
		<div class="top">
			<a href="javascript:;" onclick="$('#layer_pop_s').hide();$('#popfrm_s').attr('src', '');"><img src="/images/adm/layer_btn_close.gif" alt="close" /></a>
		</div>
		
		<div class="iframe"><iframe id="popfrm_s" width="400" height="300" frameborder="0" scrolling="yes"></iframe></div>
	</div>
</div>
<!--// footer -->