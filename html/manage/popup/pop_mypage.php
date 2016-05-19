<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CIRCUS ADMIN</title>
<script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>
<link rel="stylesheet" type="text/css" href="/css/adm/common.css" />
<link rel="stylesheet" type="text/css" href="/css/adm/style.css" />
</head>
<body>
<!-- header -->
<div id="header">
	<h1><a href="index.html"><img src="..//images/adm/logo.png" alt="IRCUS ADMIN" /></a></h1>
	<div class="lnb">
		<p class="name"><span>admin</span> 님 안녕하세요!</p>
		<a href="" class="btn1">CIRCUS 바로가기</a>
		<a href="" class="btn1">Log out</a>
	</div>

	<div id="gnb">
		<ul class="gnb">
			<li><a href="">회원관리</a>
				<ul class="sub">
					<li><a href="">회원관리</a></li>
					<li><a href="">전체회원현황</a></li>
					<li><a href="">탈퇴관리</a></li>
				</ul>
			</li>
			<li><a href="">Craft Shop 관리</a>
				<ul class="sub">
					<li><a href="">Craft Shop 관리</a></li>
					<li><a href="">전체Shop현황</a></li>
					<li><a href="">승인대기현황</a></li>
					<li><a href="">승인보류/거부현황</a></li>
					<li><a href="">신규신청</a></li>
					<li><a href="">공지사항</a></li>
					<li><a href=""> 1:1문의</a></li>
				</ul>
			</li>
			<li><a href="">Item 관리</a>
				<ul class="sub">
					<li><a href="">Item 관리</a></li>
					<li><a href="">전체Item현황</a></li>
					<li><a href="">승인대기현황</a></li>
					<li><a href="">승인보류/거부현황</a></li>
					<li><a href="">기획전관리</a></li>
					<li><a href="">카테고리관리</a></li>
				</ul>
			</li>
			<li><a href="">주문관리</a>
				<ul class="sub">
					<li><a href="">주문관리</a></li>
					<li><a href="">전체 주문현황</a></li>
					<li><a href="">입금/결제관리</a></li>
					<li><a href="">취소관리</a></li>
					<li><a href="">환불관리</a></li>
					<li><a href="">교환관리</a></li>
					<li><a href="">배송관리</a></li>
					<li><a href="">거래증빙관리</a></li>
				</ul>
			</li>
			<li><a href="">정산관리</a>
				<ul class="sub">
					<li><a href="">정산관리</a></li>
					<li><a href="">매출내역</a></li>
					<li><a href="">정산현황</a></li>
					<li><a href="">지급대기</a></li>
					<li><a href="">지급완료</a></li>
				</ul>
			</li>
			<li><a href="">게시물관리</a></li>
			<li><a href="">앱관리</a></li>
			<li><a href="">시스템관리</a></li>
			<li><a href="">마케팅관리</a></li>
			<li><a href="">통계/랭킹</a></li>
		</ul>
	</div>
</div>
<!--// header -->

<!-- container -->
<div id="container">
	<div id="content">

		<div class="title">
			<h2>[내 정보관리]</h2>
			<div class="location">Home &gt; 내 정보관리</div>
		</div>
		
		<div class="sub_title">
			<span class="important">*</span>은 필수 입력사항입니다.
		</div>

		<table class="write1">
			<colgroup><col width="15%" /></colgroup>
			<tbody>
				<tr>
					<th><span class="important">*</span>관리자구분</th>
					<td>마스터 <a href="" class="btn2">권한 확인</a></td>
				</tr>
				<tr>
					<th><span class="important">*</span>계정(이메일)</th>
					<td>sung@gmail.com</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호</th>
					<td><input type="password" class="inp_sty20" maxlength="12" /> <span class="tdline ex">*영문+숫자의 조합으로 8~12자로 입력</span></td>
				</tr>
				<tr>
					<th><span class="important">*</span>비밀번호 확인</th>
					<td><input type="password" class="inp_sty20" maxlength="12" /></td>
				</tr>
				<tr>
					<th><span class="important">*</span>이름</th>
					<td>조성철</td>
				</tr>
				<tr>
					<th><span class="important">*</span>소속</th>
					<td><input type="text" class="inp_sty40" /></td>
				</tr>
				<tr>
					<th rowspan="2"><span class="important">*</span>휴대폰<br /><span class="ex tdline">(1개 이상 필수입력)</span></th>
					<td>
						<span class="tdline">사무실</span>
						<select id="" class="inp_select">
							<option value="" selected="selected">02</option>
							<option value=""></option>
						</select><span class="tdline">-</span>
						<input type="text" class="inp_sty5" maxlength="4" /><span class="tdline">-</span><input type="text" class="inp_sty5" maxlength="4" />
						<span class="mg_l10">내선 <input type="text" class="inp_sty5" maxlength="4" /></span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="tdline">휴대폰</span>
						<select id="" class="inp_select">
							<option value="" selected="selected">010</option>
							<option value=""></option>
						</select><span class="tdline">-</span>
						<input type="text" class="inp_sty5" maxlength="4" /><span class="tdline">-</span><input type="text" class="inp_sty5" maxlength="4" />
					</td>
				</tr>
			</tbody>
		</table>
		
		<div class="btn_list">
			<a href="" class="btn3">저장</a>
			<a href="" class="btn1">목록</a>
		</div>

	</div>
</div>
<!--// container -->

<!-- footer -->
<div id="footer">
	<div class="footer">
		<p>Copyright© 2016 CIRCUS. All Right Reserved.</p>
	</div>
</div>
<!--// footer -->
</body>
</html>