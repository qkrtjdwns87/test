<!doctype html>
<head>
<title> CIRCUS </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel="stylesheet" type="text/css" href="/css/app/common.css">
<link rel="stylesheet" type="text/css" href="/css/app/main.css">
<script type="text/javascript" src="/js/app/jquery-1.9.1.js"></script>
<script type="text/javascript" src="/js/loading.js"></script>
<script type="text/javascript">
var loading;
$(document).ready(function(){
	$(document).on('click', '.faq_list dt', function(){
		$(".faq_list dd").hide();
		$(".faq_list dt").addClass("faq_off");
		$(".faq_list dt").removeClass("faq_on");
		if(!$(this).next().is(":visible"))
		{
			$(this).addClass("faq_on");
			$(this).removeClass("faq_off");
			$(this).next().show();
		}
	});
	
	var $select = $('#select');
	var $list = $('.faq_list');
	var categoryData;
	var listData;
	var currentIndex = -1;	
	var currentCategory;
	var url = 'faq.json';
	var currentPage = 1;
	var totalPage;
	var viewPost;
	
	init();
	function init(){
		$select.on('change', changedSelect);
		$(window).on('scroll', onScroll);
		
		loading = new Loading();		
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
			if(data.list.length){
				
				listData = pushData(data.list);
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
				categoryData = pushData(data.category);
				renderSelect(categoryData);
				var selectedIndex = indexOfCategory(currentCategory);
				$select[0].selectedIndex = selectedIndex;
			}
			
			viewPost = data.viewPost;
			totalPage = Math.ceil(data.totalPost/data.viewPost);
			
			currentIndex = -1;
			listData = pushData(data.list);
			renderList(listData);
		}, {
			page:currentPage,
			category:currentCategory
		});
	}
	
	function renderList(data, isAdd){
		var html = '';
		for(var i=0; i<data.length; i++){			
			var index = (i+(currentPage-1)*viewPost);
			var first = index == 0 ? 'first' : '';
			html += '<li data-index="'+index+'">';
			html += '<dl class="'+first+'">';
			html += '<dt class="faq_on">';
			html += '<p class="title">'+data[i].title+'</p>';
			html += '<span class="arrow"></span>';
			html += '</dt>';
			html += '<dd>'+data[i].content+'</dd>';
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
		var html = '';
		for(var i=0; i<data.length; i++){
			html += '<option value="'+data[i].parameter+'">'+data[i].title+'</option>';
		}
		$select.html(html);
	}
	
	function loadAjax(url, success, params){
		$.ajax({
			url:url,
	        dataType:'json',
	        data:params,
	        type:'POST',
	        success:success
		});
	}
	
	function indexOfCategory(category){
		for(var i=0; i<categoryData.length; i++){
			if(categoryData[i].parameter == category){
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
</script>
</head>
<body>
<div id="wrap">
	
	<section id="section_faq">
		<div class="faq_title">
			<select name="" id="select">
				<!-- load data option
				<option value="">Craft Shop운영1</option>
				<option value="">Craft Shop운영2</option>
				<option value="">Craft Shop운영3</option>
				-->
			</select>
			<p>분류선택</p>
		</div>
		
		<ul class="faq_list">
			<!-- load data list
			<li>
				<dl class="first">
					<dt class="faq_on">
						<p class="title">[서비스이용] 미성년자는 사용할 수 없나요?</p>
						<span class="arrow"></span>
					</dt>
					<dd>미성년자도 사용하실 수 있습니다. 단, 보호자의 동의가 필요한 서비스가 일부 있으므로,…</dd>
				</dl>
			</li>

			<li>
				<dl>
					<dt>
						<p class="title">[결제] 30만원 이상 결제가 되나요? </p>
						<span class="arrow"></span>
					</dt>
					<dd>30만원 이상은 카드결제만 가능합니다.</dd>
				</dl>
			</li>

			<li>
				<dl>
					<dt>
						<p class="title">[결제] 30만원 이상 결제가 되나요? </p>
						<span class="arrow"></span>
					</dt>
					<dd>30만원 이상은 카드결제만 가능합니다.</dd>
				</dl>
			</li>-->
		</ul>
	</section>
</div>
</body>
</html>