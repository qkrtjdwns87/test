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
	
	<div class="title">
		<h3>[배송정보 일괄등록]</h3>
	</div>

	<table class="write1">
		<colgroup><col width="15%" /></colgroup>
		<tbody>
			<tr>
				<th>파일로 일괄등록</th>
				<td><input type="file" id="" /></td>
			</tr>
		</tbody>
	</table>
	
	<div class="btn_list ag_c">
		<a href="" class="btn3">등록</a>
	</div>

	<div class="help_tip">
		<dl>
			<dt><img src="..//images/adm/icn_q1.png" alt="느낌표" class="icn_q1" />도움말</dt>
			<dd>- 한 번에 처리할 수 잇는 건수는 500건 이내 입니다. 처리할 데이터가 많을 경우 여러 번 나누어 처리해 주시기 바랍니다.</dd>
			<dd>- 주문번호, 배송업체, 송장번호는 필수입력사항입니다. 엑샐의 첫번째 제목행은 변경 불가입니다.</dd>
			<dd>- 주문의 배송정보가 이미 입력된 경우에는 이전에 입력된 배송정보가 삭제되고 새로운 배송정보로 등록됩니다.</dd>
			<dd>- Item코드가 공란인 경우에는 해당 주문건의 Item 전체에 동일한 배송정보가 입력됩니다. (가장 나중에 입력된 배송정보)</dd>
			<dd>- 배송정보를 정확하게 입력하지 않은 경우에는 배송이 되지 않습니다. 확인 후 정확하게 입력해 주십시오.</dd>
		</dl>
	</div>
	
</div>
<!-- //popup -->

</body>
</html>