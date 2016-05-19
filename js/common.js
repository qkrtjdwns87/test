$(function() {
    $.ajaxSetup({
        //cache: true,
    	//data: {
        //    csrf_test_name: getCookie('csrf_cookie_name')
        //}, //csrf 사용시
        async: true,
        beforeSend: function () {
            //$(".loading_dimmed").show();
            //$(".loading_bar").show();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(xhr.status);
            console.log(xhr.statusText);
            console.log(xhr.responseText);
            console.log(thrownError);
        },
        complete: function () {
            //$(".loading_dimmed").hide();
            //$(".loading_bar").hide();
        }
    }); 

});

/*
 * 로그인 체크가 필요한 경우
 * 해당 페이지 isLogin 이 설정되어 있어야 한다
 * jquery.base64.min.js 인클루드
 */
function loginCheck(){
    if (!isLogin){
	    alert('로그인 후 이용하실 수 있습니다.');
	    //var url = '/app/user_a/login/return_url/' + $.base64.encode(location.pathname + location.search);				
		//location.href = url;
	    app_showMenuWindow('로그인', '/app/user_a/login');
		return false;			    
    }else{
	    return true;
    }
}

/*
 * return_url 로 페이지 전환시
 */
function backRedirect(){
    var url=$('#return_url').val();
    if (url == ''){
	    url='/';
    }else{
    	url=$.base64.decode(url);
    }
    location.href=url;
}	

function webFlaging(type, no, highno){
    if (!loginCheck()) return;
	flaging(type, no, highno, false);
}

function appFlaging(type, no, highno){
	flaging(type, no, highno, true);
}

/* 
 * type : shop, item
 * isflag : 
 * no : unique number
 * highno : upper unique number
 */
function flaging(fromtype, uniqno, highno, isApp){
	var result;
    var ajaxUrl;
    var isFlagResult = false;
    var param = '?fromtype='+fromtype+'&uniqno='+uniqno+'&highno='+highno;
    if (fromtype == 'item'){
    	ajaxUrl = '/app/item_a/flag';
    }else if(fromtype == 'shop'){
    	ajaxUrl = '/app/shop_a/flag';	
    }

    $.ajax({
        url: ajaxUrl+param,
        //data: {'fromtype':fromtype,'uniqno':uniqno,'highno':highno},        
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json',
        success: function (data) {
            if (data.result == -1){
            	alert('인증되지 않은 사용자입니다.');
            }else{
            	result = Boolean(data.result);
            }
            //data 플래그 처리된 결과 (1:flag, 0:unflag)  
            if (isApp){
            	return data.result;
            }else{
            	flagingEnd(fromtype, result, uniqno, highno);            
            }
        }
    });

    //location.href='/app/item_a/flag?fromtype='+fromtype+'&uniqno='+uniqno+'&highno='+highno;
}

function flagingEnd(type, isflag, uniqno, highno){
    if (isflag){
    	$('#'+type+'_'+uniqno).addClass('on');			    
    }else{
    	$('#'+type+'_'+uniqno).removeClass('on');
    }
}

function openNewsWin(openUrl) {
    var winObj;
    winObj = window.open(openUrl, "sendNewsWin", "width=800, height=600, scrollbars=yes");

    //var openNewWindow = window.open("about:blank");
    //openNewWindow.location.href = openUrl;
}

function IsEmail(strEmail) {
    if (strEmail.length > 0 && strEmail.search(/(\S+)@(\S+)\.(\S+)/) == -1) {
        return false;
    } else {
        return true;
    }
}

function trim(str) {
    if (str == undefined) {
        return "";
    }
    else {
        return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    }
}

function IsNumber(str) {
    /*
	var patt=/[0-9]/;
	return patt.test(str);
	*/
    var i;
    var ch;
    var isNumeric = true;
    for (i = 0; i < str.length; i++) {
        ch = str.charAt(i);
        if (!((ch >= '0') && (ch <= '9')))
            isNumeric = false;
    }
    return isNumeric; // true, false 반환
}

