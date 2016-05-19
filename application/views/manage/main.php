<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/header.php"; ?>
	<script src="/js/jquery.base64.min.js"></script>	
	<script type="text/javascript">
		$(function() {

		});

	</script>
<!-- container -->
<div id="container">
	<div id="content">
		
		<div class="title">
			<span class="main_tit">- <strong><?=$sessionData['user_name']?></strong> 님이 로그인하셨습니다.</span>
			<div class="location"><?=date('Y-m-d H:i:s')?></div>
		</div>
	<?
		if ($isAdmin)
		{
	?>		
		<table class="write2">
			<colgroup><!-- <col width="10%" /><col width="10%" /> --><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2"><a href="/manage/message_m/list">미확인 메시지 <img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th>
					<th colspan="2"><a href="/manage/user_m/list">회원현황 <img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th>
					<th colspan="2"><a href="/manage/shop_m/list">Craft Shop 현황 <img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th>
					<th colspan="2"><a href="/manage/item_m/list">Item 현황<img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th>
					<!-- 1:1문의는 없음 <th colspan="2"><a href="">1:1문의 <img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th> -->
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Craft Shop</td>
					<td>고객</td>
					<td>누적회원수</td>
					<td>신규가입</td>
					<td>누적</td>
					<td>신규승인신청</td>
					<td>누적</td>
					<td>신규승인신청</td>
					<!-- 
					<td>누적</td>
					<td>미확인</td>
					 -->
				</tr>
				<tr>
					<td class="red bold"><?=number_format($m_msgSet['NO_READ_SHOP_CNT'])?></td>
					<td class="red bold"><?=number_format($m_msgSet['NO_READ_USER_CNT'])?></td>
					<td class="red bold"><?=number_format($m_userSet['USER_CNT'])?></td>
					<td class="red bold"><?=number_format($m_userSet['JOIN_USER_CNT'])?></td>
					<td class="red bold"><?=number_format($m_shopSet['SHOP_CNT'])?></td>
					<td class="red bold"><?=number_format($m_shopSet['REQ_APPR_SHOP_CNT'])?></td>
					<td class="red bold"><?=number_format($m_itemSet['ITEM_CNT'])?></td>
					<td class="red bold"><?=number_format($m_itemSet['TODAY_REQ_APPR_ITEM_CNT'])?></td>
					<!-- 
					<td class="red bold"><?=number_format($m_msgSet['QNA_CNT'])?></td>
					<td class="red bold"><?=number_format($m_msgSet['NO_READ_QNA_CNT'])?></td>
					 -->
				</tr>
			</tbody>
		</table>
	<?
		}
		else 
		{
	?>
		<table class="write2">
			<colgroup><col width="10%" /><col width="10%" /><col width="10%" /><col width="10%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2"><a href="/manage/message_m/listshop">미확인 메시지 <img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th>
					<th colspan="2"><a href="/manage/item_m/list">Item 현황<img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></a></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Circus</td>
					<td>고객</td>
					<td>누적</td>
					<td>신규승인신청</td>
				</tr>
				<tr>
					<td class="red bold"><?=number_format($m_msgSet['NO_READ_SHOP_CNT'])?></td>
					<td class="red bold"><?=number_format($m_msgSet['NO_READ_USER_CNT'])?></td>
					<td class="red bold"><?=number_format($m_itemSet['ITEM_CNT'])?></td>
					<td class="red bold"><?=number_format($m_itemSet['TODAY_REQ_APPR_ITEM_CNT'])?></td>
				</tr>
			</tbody>
		</table>	
	<?
		}
	?>
		<div class="sub_title bold mg_t10">[오늘의 주문현황]</div>
		<table class="write2">
			<colgroup><col width="12.5%" /><col width="12.5%" /><col width="12.5%" /><col width="12.5%" /><col width="12.5%" /><col width="12.5%" /><col width="12.5%" /><col width="12.5%" /></colgroup>
			<thead>
				<tr>
					<th colspan="2">처리지연 현황 <a href="#" class="tooltip" 
					data-tooltip="처리지연 현황 도움말
					- 처리지연이란 주문상태가 1주일(7일)이상 변동되지 않은 주문상태를 말합니다.
					- 각각의 처리지연 목록을 확인하신 후 처리 부탁드립니다."><img src="/images/adm/icn_q.png" alt="물음표" class="icn_q" /></a></th>
					<th colspan="2">오늘의 할일</th>
					<th colspan="2">오늘 처리한 일</th>
					<th colspan="2">Item 현황</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>주문 미확인 &gt;</td>
					<td class="bold"><a href="/manage/order_m/list" class="red"><span class="underline"><?=number_format($m_ordSet['DELAY_ORD_CHECK_CNT'])?></span></a>건</td>
					<td>주문확인 &gt;</td>
					<td class="bold"><a href="/manage/order_m/list" class="red"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_CNT'])?></span></a>건</td>
					<td>신규 주문확인 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_CHECK_CNT'])?></span>건</td>
					<td>승인 요청 &gt;</td>
					<td class="bold"><a href="/manage/item_m/apprlist" class="red"><span class="underline"><?=number_format($m_itemSet['REQ_APPR_ITEM_CNT'])?></span></a>건</td>
				</tr>
				<tr>
					<td>배송정보 미등록 &gt;</td>
					<td class="bold"><a href="/manage/order_m/deliverylist" class="red"><span class="underline"><?=number_format($m_ordSet['DELAY_ORD_DELIVERY_CNT'])?></span></a>건</td>
					<td>배송정보 등록대기중 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['ORD_DELIVERY_STANDBY_CNT'])?></span>건</td>
					<td>배송정보 등록 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_DELIVERY_CNT'])?></span>건</td>
					<td>판매중 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_itemSet['ING_ITEM_CNT'])?></span>건</td>
				</tr>				
				<tr>
					<td>취소 지연 &gt;</td>
					<td class="bold"><a href="/manage/order_m/cancellist" class="red"><span class="underline"><?=number_format($m_ordSet['DELAY_ORD_DELIVERY_CNT'])?></span></a>건</td>
					<td>취소신청 처리대기중 &gt;</td>
					<td class="bold"><a href="/manage/order_m/cancellist" class="red"><span class="underline"><?=number_format($m_ordSet['ORD_CANCEL_STANDBY_CNT'])?></span></a>건</td>
					<td>배송 완료 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_DELIVERY_FIN_CNT'])?></span>건</td>					
					<td>판매중지 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_itemSet['STOP_ITEM_CNT'])?></span>건</td>
				</tr>				
				<tr>
					<td>환불 지연 &gt;</td>
					<td class="bold"><a href="/manage/order_m/refundlist" class="red"><span class="underline"><?=number_format($m_ordSet['DELAY_ORD_REFUND_CNT'])?></span></a>건</td>
					<td>환불신청 처리대기중 &gt;</td>
					<td class="bold"><a href="/manage/order_m/refundlist" class="red"><span class="underline"><?=number_format($m_ordSet['ORD_REFUND_STANDBY_CNT'])?></span></a>건</td>
					<td>취소 불가 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_CANCEL_DENY_CNT'])?></span>건</td>
					<td>품절 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_itemSet['SOLDOUT_ITEM_CNT'])?></span>건</td>
				</tr>
				<tr>
					<td>교환 지연 &gt;</td>
					<td class="bold"><a href="/manage/order_m/exchangelist" class="red"><span class="underline"><?=number_format($m_ordSet['DELAY_ORD_REFUND_CNT'])?></span></a>건</td>
					<td>환불완료 처리대기중 &gt;</td>
					<td class="bold"><a href="/manage/order_m/refundlist" class="red"><span class="underline"><?=number_format($m_ordSet['ORD_REFUND_STANDBY_FIN_CNT'])?></span></a>건</td>
					<td>취소 완료 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_CANCEL_CNT'])?></span>건</td>
					<td></td>
					<td class="bold"></td>
				</tr>	
				<tr>
					<td></td>
					<td class="bold"></td>
					<td></td>
					<td class="bold"></td>
					<td>환불 불가 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_REFUND_DENY_CNT'])?></span>건</td>
					<td></td>
					<td class="bold"></td>
				</tr>	
				<tr>
					<td></td>
					<td class="bold"></td>
					<td></td>
					<td class="bold"></td>
					<td>환불 완료 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_REFUND_FIN_CNT'])?></span>건</td>
					<td></td>
					<td class="bold"></td>
				</tr>
				<tr>
					<td></td>
					<td class="bold"></td>
					<td></td>
					<td class="bold"></td>
					<td>결제 취소 &gt;</td>
					<td class="bold"><span class="underline"><?=number_format($m_ordSet['TODAY_ORD_CANCEL_FIN_CNT'])?></span>건</td>
					<td></td>
					<td class="bold"></td>
				</tr>																						
			</tbody>
		</table>

		<!-- 정산 통계 구축뒤 완료
		<div class="sub_title bold mg_t10">[매출현황]</div>
		<table class="write2">
			<colgroup><col width="25%" /><col width="25%" /><col width="25%" /><col width="25%" /></colgroup>
			<thead>
				<tr>
					<th>구분</th>
					<th>오늘</th>
					<th>이번 달</th>
					<th>바로가기</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>총 주문금액(건수)</td>
					<td><span class="red bold">205,500</span>원(6건)</td>
					<td><span class="bold">205,500</span>원(6건)</td>
					<td><a href="" class="btn1">전체주문현황</a></td>
				</tr>
				<tr>
					<td>총 실결제금액(건수)</td>
					<td><span class="red bold">205,500</span>원(6건)</td>
					<td><span class="bold">205,500</span>원(6건)</td>
					<td><a href="" class="btn1">입금/결제관리</a></td>
				</tr>
				<tr>
					<td>총 환불금액(건수)</td>
					<td><span class="red bold">205,500</span>원(6건)</td>
					<td><span class="bold">205,500</span>원(6건)</td>
					<td><a href="" class="btn1">환불관리</a></td>
				</tr>
			</tbody>
		</table>
		 -->
	<?
		if ($userLevelType == 'SHOP')
		{
	?>
		<div class="sub_title bold mg_t10">Craft Shop 공지사항 <img src="/images/adm/icn_sq.png" class="icn_sq" alt="" /></div>
		<ul class="main_list">
		<?
			$i = count($notiSet['recordSet']);
			foreach ($notiSet['recordSet'] as $rs)
			{
				$url = '/manage/board_m/view/setno/'.$rs['SET_NUM'].'/bno/'.$rs['NUM']
		?>
			<li>
				<a href="<?=$url?>">
				<span class="num"><?=$i?></span>
				<span class="txt"><?=$rs['TITLE']?></span>
				<span class="day"><?=$rs['CREATE_DATE']?></span>
				</a>
			</li>
		<?
				$i--;
			}
		?>
		</ul>

		<!-- paging -->
		<!-- 
		<div class="pagination">
			<a href="#" class="prev"><img src="/images/adm/btn_paging_prev.gif" alt="전페이지로" /></a>
			<a href="#"><span class="on">1</span></a>
			<a href="#"><span>2</span></a>
			<a href="#"><span>3</span></a>
			<a href="#"><span>4</span></a>
			<a href="#"><span>5</span></a>
			<a href="#"><span>6</span></a>
			<a href="#"><span>7</span></a>
			<a href="#"><span>8</span></a>
			<a href="#"><span>9</span></a>
			<a href="#"><span>10</span></a>
			<a href="#" class="next"><img src="/images/adm/btn_paging_next.gif" alt="다음으로" /></a>
		</div>
		 -->
		<!--// paging -->
	<?
		}
	?>
	</div>
</div>
<!--// container -->
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/adm/footer.php"; ?>
</body>
</html>			