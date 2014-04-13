/*Multilevel dropdown menu
/ ====================================================================================
*/
$(document).ready(function(){
	$("ul.simple-drop-down-menu li").each(function () {
		$(this).hoverIntent({
			timeout: 300,
			over: function () {
				var current = $("ul:first", this);
				current.fadeIn(300);
							
			},
			out: function () {
				var current = $("ul:first", this);
				current.fadeOut(300);
			}
		});
	});
	$("ul.simple-drop-down-menu li:has(ul)").find("a:first").append("<span></span>");
	$("ul.simple-drop-down-menu li:has(ul) a").addClass("parent");
	
});

/*Get first Word in heading tag & Peload image in css
/ ====================================================================================
*/
$(document).ready(function(){
	/*Preload image in Css file*/
	$.preloadCssImages();

	/*Get first word*/
	$(".first-word").each(function(){
     var me = $(this);
        me.html(me.html().replace(/^([\w-]+)/, "<strong>$1</strong>"));
	});

	
});

/*Quicksand
/ ====================================================================================
*/
$(document).ready(function(){
	/* Clone applications to get a second collection	*/
	$(".portfolio-content.three-column > div.block").addClass("project");
	/*Add data-id attribute automatically*/
	
	var $data = $(".portfolio-content").clone();
	$('.portfolio-main li.all-projects').addClass("current");
	//NOTE: Only filter on the main portfolio page, not on the subcategory pages
	$('.portfolio-main li').click(function(e) {
		$(".filter li").removeClass("current");	
		
		
		/*Use the last category class as the category to filter by. This means that multiple categories are not supported (yet)*/ 
		var filterClass=$(this).attr('class').split(' ').slice(-1)[0];
		
		if (filterClass == 'all-projects') {
			var $filteredData = $data.find('.project');
		} else {
			var $filteredData = $data.find('.project[data-type=' + filterClass + ']');
		}
		$(".portfolio-content").quicksand($filteredData, {
			
			duration: 800,
			easing: 'swing',
			enhancement: function() {Cufon.refresh();$(".video-preview,.image-preview").mPreviewOverlay();$("*[rel^='prettyPhoto']").prettyPhoto();}
		});
		$(this).addClass("current"); 			
		return false;
		
	});
});

	
/*Blog entry View-Switcher with Cookie
/ ====================================================================================
*/
$(document).ready(function(){
	
	/*Switch view*/
	var cookieName = "active-view",
	cookieOptions = {expires: 7, path: "/"},
	blogEntry = $("#list-blog-entry li"),
	switcherLink = $("#view-switcher li a");
	
	/*Check if a cookie with name of "active-view" exists.*/
	/*if not exists will add default blog entry view*/
	
	switcherLink.css({opacity:.5});
	if( $.cookie("active-view") == null ) {
		$("#view-switcher li a.display-list").css({opacity:1}).parent().addClass("active");
		$("#list-blog-entry").addClass("display-list");
		blogEntry.equalHeights();
	}
	/*if exists , load cookie and use saved view status*/
	else{
		$("#view-switcher li a." + $.cookie(cookieName)).css({opacity:1}).parent("li").addClass("active");
		blogEntry.removeAttr("style");		
		$("#list-blog-entry").removeClass().addClass("list-blog-entry").fadeIn().addClass($.cookie(cookieName));
		blogEntry.equalHeights();
	}
	/*event add cookie and switch display*/
	switcherLink.click(function(){
		blogEntry.removeAttr("style");
		
		switcherLink.css({opacity:.5});
		$(this).css({opacity:1});
		
		/*remove all class active from switcher tab*/
		$("#view-switcher li").removeClass("active");		
		$.cookie(cookieName, $(this).attr("class"), cookieOptions);
		$("." + $.cookie(cookieName)).parent().addClass("active");
		
		
		
		
		/*Fade out current view and fade in with new view*/
		$("#list-blog-entry").fadeOut(function(){
			$(this).removeClass().addClass("list-blog-entry").fadeIn(function(){blogEntry.equalHeights();}).addClass($.cookie(cookieName));
			
		});
		
		/*alert($.cookie("active-view"));*/
	});

});