function IsKor(str) {
    /*
    var patt = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/;
    return patt.test(str);
	*/
    var i;
    var ch;
    var patt = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/;
    var IsKor = true;
    for (i = 0; i < str.length; i++) {
        ch = str.charAt(i);
        if (!patt.test(ch)) {
            IsKor = false;
            break;
        }
    }
    return IsKor; // true, false 반환
}

function IsEngNumber(str){
    var i;
    var ch;
    var patt =  /^[A-Za-z0-9+]*$/; 
    var IsEngNum = true;
    for (i = 0; i < str.length; i++) {
        ch = str.charAt(i);
        if (!patt.test(ch)) {
        	IsEngNum = false;
            break;
        }
    }
    return IsEngNum; // true, false 반환	
}

function nl2br(str){  
    return str.replace(/\n/g, "<br />");  
}  

//문자열 태그 제거
//i태그와 b태그는 제외할 경우  allowed = '<i><b>';
function strip_tags(input, allowed) {
    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}

function setCookie(name, value, expiredays) {
    var todayDate = new Date();
    todayDate.setDate(todayDate.getDate() + expiredays);
    todayDate.setHours(0);
    todayDate.setMinutes(0);
    todayDate.setSeconds(0);
    document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";"
}

function getCookie(name) {
    var nameOfCookie = name + "=";
    var x = 0;
    while (x <= document.cookie.length) {
        var y = (x + nameOfCookie.length);
        if (document.cookie.substring(x, y) == nameOfCookie) {
            if ((endOfCookie = document.cookie.indexOf(";", y)) == -1) endOfCookie = document.cookie.length;
            return unescape(document.cookie.substring(y, endOfCookie));
        }
        x = document.cookie.indexOf(" ", x) + 1;
        if (x == 0) break;
    }
    return "";
}

function closeLayerDiv(pobj) {
    var layPop, dimmed = $('.dimmed').eq(0);
    layPop = $(pobj);
    $(layPop).hide();
    dimmed.fadeOut('fast');
    $('body,html').css("overflow", "visible"); //화면 스크롤 이용
    //if (myScroll != null) myScroll.refresh();
    //$('body').css("height", "auto");
    //$('body').css("overflow", "visible"); //화면 스크롤 이용
}

/* name이 일치하는 element 처리 */
function AllCheckBoxCheck(element, id) {
    if($("#"+id).prop("checked")){
        //input태그의 name이 chk인 태그들을 찾아서 checked옵션을 true로 정의
        $("input[name="+element+"]").prop("checked",true);
        //클릭이 안되있으면
    }else{
        //input태그의 name이 chk인 태그들을 찾아서 checked옵션을 false로 정의
        $("input[name="+element+"]").prop("checked",false);
    }
} 

/* id 시작하는 element 처리 */
function AllCheckBoxCheck2(element, id) {
    if($("#"+id).prop("checked")){
        //input태그의 name이 chk인 태그들을 찾아서 checked옵션을 true로 정의
        $("input[id^="+element+"]").prop("checked",true);
        //클릭이 안되있으면
    }else{
        //input태그의 name이 chk인 태그들을 찾아서 checked옵션을 false로 정의
        $("input[id^="+element+"]").prop("checked",false);
    }
} 

/* name이 일치하는 element 처리 */
function getCheckboxSelectedValue(elementName) {
    var chk = $("input[name=" + elementName + "]");
    var selectedValues = "";
    var i = 0;
    $("input[name=" + elementName + "]").each(
        function () {
            if (this.checked) {
                selectedValues += this.value + ",";
            }
        }
    );

    if (selectedValues.length > 0) {
        selectedValues = selectedValues.substr(0, selectedValues.length - 1);
    }
    return selectedValues;
}

