<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/cart_order.css">
</head>
<body>
<div id="wrap_zip" style="display:none;border:1px solid;width:100%;height:300px;margin:5px 0;position:relative">
<!-- <img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode()" alt="접기 버튼"> -->
</div>
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
  	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
	<script type="text/javascript">
	    // 우편번호 찾기 찾기 화면을 넣을 element
	    var element_wrap = document.getElementById('wrap_zip');

	    function foldDaumPostcode() {
	        // iframe을 넣은 element를 안보이게 한다.
	        element_wrap.style.display = 'none';
	        
	        app_closeWindow();  
	    }

	    function searchAddressLayerAppend(zip, addr1, addr2, o_addr){
	        // 현재 scroll 위치를 저장해놓는다.
	        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

	        new daum.Postcode({
	            oncomplete: function(data) {
	                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

	                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
	                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
	                var fullAddr = data.address; // 최종 주소 변수
	                var fullRoadAddr = data.roadAddress; // 도로명 주소 변수            
	                var extraAddr = ''; // 조합형 주소 변수

	                // 기본 주소가 도로명 타입일때 조합한다.
	                if(data.addressType === 'R'){
	                    //법정동명이 있을 경우 추가한다.
	                    if(data.bname !== ''){
	                        extraAddr += data.bname;
	                    }
	                    // 건물명이 있을 경우 추가한다.
	                    if(data.buildingName !== ''){
	                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
	                    }
	                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
	                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
	                }

	                // 우편번호와 주소 정보를 해당 필드에 넣는다.
	                //document.getElementById('sample3_postcode').value = data.zonecode; //5자리 새우편번호 사용
	                //document.getElementById('sample3_address').value = fullAddr;
	                //document.getElementById(zip).value = data.zonecode; //5자리 새우편번호 사용
	                //document.getElementById(addr1).value = fullRoadAddr;
	                //document.getElementById(o_addr).value = data.jibunAddress;
	                
	                // 아이폰에 데이터 전달을 하기 위해

			        var IOSframe = document.createElement('iframe');
			        IOSframe.style.display = 'none';
			        IOSframe.src = 'jscall://searchAddrResult/' + data.zonecode + '/' + fullRoadAddr + '/' + data.jibunAddress;
			        document.documentElement.appendChild(IOSframe);	
			        
			        IOSframe.src = 'jscall://closeWindow';
	        
	                _android.searchAddrResult(data.zonecode, fullRoadAddr, data.jibunAddress);
	                app_closeWindow();            

	                // iframe을 넣은 element를 안보이게 한다.
	                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
	                //element_wrap.style.display = 'none';

	                // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
	                //document.body.scrollTop = currentScroll;
	            },
	            // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
	            onresize : function(size) {
	                element_wrap.style.height = size.height+'px';
	            },
	            width : '100%',
	            height : '100%'
	        }).embed(element_wrap, {autoClose:false});

	        // iframe을 넣은 element를 보이게 한다.
	        element_wrap.style.display = 'block';
	    }	    
    	
	    $(document).ready(function () {
		    //지연시켜주지 않으면 기능엔 문제없으나 무한로딩 상태표시가 되는 문제가 있음
		    //아마도 페이지 로딩되면서 다음주소api와 통신해야되는 내용이 있는데 이를 무시하고
		    //바로 열어버리면 통신이 미처 완료되기전에 검색창이 열리면서 완료를 위해서 무한 콜하고 있는 상태로 보임
	    	setTimeout("searchAddressLayerAppend('rcvr_zipx','rcvr_add1','rcvr_add2','param_opt_1');",300);
	    });
	</script>

<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>		