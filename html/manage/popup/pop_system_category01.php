<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CIRCUS ADMIN</title>
<link rel="stylesheet" type="text/css" href="/css/adm/common.css" />
<link rel="stylesheet" type="text/css" href="/css/adm/style.css" />
</head>
<body>

<!-- popup -->
<div id="popup">
	
	<div class="sub_title">
		<h2 class="font14 bold">[권한 확인 및 수정]</h2><br />
		<p>관리자 메뉴에 대한 이용권한을 부여할 메뉴만 체크해 주십시오.</p>
	</div>
	
	<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$(".sys_category dt").click(function(){
			$(".sys_category dd").slideUp();
			$(".sys_category dt").find("span").css('background','url(..//images/adm/icn_off.png) no-repeat 0 0');
			if(!$(this).next().is(":visible"))
			{
				$(this).find("span").css('background','url(..//images/adm/icn_on.png) no-repeat 0 0');
				$(this).next().slideDown();
			}
			return false;
		});
	});
	</script>
	<div class="sys_category">
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />회원관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />Craft Shop관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />전체Shop현황</label></li>
					<li><label><input type="checkbox" class="inp_check" />승인대기</label></li>
					<li><label><input type="checkbox" class="inp_check" />신규신청</label></li>
					<li><label><input type="checkbox" class="inp_check" />공지신청</label></li>
					<li><label><input type="checkbox" class="inp_check" />1:1 문의</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />Item 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />주문 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />정산 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />게시물 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />앱 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />시스템 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />마케팅 관리</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
		<dl>
			<dt><label><input type="checkbox" class="inp_check" />통계/랭킹</label><span></span></dt>
			<dd>
				<ul>
					<li><label><input type="checkbox" class="inp_check" />회원관리1</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리2</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리3</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리4</label></li>
					<li><label><input type="checkbox" class="inp_check" />회원관리5</label></li>
				</ul>
			</dd>
		</dl>
	</div>

</div>
<!-- //popup -->

</body>
</html>