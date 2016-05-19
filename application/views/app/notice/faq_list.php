<?
	defined('BASEPATH') OR exit('No direct script access allowed');
?>
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/header.php"; ?>
	<link rel="stylesheet" type="text/css" href="/css/app/main.css">
	<script type="text/javascript" src="/js/loading.js"></script>
	<script type="text/javascript">
	    $(document).ready(function () {
	    	$(document).on('click', '.faq_list dt', function(){
	    		$(".faq_list dd").slideUp();
	    		$(".faq_list dt").addClass("faq_off");
	    		$(".faq_list dt").removeClass("faq_on");
	    		if(!$(this).next().is(":visible"))
	    		{
	    			$(this).addClass("faq_on");
	    			$(this).removeClass("faq_off");
	    			$(this).next().slideDown();
	    		}
	    	});
	    	
	    	var $select = $('#boardcate');
	    	var $list = $('.faq_list');
	    	var categoryData;
	    	var listData;
	    	var currentIndex = -1;	
	    	var currentCategory;
	    	var url = '/app/board_a/list/setno/9130/format/json';
	    	var currentPage = 1;
	    	var totalPage;
	    	var viewPost;
	    	
	    	init();
	    	function init(){
	    		$select.on('change', changedSelect);
	    		$(window).on('scroll', onScroll);
	    		
	    		var source = ['/images/app/common/loading_spinner.gif'];
	    		loading = new Loading(source);		
	    		loadList();
	    	}
	    	
	    	function changedSelect(e){
	    		currentCategory = $(this).val();
	    		loadList();
	    	}
	    	
	    	function onScroll(e){
	    		var scrollTop = $(this).scrollTop();
	    		var docHeight = $(document).height();
	    		var winHeight = $(window).height();
	    		if(docHeight-winHeight-10 <= scrollTop){
	    			addList();
	    		}
	    	}
	    	
	    	function addList(){
	    		loading.show();
	    		$(window).off('scroll', onScroll);
	    		currentPage++;
	    		loadAjax(url, function(data){
	    			loading.hide();
	    			$(window).on('scroll', onScroll);
	    			if(data.recordSet.length){
	    				
	    				listData = pushData(data.recordSet);
	    				renderList(listData, true);
	    			}
	    		}, {
	    			page:currentPage,
	    			category:currentCategory
	    		});
	    	}
	    	
	    	function loadList(){
	    		currentPage = 1;
	    		loadAjax(url, function(data){
	    			if(!categoryData){
	    				categoryData = pushData(data.faqCateCdSet);
	    				renderSelect(categoryData);
	    				var selectedIndex = indexOfCategory(currentCategory);
	    				$select[0].selectedIndex = selectedIndex;
	    			}
	    			
	    			viewPost = data.listCount;
	    			totalPage = Math.ceil(data.rsTotalCount/data.viewPost);
	    			
	    			currentIndex = -1;
	    			listData = pushData(data.recordSet);
	    			renderList(listData);
	    		}, {
	    			page:currentPage,
	    			boardcate:currentCategory
	    		});
	    	}
	    	
	    	function renderList(data, isAdd){
	    		var html = '';
	    		for(var i=0; i<data.length; i++){			
	    			var index = (i+(currentPage-1)*viewPost);
	    			var first = index == 0 ? ' class="first"' : '';
	    			var faqon = index == 0 ? ' class="faq_on"' : '';
	    			html += '<li data-index="'+index+'">';
	    			html += '<dl'+first+'>';
	    			html += '<dt'+faqon+'>';
	    			html += '<p class="title">'+data[i].TITLE+'</p>';
	    			html += '<span class="arrow"></span>';
	    			html += '</dt>';
	    			html += '<dd>'+data[i].CONTENT+'</dd>';
	    			html += '</dl>';
	    			html += '</li>';
	    		}
	    		if(isAdd){
	    			$list.append(html);
	    		}else{
	    			$list.html(html);
	    		}
	    	}
	    	
	    	function renderSelect(data){
	    		var html = '<option value="">전체</option>';
	    		for(var i=0; i<data.length; i++){
	    			html += '<option value="'+data[i].NUM+'">'+data[i].TITLE+'</option>';
	    		}
	    		$select.html(html);
	    	}
	    	
	    	function loadAjax(url, success, params){
	    		$.ajax({
	    			url:url + '/page/' + currentPage,
	    	        dataType:'json',
	    	        data:params,
	    	        type:'POST',
	    	        success:success
	    		});
	    	}
	    	
	    	function indexOfCategory(category){
	    		for(var i=0; i<categoryData.length; i++){
	    			if(categoryData[i].NUM == category){
	    				return i;
	    			}
	    		}
	    		return 0;
	    	}
	    	
	    	function pushData(data){
	    		var rData = [];
	    		for(var i=0; i<data.length; i++){
	    			rData.push(data[i]);
	    		}
	    		return rData;
	    	}
	    });	

		function search(){
			document.srcfrm.submit();
		}
	</script>
</head>
<body>	
<div id="wrap">
	
	<section id="section_faq">
		<form name="srcfrm" method="post">
		<div class="faq_title">
			<select id="boardcate" name="boardcate">
	
			</select>
			<p>분류선택</p>
		</div>
		</form>
		
		<ul class="faq_list">

		</ul>
	</section>

</div>
	
<? include $_SERVER["DOCUMENT_ROOT"]."/inc/app/footer.php"; ?>
</body>
</html>			