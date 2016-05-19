
/* 일반 경고창 alert과 동일 */
function msgNotice(message, url){
	loginBtnReset();
	var msg = message;
	var btn_cnt = 1;
	var btn1_title = '확인';
	var btn2_title = '';
	var btn1_css = '';
	var btn2_css = '';
	var btn1_url = "javascript:msgClose();";
	var btn2_url = '';
	msgAlert(btn_cnt, msg, btn1_title, btn2_title, btn1_css, btn2_css, btn1_url, btn2_url);		    
}

function loginBtnReset(){
	$('.login_active').removeClass('off');
	$('.login_active').addClass('on');		
	$('.login_ing').removeClass('on');		
	$('.login_ing').addClass('off');		
}

function msgAlert(bCnt, msg, btitle_1, btitle_2, bCss_1, bCss_2, burl_1, burl_2){
	$('#msgtext').html(msg);
	if (bCnt == 1){
		$('#msgbtn_2').hide();
	}
	
	$('#msgbtn_1').text(btitle_1);
	$('#msgbtn_1').attr('href', burl_1);
	if (bCss_1 != '') $('#msgbtn_1').addClass(bCss_1);
	
	$('#msgbtn_2').text(btitle_2);
	$('#msgbtn_2').attr('href', burl_2);
	if (bCss_2 != '') $('#msgbtn_2').addClass(bCss_2);
	
	$('#layer_alert').show();
}

function msgClose(){
	$('#msgbtn_2').show();
	$('#msgtext').html('');
	$('#msgbtn_1').attr('href', '');
	$('#msgbtn_1').removeClass('red');
	$('#msgbtn_2').attr('href', '');
	$('#msgbtn_2').removeClass('red');
	$('#layer_alert').hide();
}

/* 30일후 변경 체크시 */
function passChangeAfter(url){
	setCookie('changeAfterYn', 'Y', 30);
	msgClose();
	location.href = url;
}

/* 30일후 변경 체크했는지 여부 */
function isPassChangeAfter(){
	return getCookie('changeAfterYn');
}

function snsShareOpen(){
	$('#layer_sns').show();
	$('#layer_sns_share').show();
}

/* 메시지 대화창에서 아이템 정보 클릭시 */
function msgItemDetailView(sno, sino){
	location.href = '/app/item_a/view/sno/'+sno+'/sino/'+sino;
}

function app_loginok(redirurl, authkey, autologinyn){

	if (typeof(_android) != 'undefined') {
	
		_android.loginok(authkey, autologinyn);
		_android.closeWindow();
	}else{
		location.href=redirurl;
	}
}

//웹뷰창 닫기
function app_closeWindow(){
	if (typeof(_android) != 'undefined') {
		_android.closeWindow();
	}	
}

//메인으로 가기
function app_moveToHome(){
	if (typeof(_android) != 'undefined') {
		_android.moveToHome();		
	}
}	

//BestItem으로 가기
function app_moveToBestItem(){
	if (typeof(_android) != 'undefined') {
		_android.moveToBestItem();		
	}
}	

//Item & CraftShop 키워드로 검색하기 페이지로 이동
function app_moveToSearch(){
	if (typeof(_android) != 'undefined') {
		_android.moveToSearch();		
	}
}

//아이템 상세로 이동
function app_moveToItemDetail(sno, sino){
	if (typeof(_android) != 'undefined') {
		_android.moveToItemDetail(sno, sino);		
	}
}	

//구매창 넘기기
function app_showPurchaseWindow(){
	if (typeof(_android) != 'undefined') {
		_android.showPurchaseWindow();
	}
}

//팝업창 열기
function app_showPopUpWindow(poptitle, pageurl) {
	if (typeof(_android) != 'undefined') {
		_android.showPopUpWindow(poptitle, pageurl);
	}
}

//페이지 이동
function app_showMenuWindow(poptitle, pageurl) {
	if (typeof(_android) != 'undefined') {
		_android.showMenuWindow(poptitle, pageurl);
	}
}

//샵 flag한 회원리스트 페이지 이동
function app_showShopFlagUserList(sname, sno){
	if (typeof(_android) != 'undefined') {
		_android.showShopFlagUserList(sname, sno);
	}	
}

//구매후기 폼 불러오기(구매한 리스트 선택화면)
function app_moveToPurchaseReview(ordno, ordptno){
	if (typeof(_android) != 'undefined') {
		_android.moveToPurchaseReview(ordno, ordptno);
	}	
}
 
//다른 유저의 flag page
function app_showUserFlagPage(uname, uno, isself){
	if (typeof(_android) != 'undefined') {
		_android.showUserFlagPage(uname, uno, isself);
	}	
}

//주문과 관련된 대화방 열기
function app_showMessageRoom(shopname, sno, ordno){
	if (typeof(_android) != 'undefined') {
		_android.showMessageRoom(shopname, sno, ordno);		
	}
}

//구매페이지에서 주소검색창 호출
function app_showAddressWindow(title, url){
	if (typeof(_android) != 'undefined') {
		_android.showAddressWindow(title, url);		
	}
}

//샵정보 보기
function app_showCraftShopPage(sno){
	if (typeof(_android) != 'undefined') {
		_android.showCraftShopPage(sno);		
	}
}


 