$(document).ready(function(){
	
	
	/*Fake Smooth preloader
	/ ====================================================================================
	/This must include after quicksand
	*/
	if ( $.browser.webkit || $.browser.mozilla || $.browser.msie && $.browser.version  > 8  ) {
		$("#body-content,#footer-content").maxxSmoothPreloader();
	};
	
	
	
	/*Some Effect
	/ ====================================================================================
	*/
	
	/*Pretty Photo*/
	$("*[rel^='prettyPhoto']").prettyPhoto();
	
	/*preview Overlay*/
	$(".video-preview,.image-preview").mPreviewOverlay();
	
	$(".back-to-top").click(function(){$("html,body").animate({scrollTop:0}, 800)});
	$(".social-network li a img").css({opacity:.5});
	$(".social-network li a").hover(function(){
		$("img",this).stop().animate({
			opacity:1,
			marginTop:'0px'
		},300);	
	},function(){
		$("img",this).stop().animate({
			opacity:.5,
			marginTop:'5px'
		},300);	
	});
	
	/*Valid Form*/
	$("#contact-form").validate();
	
	/*auto align layout
	/ ====================================================================================
	*/
	
	$(".list-blog-entry li:odd").addClass("odd");
	$("#sidebar .ads a:even,.list-blog-entry li:even").addClass("even");
	$(".services.three-column").countThree({className:"last-child-of-line"});
	$("ul.zigzag li:last-child,ul.simple-drop-down-menu ul li:last-child").css({borderBottom:"none"});
	$(".three-column .block:last-child").addClass("last-child");
	
	/*Tooltip
	/ ====================================================================================
	*/
	$(".tipMe").tipTip({maxWidth: "auto", edgeOffset: 5});
	$(".tipMe.tip_left").tipTip({maxWidth: "auto", edgeOffset: 5,defaultPosition:"left"});
	$(".tipMe.tip_right").tipTip({maxWidth: "auto", edgeOffset: 5,defaultPosition:"right"});
	$(".tipMe.tip_top").tipTip({maxWidth: "auto", edgeOffset: 5,defaultPosition:"top"});
	
	
	
	/*Empty text-box
	/ ====================================================================================
	*/
	$("input:text,input:password").css({color:'#222'});
	$("input:text,input:password").emptyTextBox();
	
	/*Scoll to comment
	/ ====================================================================================
	*/
	$(".add-comment").click(function(){
		var offsets = $("#add-comment-form").offset();		
		$("html,body").animate({scrollTop:offsets.top - 80}, 800);
		return false;
	});
	
	/*Nivo slider
	/ ====================================================================================
	*/
	$("#slider").nivoSlider();
	/*Slide button effect*/
	$(".nivo-directionNav a.nivo-nextNav").delay(1000).animate({right:390});
	$(".nivo-directionNav a.nivo-prevNav").delay(1000).animate({left:390});
	$(".nivo-directionNav").hoverIntent({
		timeout: 300,
		over: function () {	$("a.nivo-nextNav").animate({right:355});$("a.nivo-prevNav").animate({left:355});},
		out: function () {	$("a.nivo-nextNav").animate({right:390});$("a.nivo-prevNav").animate({left:390});}
	})
	/*Slide overlay .Remove the below line if you dont want the slice overlay*/
	$(".nivoSlider").find("a.nivo-imageLink").append("<div class='slide-overlay'></div>");	
	
	
	/*Flick Gallery effect
	/ ====================================================================================
	*/
	$(".flick-gallery li a").mPreviewOverlay({opacityOverlay:.3});
	/*Reset marin*/	
	$(".flick-gallery").countThree({className:"reset-margin-right"});
		
	
	/*Equal Heights
	/ ====================================================================================
	*/
	$("ul.display-short li").equalHeights();
	$(".services .block .content").equalHeights();
	$(".entry .block .content").equalHeights();
	
		/*Flick Gallery2 effect
	/ ====================================================================================
	*/
	
});
	