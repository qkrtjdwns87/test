/**
 * @author ares
 */
var circus = {
	initLogin:function(){
		var checkboxPassword = document.getElementById('login_check1');
		var checkboxAutoLogin = document.getElementById('login_check2');
		
		checkboxPassword.addEventListener('change', function(e){
			if(this.checked){
				$('#userpw').attr('type', 'text');
			}else{
				$('#userpw').attr('type', 'password');
			}
		});		
	},
	initFaq:function(){
		var self = this;
		var url = "./json/faq.json";
		var currentPage = 1;
		//self.loadAjax(url, loadedData, {page:currentPage, limit:20});
		
		$("#section_notice_list").on('click', 'dt', function(){
			slideUp();
			if(!$(this).next().is(":visible"))
			{
				$(this).find("span.arrow").addClass('on');
				$(this).next().slideDown();
			}
		});
		$(window).on('scroll', onScroll);
		
		function slideUp(){
			$("#section_notice_list dl").each(function(i){
				if($(this).find('.arrow').hasClass('on')){
					$(this).find('dd').slideUp();
					$(this).find(".arrow").removeClass('on');
				}
			});
		}
		
		function onScroll(e){
			var scrollTop = $(this).scrollTop();
			var docHeight = $(document).height();
			var winHeight = $(window).height();
			if(docHeight-winHeight-10 <= scrollTop){
				$(window).off('scroll');
				self.loadAjax(url, addData, {page:++currentPage, limit:20});								
			}
		}
		
		function addData(data){
			if(!data.data.length == 0){
				renderList(data.data, true);
				$(window).on('scroll', onScroll);
			}
		}
		
		function loadedData(data){
			renderList(data.data);
		}
		
		function renderList(data, isAdd){
			var i = 0;
			var len = data.length;
			var html = '';
			for(i; i<len; i++){
				var title = data[i].title;
				var content = data[i].content;
				var date = data[i].date;								
				html += '<dl>';
				html += '<dt>';
				html += '<p class="title">'+title+'</p>';
				html += '<p class="time">'+date+'</p>';
				html += '<span class="arrow"></span>';
				html += '</dt>';
				html += '<dd>'+content+'</dd>';
				html += '</dl>';
			}
			if(isAdd){
				$('#section_notice_list').append(html);
			}else{
				$('#section_notice_list').html(html);
			}			
		}
	},
	initItemView: function(){
		var self = this;
		var url = "../json/item_view.json";
		var commentTraceWriteUrl = "../json/write_comment_trace.json";
	    //self.loadAjax(url, successData, {});
	    
		/*
	    $('.btn_write').on('click', function(e){
	    	var val = $('.inp_write').val();
	    	if(val.length == 0){
	    		alert('댓글을 입력하세요.');	    		
	    	}else{
	    		self.loadAjax(commentTraceWriteUrl, successWrite, {comment:val});
	    	}
	    	return false;
	    });
	    */
	    
	    $('.inp_write').on('input', function(e){
	    	var textarea = $(this)[0];
	    	var borderH = (textarea.offsetHeight - textarea.clientHeight) / 2;
	    	
 			var line = Math.ceil( (textarea.scrollHeight - borderH) / ((textarea.clientHeight - borderH) / textarea.rows) );
 			//console.log(textarea.scrollHeight)
	    	$(this).css('height', line*100);
	    });
	    
	    /*$('.explanation .btn_more').on('click', function(e){
	    	if($(this).data('isOpen')){
	    		$('.explanation dd').css('height', 165);
	    		$(this).data('isOpen', false);
	    		$(this).css('transform', 'rotate(00deg)');
	    	}else{
	    		$('.explanation dd').css('height', $('.explanation .content').height()+41);
	    		$(this).data('isOpen', true);
	    		$(this).css('transform', 'rotate(180deg)');
	    	}
	    	return false;
	    });
		*/
	    
	    function successWrite(data){
	    	//console.log(data.result);
	    }
	    
	    function successData(data){	    	
	    	renderInfo(data.info);
	    	renderShop(data.craft_shop);
	    	renderRecommendItem(data.recommend_item);
	    	renderCommentBuy(data.comment_buy);
	    	renderCommentTrace(data.comment_trace);
	    }
	    
	    function renderInfo(data){
			var i = 0;
			var len = data.option.length;
			var html = '';
			for(i; i<len; i++){
				html += '<dd>';
				html += '<span class="tit">'+data.option[i].title+' :</span>';
				html += '<span class="cont">'+data.option[i].content+'</span>';
			}
			$('.option').html(html);			
			$('.explanation .content').html(data.explanation);			
	    }
	    
	    function renderShop(data){
	    	var thumbnail = data.thumbnail;
	    	var title = data.title;
	    	var author = data.author;
	    	var content = data.content;
	    	var popularity = data.popularity == 'false' ? 'hidden' : '';
	    	var today_author = data.today_author == 'false' ? 'hidden' : '';
	    	
	    	$('#item_view_shop .craft_thumbnail').attr('src', thumbnail);
	    	$('#item_view_shop .title').text(title);
	    	$('#item_view_shop .name').text('작가 ' + author);
	    	$('#item_view_shop .text').text(content);
	    	$('#item_view_shop .popularity').addClass(popularity);
	    	$('#item_view_shop .today_author').addClass(today_author);
	    	
	    	var i = 0;
			var len = data.item_list.length;
			var html = '';
			for(i; i<len; i++){
				var list = $('#item_view_shop .thumbnail li').eq(i);
				var thumbnail = data.item_list[i].thumbnail;
				var item_code = data.item_list[i].item_code;
				list.find('img').attr('src', thumbnail);
				list.find('a').attr('href', 'url?item_code='+item_code);
			}
	    }
	    
	    function renderRecommendItem(data){
	    	var total = data.item_list.length;
	    	var viewNum = 4;
	    	var len = Math.ceil(total/viewNum);
	    	var html = '';
	    	var buttonHtml = '';
	    	var index = 0;
	    	for(var i=0; i<len; i++){
	    		html += '<li class="swiper-slide">';
	    		html += '<ul class="product_type1">';
	    		for(var j=0; j<viewNum; j++){
	    			if(index == total) break;
	    			var item_code = data.item_list[index].item_code;
	    			var thumbnail = data.item_list[index].thumbnail;
	    			var flag = data.item_list[index].flag == 'true' ? 'on' : '';
	    			var sale = data.item_list[index].sale == 'true' ? 'sale' : '';
	    			var title = data.item_list[index].title;
	    			var shop = data.item_list[index].shop;
	    			html += '<li>';
	    			html += '<a href="url?item_code='+item_code+'">';
	    			html += '<span class="'+sale+'"></span>';
	    			html += '<img src="'+thumbnail+'" width="488" height="330" class="img_box" />';
	    			html += '<span class="flag '+flag+'"></span>';
	    			html += '<p class="name">'+title+'</p>';
	    			html += '<p class="shop">'+shop+'</p>';
	    			html += '</a>';
	    			html += '</li>';
	    			index++;
	    		}
	    		html += '</ul>';
	    		html += '</li>';
	    		
	    		var on = i == 0 ? 'on' : '';
	    		buttonHtml += '<button type="button" class="btn_page '+on+'">paging</button>';
	    	}
	    	$('#item_view_more .swiper-wrapper').html(html);
	    	$('#item_view_more .btn_area2').html(buttonHtml);
	    	
	    	//swiper
			var swiper = new Swiper('.top-swiper-container', {
				loop: false,
				onSlideChangeStart:function(swiper){
					var index = self.getActiveSwiperIndex($('.top-swiper-container .swiper-slide'));
					self.activeBtn($('.top-btn .btn_page'), index);
				}
		    });
		    $('.btn_page').on('click', function(e){
		    	var index = $(this).index();
		    	swiper.slideTo(index);
		    });
	    }
	    
	    function renderCommentBuy(data){
	    	var total = data.total;
	    	var listData = data.comment_list;
	    	var moreText = '구매후기 ('+total+')';
	    	$('#item_view_comment_buy .btn_more').html(moreText);
	    	
	    	if(total != 0 || listData.length != 0){	    		
	    		var html = '';	    		
	    		for(var i=0; i<listData.length; i++){
	    			var thumbnail = listData[i].thumbnail || '../images/main/photo.jpg';
	    			var user_id = listData[i].user_id;
	    			var content = listData[i].content;
	    			var star_count = listData[i].star_count;
	    			var delete_comment = listData[i].delete_comment;
	    			console.log(i);
	    			if(delete_comment == 'true'){
	    				html += '<li class="reple_del">';
	    				html += '<img src="../images/main/icn_reple_del.png" alt="" /> 관리자에 의해 삭제된 흔적입니다.';
	    				html += '</li>';
	    			}else{
	    				html += '<li>';
		    			html += '<dl>';
		    			html += '<dt><span class="name">'+user_id+'</span><span class="star star'+star_count+'"></span></dt>';
		    			html += '<dd class="img"><img src="'+thumbnail+'" width="100" height="100" alt="" /></dd>';
		    			html += '<dd class="text"><span>'+content+'</span></dd>';
		    			html += '</dl>';
		    			html += '</li>';
	    			}
	    		}
	    		$('#item_view_comment_buy ul').html(html);
	    	}
	    }
	    
	    function renderCommentTrace(data){
	    	var total = data.total;
	    	var listData = data.comment_list;
	    	var moreText = '한줄 흔적 남기기 ('+total+')';
	    	$('#item_view_comment .btn_more').html(moreText);
	    	if(total != 0 || listData.length != 0){	    		
	    		var html = '';	    		
	    		for(var i=0; i<listData.length; i++){
	    			var thumbnail = listData[i].thumbnail || '../images/main/photo.jpg';
	    			var user_id = listData[i].user_id;
	    			var content = listData[i].content;
	    			var delete_btn = listData[i].delete_btn;
	    			var date = listData[i].date;
	    			var isAdmin = listData[i].isAdmin;
	    			var delete_comment = listData[i].delete_comment;
	    			
	    			if(delete_comment == 'true'){
	    				html += '<li class="reple_del">';
	    				html += '<img src="../images/main/icn_reple_del.png" alt="" /> 관리자에 의해 삭제된 흔적입니다.';
	    				html += '</li>';
	    			}else{
	    				var delBtnStr = delete_btn == "true" ? '<a href="" class="btn_del"><img src="../images/main/bestitem/btn_del.png" alt="삭제" /></a>' : '';
		    			var admin = isAdmin == "true" ? "reple" : '';
		    			
		    			html += '<li class="'+admin+'">';
		    			html += '<dl>';
		    			html += '<dt><span class="name">'+user_id+'</span><span class="time">'+date+'</span></dt>';
		    			html += '<dd class="img"><img src="'+thumbnail+'" width="100" height="100" alt="" /></dd>';
		    			html += '<dd class="text"><span>'+content+'</span>' +delBtnStr+'</dd>';
		    			html += '</dl>';
		    			html += '</li>';
	    			}
	    		}
	    		$('#item_view_comment ul').html(html);
	    	}
	    }
	},
	getActiveSwiperIndex:function($list){
		for(var i=0; $list.size(); i++){
    		var $item = $list.eq(i);
    		if($item.hasClass('swiper-slide-active')){
    			return $item.index();
    		}
    	}
    	return -1;
	},
	activeBtn:function($btnList, index){
		$btnList.removeClass('on');
	    $btnList.eq(index).addClass('on');
	},
	loadAjax:function(url, success, params){
		$.ajax({
            url:url,
            dataType:'json',
            data:params,
            type:'POST',
            success:function(data){
                success(data);
            }
       });
	}
};


/*
function layer_open(str){

	$.ajax({
		url: "layer.html?step="+str,
		type: "GET",
		async: false,
		success: function (html) {
			$('#layer').html(html);
			$('#'+str).show();
		},
		//error : function(data){alert("잠시후에 시도하여 주세요.");}
	});
}

*/