/* id 시작하는 element 처리 */
function getCheckboxSelectedValue2(elementName) {
    var chk = $("input[id^=" + elementName + "]");
    var selectedValues = "";
    var i = 0;
    $("input[id^=" + elementName + "]").each(
        function () {
            if (this.checked) {
                selectedValues += this.value + ",";
            }
        }
    );

    if (selectedValues.length > 0) {
        selectedValues = selectedValues.substr(0, selectedValues.length - 1);
    }
    return selectedValues;
}

function fillZeros(n, digits) {
    var zero = '';
    n = n.toString();

    if (n.length < digits) {
        for (i = 0; i < digits - n.length; i++)
            zero += '0';
    }
    return zero + n;
}

function getNowTimeStamp(getType) {
    var d = new Date();
    var s;
    if (getType == "day") {
        s = fillZeros(d.getFullYear(), 4) + '-' +
                fillZeros(d.getMonth() + 1, 2) + '-' +
                fillZeros(d.getDate(), 2);
    } else {
        s = fillZeros(d.getFullYear(), 4) + '-' +
                fillZeros(d.getMonth() + 1, 2) + '-' +
                fillZeros(d.getDate(), 2) + ' ' +

                fillZeros(d.getHours(), 2) + ':' +
                fillZeros(d.getMinutes(), 2) + ':' +
                fillZeros(d.getSeconds(), 2);
    }
    return s;
}

var timerID;

function showCountdown(timeToExpiration) {
    remain = timeToExpiration - 1;
    if (remain >= -1) {
        day = Math.floor(timeToExpiration / (3600 * 24));
        mod = timeToExpiration % (24 * 3600);
        hour = Math.floor(mod / 3600);
        mod = mod % 3600;
        min = Math.floor(mod / 60);
        sec = mod % 60;
        count = " " + min + "분 " + sec + "초";
        $("#timeDisp").text(count);

        if (remain == -1) {
            //$("#timeCk").val("Y");
            alert("인증번호 입력제한 시간연장은 3분이내에 해야 합니다.\n다시 인증번호 받기버튼을 선택해 주십시오!");
        } else {
            timerID = setTimeout("showCountdown(remain)", 1000);
        }
    }
}

function showCountdownReset(timeToExpiration) {

    if ($("#timeCk").val() == "0") {
        $("#timeCk").val("1");
        clearTimeout(timerID);
        showCountdown(timeToExpiration);
    }
    else if ($("#timeCk").val() == "1") {
        $("#timeCk").val("2");
        clearTimeout(timerID);
        showCountdown(timeToExpiration);
    }
    else if ($("#timeCk").val() == "2") {
        $("#timeCk").val("3");
        clearTimeout(timerID);
        showCountdown(timeToExpiration);
    }
    else if ($("#timeCk").val() == "3") {
        alert("인증번호 입력제한 시간연장은 3회까지만 할 수 있습니다.");
    }
    //else if ($("#timeCk").val() == "Y") {
    //    alert("인증번호 입력제한 시간연장은 3분이내에 해야 합니다.\n다시 인증번호 받기버튼을 선택해 주십시오!");
    //}

}

/* 사용예
<tr>
<td style="text-align:center; font:11px dotum; vertical-align:middle; color:#656565; padding-top:1px">
(남은시간<label for="countdown"><input name="InputTime" type="text" id="countdown" style="background-color:#ffffff; border:none; width:55px; font:bold 11px dotum; vertical-align:0; text-align:center; color:#4d4d4d;" maxlength="6" readonly="readonly" title="남은시간"/></label>
<script type="text/javascript">
<!--
showCountdown_kmcis(180);
// -->
</script>)</td>
<td><a href="JavaScript:showCountdownReset_kmcis(180);"  title="인증번호 입력 제한시간 연장"><img src="https://img.kmcert.com/kmcis/comm/images/img/pop/btn_time2.gif" alt="인증번호 입력 제한시간 연장" /></a></td>
</tr>
*/

function setComma(n) {
    var reg = /(^[+-]?\d+)(\d{3})/;
    n += '';
    while (reg.test(n))
        n = n.replace(reg, '$1' + ',' + '$2');

    return n;
}

