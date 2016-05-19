<?
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script src="//code.jquery.com/jquery.min.js"></script>
	<script src="/js/common.js"></script>
	
	<title>Insert title here</title>
	<script type="text/javascript">
	    $(document).ready(function () {

	    });	
		function send(){
			if ($('#useremail').val() == ''){
				alert('이메일을 입력하세요.');
				return false;
			}
						
			if ($('#userpw').val() == ''){
				alert('비밀번호를 입력하세요.');
				return false;
			}

			document.form.target = 'hfrm';
			document.form.action = "/user/loginconfirm";			
		}

        function facebookLogin() {
        	//location.href = '<?=$fbLoginUrl?>';
        	openNewsWin('<?=$fbLoginUrl?>');
        }

		function facebookLogout(){
			location.href = '<?=$fbLogoutUrl?>';
			/*
			FB.init({
				appId      : '421593507991526',
				status     : true,
				xfbml      : true,
				cookie	   : true,
				version    : 'v2.4' // or v2.0, v2.1, v2.2, v2.3
			});
						
	        FB.getLoginStatus(function(response) {
	            if (response.status === 'connected') {
	                FB.logout(function(response) {
		                alert('log out');
	                });
	            }
	        });
	        */
		}

        function twitterLogin() {
        	//location.href = '<?=$twLoginUrl?>';
        	openNewsWin('<?=$twLoginUrl?>');        	            
        }

        function twitterLogout() {
        	location.href = '<?=$twLogoutUrl?>';
        }    

        function naverLogin() {
        	//location.href = '<?=$nvLoginUrl?>';
        	openNewsWin('<?=$nvLoginUrl?>');
        }

        function naverLogout() {
        	location.href = '<?=$nvLogoutUrl?>';
        }

        function kakaoLogin(){
        	openNewsWin('<?=$kaLoginUrl?>');
        }

        function kakaoLogout(){
        	location.href = '<?=$kaLogoutUrl?>';
        }

        function googleLogin(){
        	openNewsWin('<?=$ggLoginUrl?>');
        }

        function googleLogout(){
        	location.href = '<?=$ggLogoutUrl?>';
        }        

        function memerJoin() {
            location.href = '/user/writeform';
        }             
	</script>
</head>
	<body>
		<div id="fb-root"></div>
	    <div>Board 작성</div>
	    <form name="form" method="post" onsubmit="return send()">
	    <input type="hidden" id="return_url" name="return_url" value="<?=$returnUrl?>"/>	    
	    <div>
	    	<p>
	    	<?if (!$fbIsLogin){?><a href="javascript:facebookLogin();">Facebook login</a>/<?}?> 
	    	<?if ($fbIsLogin){?><a href="javascript:facebookLogout();">Facebook logout</a>/<?}?>	    	
	    	<?if (!$twIsLogin){?><a href="javascript:twitterLogin();">Twitter login</a>/<?}?>
	    	<?if ($twIsLogin){?><a href="javascript:twitterLogout();">Twitter logout</a>/<?}?>	    	
	    	<?if (!$nvIsLogin){?><a href="javascript:naverLogin();">Naver login</a>/<?}?>
	    	<?if ($nvIsLogin){?><a href="javascript:naverLogout();">Naver logout</a>/<?}?>
	    	<?if (!$kaIsLogin){?><a href="javascript:kakaoLogin();">Kakao login</a>/<?}?>
	    	<?if ($kaIsLogin){?><a href="javascript:kakaoLogout();">Kakao logout</a><?}?>
	    	<?if (!$ggIsLogin){?><a href="javascript:googleLogin();">Google login</a>/<?}?>
	    	<?if ($ggIsLogin){?><a href="javascript:googleLogout();">Google logout</a><?}?>	    	
	    	</p>
	    </div>	    
	    <div>
		    <div>이메일 : <input type="text" id="useremail" name="useremail" style="width:200px" /></div>
		    <div>비밀번호: <input type="text" id="userpw" name="userpw" style="width:200px" /></div>
		    <div>
		    	<p>
		    	<input type="button" id="writeBtn" value="회원가입" onclick="memerJoin();"/> 
		    	<input type="submit" id="writeBtn" value="확인 "/>
		    	</p>
		    </div>
	    </div>
	    </form>
	    <p>Page rendered in <strong>{elapsed_time}</strong> seconds.</p>
		<?
			 include '/inc/hidden_frame.php';		
		?>
    </body>
</html>