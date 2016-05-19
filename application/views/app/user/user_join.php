<?
	defined('BASEPATH') OR exit('No direct script access allowed');
	
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/login.css">
	<script type="text/javascript">
	    $(document).ready(function () {
	    	//app_showMenuWindow('회원가입방법선택', '<?=$currentUrl?>');
	    });	

		function facebookLogin() {
			location.href = '<?=$fbLoginUrl?>';
		}
	
		function facebookLogout(){
			location.href = '<?=$fbLogoutUrl?>';
		}	    

		function twitterLogin() {
			//location.href = '<?=$twLoginUrl?>';
			location.href = '<?=$twLoginUrl?>';        	            
		}
	
		function twitterLogout() {
			location.href = '<?=$twLogoutUrl?>';
		}    
	
		function naverLogin() {
			//location.href = '<?=$nvLoginUrl?>';
			location.href = '<?=$nvLoginUrl?>';
		}
	
		function naverLogout() {
			location.href = '<?=$nvLogoutUrl?>';
		}
	
		function kakaoLogin(){
			location.href = '<?=$kaLoginUrl?>';
		}
	
		function kakaoLogout(){
			location.href = '<?=$kaLogoutUrl?>';
		}
	
	    function googleLogin(){
	    	location.href = '<?=$ggLoginUrl?>';
	    }
	
	    function googleLogout(){
	    	location.href = '<?=$ggLogoutUrl?>';
	    }        
	
	    function memberJoinForm() {
	        location.href = '/app/user_a/writeform/return_url/<?=$returnUrl?>';
	    }
			
	</script>
</head>
<body>	
<div id="wrap">
	<section id="section_join">
		<dl>
			<dt>CIRCUS 일반회원으로 가입</dt>
			<dd>Item 구매 시 보다 편하게 이용하실 수 있어요.</dd>
			<dd><a href="javascript:memberJoinForm();" class="btn_join">회원가입</a></dd>
		</dl>

		<p class="title_or"><span>또는</span></p>

		<dl>
			<dt>SNS/네이버 계정으로 가입</dt>
			<dd>회원님의 허락없이는 어떤 것도 SNS에 게재하지 않습니다.</dd>
			<dd>
				<ul>
					<li class="join_fb">
						<a href="javascript:facebookLogin();">페이스북 아이디로 가입</a>
						<!-- 					
				    	<?if (!$fbIsLogin){?><a href="javascript:facebookLogin();"><img src="/images/app/login/btn_join_fb.png" alt="페이스북 아이디로 가입" /></a><?}?> 
				    	<?if ($fbIsLogin){?><a href="javascript:facebookLogout();"><img src="/images/app/login/btn_join_fb.png" alt="페이스북 아이디로 가입" /></a><?}?>					
				    	 -->
					</li>
					<li class="join_tw">
						<a href="javascript:twitterLogin();">트위터 아이디로 가입</a>
						<!-- 					
				    	<?if (!$twIsLogin){?><a href="javascript:twitterLogin();"><img src="/images/app/login/btn_join_tw.png" alt="트위터 아이디로 가입" /></a><?}?>
				    	<?if ($twIsLogin){?><a href="javascript:twitterLogout();"><img src="/images/app/login/btn_join_tw.png" alt="트위터 아이디로 가입" /></a><?}?>
 						-->				    						
					</li>
					<li class="join_naver">
						<a href="javascript:naverLogin();">네이버 아이디로 가입</a>
						<!-- 
				    	<?if (!$nvIsLogin){?><a href="javascript:naverLogin();"><img src="/images/app/login/btn_join_naver.png" alt="네이버 아이디로 가입" /></a><?}?>
				    	<?if ($nvIsLogin){?><a href="javascript:naverLogout();"><img src="/images/app/login/btn_join_naver.png" alt="네이버 아이디로 가입" /></a><?}?>
				    	 -->					
					</li>
					<li class="join_kt">
						<a href="javascript:kakaoLogin();">카카오 아이디로 가입</a>
						<!-- 
				    	<?if (!$kaIsLogin){?><a href="javascript:kakaoLogin();"><img src="/images/app/login/btn_join_kt.png" alt="카카오 아이디로 가입" /></a><?}?>
				    	<?if ($kaIsLogin){?><a href="javascript:kakaoLogout();"><img src="/images/app/login/btn_join_kt.png" alt="카카오 아이디로 가입" /></a><?}?>
				    	 -->					
					</li>
					<li class="join_google">
						<a href="javascript:googleLogin();">구글 아이디로 가입</a>
						<!-- 
				    	<?if (!$ggIsLogin){?><a href="javascript:googleLogin();"><img src="/images/app/login/btn_join_google.png" alt="구글 아이디로 가입" /></a><?}?>
				    	<?if ($ggIsLogin){?><a href="javascript:googleLogout();"><img src="/images/app/login/btn_join_google.png" alt="구글 아이디로 가입" /></a><?}?>
				    	 -->					
					</li>
				</ul>
			</dd>
		</dl>

	</section>

</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		