function sendTopSelect(element) {
    //$('#form1').attr('action', element.value);
    //$('#form1').attr('action', element);
    //$('#form1').submit();
    location.href = element;
}

function charValueCheck(val, minLength, maxLength, type) {
    var pattern;
    if (val.length < minLength) {
        return "minlength";
    }

    if (val.length > maxLength) {
        return "maxlength";
    }
    
    var SamePass_0 = 0; //동일문자 카운트
    var SamePass_1 = 0; //연속성(+) 카운드
    var SamePass_2 = 0; //연속성(-) 카운드

    var chr_pass_0;
    var chr_pass_1;

    for (var i = 0; i < val.length; i++) {
        chr_pass_0 = val.charAt(i);
        chr_pass_1 = val.charAt(i + 1);

        //동일문자 카운트
        if (chr_pass_0 == chr_pass_1) {
            SamePass_0 = SamePass_0 + 1;

            if (SamePass_0 > 1) {
                return "samechar";
                break;
            }
        } else {
            SamePass_0 = 0;
        }

        var chr_pass_2 = val.charAt(i + 2);

        //연속성(+) 카운드
        if (chr_pass_0.charCodeAt(0) - chr_pass_1.charCodeAt(0) == 1 && chr_pass_1.charCodeAt(0) - chr_pass_2.charCodeAt(0) == 1) {
            SamePass_1 = SamePass_1 + 1;
        }

        //연속성(-) 카운드
        if (chr_pass_0.charCodeAt(0) - chr_pass_1.charCodeAt(0) == -1 && chr_pass_1.charCodeAt(0) - chr_pass_2.charCodeAt(0) == -1) {
            SamePass_2 = SamePass_2 + 1;
        }
    }

    if (SamePass_0 > 1) {
        return "samechar";
    }

    if (SamePass_1 > 0 || SamePass_2 > 0) {
        return "contchar";
    }    

    if (type == "id") {
        pattern = /^[a-zA-Z0-9]+$/;
        if (!pattern.test(val)) {
            return "regexcept"; //숫자 혹은 영문자만 입력가능 (234234, adfnvf, 1dk3ois)
        }
    } else {
        pattern = /^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[~,!,@,#,$,*,(,),=,+,_,.,|]).*$/;
        if (!pattern.test(val)) {   //숫자+특수문자+영문자 조합 을 하지 않은 경우
            return "reg";
        }      

        pattern = /^.*(?=.*\d)(?=.*[a-zA-Z])(?=.*[~,!,@,#,$,*,(,),=,+,_,.,|]).*$/;
        if (!pattern.test(val)) {   //숫자+특수문자+영문자 조합 을 하지 않은 경우
            var dCheck = 0;

            pattern = /^.*(?=.*\d)(?=.*[a-zA-Z]).*$/;
            if (!pattern.test(val)) {   //영문자+숫자 조합 을 하지 않은 경우
                dCheck++;
            }

            pattern = /^.*(?=.*\d)(?=.*[~,!,@,#,$,*,(,),=,+,_,.,|]).*$/;
            if (!pattern.test(val)) {   //숫자+특수문자 조합 을 하지 않은 경우
                dCheck++;
            }

            pattern = /^.*(?=.*[a-zA-Z])(?=.*[~,!,@,#,$,*,(,),=,+,_,.,|]).*$/;
            if (!pattern.test(val)) {   //영문자+특수문자 조합 을 하지 않은 경우
                dCheck++;
            }

            if (dCheck > 2) { //2개이상의 조합이 전혀 없는 경우
                return "reg2";
            }
        }
    }

    if (type == "id" || type == "passwd") {
        pattern = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/;
        if (pattern.test(val)) {   //한글이 한개라도 있는 경우
            return "han";
        }
    }
}

function passSafetyCheck(){
    var password = "";
    var score = 100;
    password = $("#passwd1").val();

    var pwCheck = charValueCheck(password, 8, 20, "passwd");
    if (pwCheck == "minlength") {
    	score = score-50;
    }

    if (pwCheck == "contchar") {
    	score = score-50;
        //alert("비밀번호는 연속된 문자열을 3개 이상(abc,123) 쓰실수 없습니다.");
    }    
                
    if (pwCheck == "samechar") {
    	score = score-50;
        //alert("비밀번호는 동일문자를 3번이상(aaa,111) 사용하실수 없습니다.");
    }

    if (pwCheck == "reg") { //숫자+특수문자+영문자 조합 을 하지 않은 경우
    	score = score-20;
    }

    if (pwCheck == "reg2") { //2개이상의 조합이 전혀 없는 경우
    	score = score-20;
    }            

    if (score > 80){
        $('.safety').addClass('on');
        $('.normal').removeClass('on');
        $('.danger').removeClass('on');
    }else if (score > 50 && score <= 80){
        $('.safety').removeClass('on');
        $('.normal').addClass('on');
        $('.danger').removeClass('on');                
    }else{
        $('.safety').removeClass('on');
        $('.normal').removeClass('on');
        $('.danger').addClass('on');                
    }
}

//object show
function alertObj(obj) {
	var str = "";
	for(key in obj) {
		str += key+"="+obj[key]+"\n";
	}
	alert(str);
	return;
}

function dateCal(month, elem1, elem2){
	var d = new Date();	
	var todate = d.getFullYear() + '-' + ((d.getMonth() + 1)<10 ? '0' : '') + (d.getMonth() + 1) + '-' + (d.getDate()<10 ? '0' : '') + d.getDate();	
	
	if (month == 0){
		result = todate;
	}else{
		d.setMonth( d.getMonth( ) - month ); 
		result = d.getFullYear() + '-' + ((d.getMonth() + 1)<10 ? '0' : '') + (d.getMonth() + 1) + '-' + (d.getDate()<10 ? '0' : '') + d.getDate();
	}

	$('#'+elem1).val(result);
	$('#'+elem2).val(todate);
}

function searchAddress(zip, addr1, addr2, o_addr){
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 도로명 조합형 주소 변수

            // 법정동명이 있을 경우 추가한다. (법정리는 제외)
            // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
            if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if(data.buildingName !== '' && data.apartment === 'Y'){
               extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }
            // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
            if(fullRoadAddr !== ''){
                fullRoadAddr += extraRoadAddr;
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById(zip).value = data.zonecode; //5자리 새우편번호 사용
            document.getElementById(addr1).value = fullRoadAddr;
            document.getElementById(o_addr).value = data.jibunAddress;
            window.setTimeout(function () { 
                document.getElementById(addr2).focus(); 
            }, 0); 

            /*
            사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
            if(data.autoRoadAddress) {
                예상되는 도로명 주소에 조합형 주소를 추가한다.
                var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
                document.getElementById('guide').innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';

            } else if(data.autoJibunAddress) {
                var expJibunAddr = data.autoJibunAddress;
                document.getElementById('guide').innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';

            } else {
                document.getElementById('guide').innerHTML = '';
            }
            */
        }
    }).open();			
}

function closeDaumPostcode() {
    // iframe을 넣은 element를 안보이게 한다.
    daum_layer.style.display = 'none';
}	

function searchAddressLayer(zip, addr1, addr2, o_addr){
    new daum.Postcode({
        oncomplete: function(data) {
            // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 도로명 조합형 주소 변수

            // 법정동명이 있을 경우 추가한다. (법정리는 제외)
            // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
            if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if(data.buildingName !== '' && data.apartment === 'Y'){
               extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }
            // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
            if(fullRoadAddr !== ''){
                fullRoadAddr += extraRoadAddr;
            }
            
            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById(zip).value = data.zonecode; //5자리 새우편번호 사용
            document.getElementById(addr1).value = fullRoadAddr;
            document.getElementById(o_addr).value = data.jibunAddress;

            // iframe을 넣은 element를 안보이게 한다.
            // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
            daum_layer.style.display = 'none';
        },
        width : '100%',
        height : '100%'
    }).embed(daum_layer);

    // iframe을 넣은 element를 보이게 한다.
    daum_layer.style.display = 'block';

    // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
    initLayerPosition();		
}

// 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
// resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
// 직접 daum_layer의 top,left값을 수정해 주시면 됩니다.
function initLayerPosition(){
    var width = 300; //우편번호서비스가 들어갈 element의 width
    var height = 460; //우편번호서비스가 들어갈 element의 height
    var borderWidth = 5; //샘플에서 사용하는 border의 두께

    // 위에서 선언한 값들을 실제 element에 넣는다.
    daum_layer.style.width = width + 'px';
    daum_layer.style.height = height + 'px';
    daum_layer.style.border = borderWidth + 'px solid';
    // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
    daum_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
    daum_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
}

function addrAppendSeting(zip, addr1, jibun){
    document.getElementById('rcvr_zipx').value = zip;
    document.getElementById('rcvr_add1').value = addr1;
    document.getElementById('param_opt_1').value = jibun;
}

function paging(url, pg){
	var redirUrl = url;
	if (pg > 1){
		redirUrl += '/page/' + pg;
	}
	location.href = redirUrl;
}

/* (Layer pop) 관련 시작*/
function layerPopClose(){
	$('#layer_pop',top.document).hide();	
	$('#popfrm',top.document).attr('src', '');
}

/*
 * totype : 발송대상
 */
function messageSend(totxt, tono, totype){
	var url;
	if (totype == 'shop'){
		url = '/manage/message_m/writeshopformpop?senduserno='+tono+'&sendusertxt='+totxt;		
	}else if (totype == 'user'){
		url = '/manage/message_m/writeuserformpop?senduserno='+tono+'&sendusertxt='+totxt;		
	}
	$('#popfrm').attr('src', url);
	$('#layer_pop').show();
}

function smsSend(totxt, phone){
	var url;
	url = '/manage/message_m/smsformpop?sendusertxt='+totxt+'&sendphone='+phone;		
	$('#popfrm').attr('src', url);
	$('#layer_pop').show();
}
/* (Layer pop) 관련 끝*/

/* Search (Layer pop) 관련 시작*/
function userSearch(){
	$('#popfrm').attr('src', '/manage/search_m/user');
	$('#layer_pop').show();
}

function managerChangeSearch(){
	$('#popfrm').attr('src', '/manage/search_m/manager');
	$('#layer_pop').show();
}	

function shopHistorySearch(num, spState){
	$('#popfrm').attr('src', '/manage/search_m/shophistory/schno/'+num+'?shopstate='+spState);
	$('#layer_pop').show();			
}

function itemHistorySearch(num, itState){
	$('#popfrm').attr('src', '/manage/search_m/itemhistory/schno/'+num+'?itemstate='+itState);
	$('#layer_pop').show();			
}

function itemSearch(){
	$('#popfrm').attr('src', '/manage/search_m/item');
	$('#layer_pop').show();
}

function itemRankSearch(){
	$('#popfrm').attr('src', '/manage/search_m/itemrank');
	$('#layer_pop').show();
}

function shopSearch(){
	$('#popfrm').attr('src', '/manage/search_m/shop');
	$('#layer_pop').show();	
}

function storySearch(){
	$('#popfrm').attr('src', '/manage/search_m/story');
	$('#layer_pop').show();	
}

function orderSearch(num, searchtype){
	$('#popfrm').attr('src', '/manage/search_m/orderlist/schno/'+num+'?searchtype='+searchtype);
	$('#layer_pop').show();
}

function orderDetailList(num, method){
	$('#popfrm').attr('src', '/manage/order_m/'+method+'/ordno/'+num);
	$('#layer_pop').show();	
}

function setShopManagerSearch(){
	
}
function passwordChange(num){
    $('#popfrm').attr('src', '/manage/user_m/passwordchangepop/uno/'+num);
    $('#layer_pop').show();
}
/* Search (Layer pop) 관련 끝*/

/* Search (Layer small pop) 관련 시작*/
function test(){
	$('#layer_pop_s').show();	
}

function cancelRequest(orderstate, ordnum, ordptnum){
	var url='/manage/order_m/cancelreqform/ordno/'+ordnum+'/ordptno/'+ordptnum+'?orderstate='+orderstate;
	$('#popfrm_s').attr('src', url);
	$('#layer_pop_s').show();	
}

function denyRequest(orderstate, ordnum, ordptnum){
	var url='/manage/order_m/denyreqform/ordno/'+ordnum+'/ordptno/'+ordptnum+'?orderstate='+orderstate;
	$('#popfrm_s').attr('src', url);
	$('#layer_pop_s').show();	
}

function deliveryInfoRequest(orderstate, ordnum, ordptnum){
	var url='/manage/order_m/deliverywriteform/ordno/'+ordnum+'/ordptno/'+ordptnum+'?orderstate='+orderstate;
	$('#popfrm_s').attr('src', url);
	$('#layer_pop_s').show();	
}
/* Search (Layer small pop) 관련 끝*/

/* SNS Share 시작*/
function snsShare(snsType) {
    if (snsImgUrl != "" && snsImgUrl != undefined) {
        //파일명이 한글일때 이미지 공유가 안되는 문제 처리
        var arrFilenm = snsImgUrl.split("\\");
        var filenm = arrFilenm[arrFilenm.length - 1];
        snsImgUrl = snsImgUrl.replace(filenm, "") + encodeURI(filenm);
    }

	switch (snsType) {
	case "facebook":
		facebookShare();
		break;
	case "twitter":
		twitterShare();
		break;
	case "kakao":
		kakaoShare();
		break;
	case "kakaostory":
		kakaoStoryShare();
		break;
	case "insta":
		instaShare();
		break; 
	case 'line':
		lineShare();  			
	default:
		break;
	}
}		

function facebookShare() {
	FB.init({
		appId      : fbAppId,
		status     : true,
		xfbml      : true,
		cookie	   : true,
		version    : 'v2.4' // or v2.0, v2.1, v2.2, v2.3
	});

	FB.ui({
		 method: 'stream.publish',
		 name: snsTitle,
		 link: snsLink,
		 redirect_uri: snsDomain,
		 picture: snsImgUrl,
		 caption: snsDomain,
		 description: snsMsg,
		 message: snsMsg
	 },
	 function(response) {
		 alertObj(response);							   
		 if (response && response.post_id) {
			 //alert('Post was published.');
		 } else {
			 //alert('Post was not published.');
		 }
	});			
}
var kakaoflag=false;
function kakaoinit(){
	if(!kakaoflag){
		  Kakao.init(kakaoKey);
		  kakaoflag=true;
	}
}
function twitterShare(){
    var sendUrl = "http://twitter.com/share?text=" + encodeURIComponent(snsTitle) + "&url=" + escape(snsLink);	//encodeURIComponent(url);
    openNewsWin(sendUrl);			
}

function kakaoShare() {
	 kakaoinit();
    if (snsImgUrl != "" && snsImgUrl != undefined) {
        Kakao.Link.sendTalkLink({
            label: "서커스(Circus) - "+ snsTitle,
            image: {
                src: snsImgUrl,
                width: '300',
                height: '200'
            },
            webLink: {
                text: '서커스(Circus) 이동',
                url: snsLink
            }
        });                
    }else{
        Kakao.Link.sendTalkLink({
            label: snsTitle,
            webLink: {
                text: '서커스(Circus) 이동',
                url: snsLink
            }
        });                
    }
}	

function kakaoStoryShare(){
		 kakaoinit();
		var refreshToken = Kakao.Auth.getRefreshToken();
		if(refreshToken==null){
			Kakao.Auth.login({
				success: function(authObj) {
					kaToken=authObj.access_token;
					Kakao.API.request({
						url: '/v1/user/me',
						success: function(res) {
							var myData = JSON.stringify(res);
							myData = JSON.parse(myData);
							_sns_id=myData.id;
							_sns="ks"; 
							ksShare();
						},
						fail: function(error) {
						  alert(JSON.stringify(error))
						}
					});
				}
			});
		}else{
			ksShare();
		}
 
  

   
}	
function ksShare(){
	Kakao.API.request( {
		url : '/v1/api/story/linkinfo',
		data : {
			url : snsLink 
		}, success: function (data) {	
		}
	}).then(function(res) {
		return Kakao.API.request( {
			url : '/v1/api/story/post/link',
			data : {
				content : snsTitle +" - "+snsMsg ,
				link_info : res
			}, success:function(data) {
				 
				alert(JSON.stringify(data));
				alert('공유되었습니다.');
			}
		});
	}, function (err) {
		alert(JSON.stringify(err));
		alert("공유에 실패하였습니다. 다시 시도해 주세요.");
	})
}
function instaShare(){
  
}  

function lineShare(){
	var sendUrl = 'http://line.me/R/msg/text/?' + encodeURIComponent(snsTitle) + ' ' + encodeURIComponent(snsLink);
	openNewsWin(sendUrl);            
}  
/* SNS Share 끝*/


/* 제품 상세, 한줄 흔적 팝업에서 흔적 남기기 시작*/
function commentDel(no){
	if (!loginCheck()) return;
	if (!confirm('삭제하시겠습니까?')){
		return;
	}
	var currentTotal;
    var ajaxUrl;
    var param = '?comtno='+no;
    ajaxUrl = '/app/item_a/commentdelete/format/json';		    

    $.ajax({
        url: ajaxUrl+param,
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json',
        success: function (data) {
            if (data.result == -1){
            	alert('인증되지 않은 사용자입니다.');
            }else if (data.result > 0){
	            alert('삭제되었습니다');
	            currentTotal = parseInt($('#totalnum span').text());
	            if (currentTotal > 0){
	            	$('#totalnum span').text(setComma(currentTotal - 1))
	            }
	            $('#cindex_'+no).remove();			            
            }else{
	            alert('작성자만 삭제할 수 있습니다.');			            
            }
        }
    });			
}

function sendComment(){
	if (!loginCheck()) return;
	if ($('#itemno').val() == ''){
		alert('아이템을 확인할 수 없습니다.');
		return;
	}

	if (trim($('#brd_content').val()) == ''){
		alert('내용을 입력하세요.');
		return;
	}

	var currentTotal;
    var delBtn;
    var html;
    var ajaxUrl;
    var param = '?itemno='+$('#itemno').val()+'&brd_content='+encodeURI($('#brd_content').val());
    ajaxUrl = '/app/item_a/commentwrite/sno/'+sno+'/sino/'+sino+'/format/json';		    
    
    $.ajax({
        url: ajaxUrl+param,
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json',
        success: function (data) {
        	if (data.result == 0){
        		alert(data.message);
        	}else if (data.result == -1){
            	alert('인증되지 않은 사용자입니다.');
            }else if (data.result > 0){
            	delBtn = '<p class="btn_del"><a href="javascript:commentDel('+data.result+');">삭제</span></a></p>';
				html = '<li data-index="'+data.result+'" id="cindex_'+data.result+'">';
				html += '<div class="img"><img src="'+data.profileImg+'" alt="" /></div>';
				html += '<div class="name"><span>'+data.userEmail.substring(0,3)+'****</span></div>';
				html += '<span class="time">'+data.createDate.substring(0,16)+'</span>';
				html += '<div class="text">'+data.content+delBtn+'</div>';
				html += '</li>';

	            currentTotal = parseInt($('#totalnum span').text());
	            if (currentTotal > 0){
	            	$('#totalnum span').text(setComma(currentTotal + 1))
	            }						
				$('#brd_content').val('');
				$('.item_comment ul').prepend(html);
				alert('등록되었습니다.');           
            }
        }
    });	
}		
/* 제품 상세, 한줄 흔적 팝업에서 흔적 남기기 끝*/

