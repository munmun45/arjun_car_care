function addSwitcher()
{
	var currentURL      = window.location.href; 
	var urlLTR = 'https://autocare-html.vercel.app/';
	var urlRTL = 'https://autocare-rtl.vercel.app/';
	if(currentURL.indexOf('https://autocare-rtl.vercel.app/') > -1){
		urlLTR = currentURL.replace('https://autocare-rtl.vercel.app/', 'https://autocare-html.vercel.app/');
		urlRTL = currentURL;
	}else{
		urlRTL = currentURL.replace('https://autocare-html.vercel.app/', 'https://autocare-rtl.vercel.app/');
		urlLTR = currentURL;
	}
	
	var dzSwitcher = '<div id="dzSwitcher-right" class="styleswitcher"> <div class="switcher-btn-bx"><a class="switch-btn closed"> <span class="fa fa-cogs fa-lg"></span></a></div><div class="styleswitcher-inner content-scroll"> <a href="javascript:void(0);" class="dez-page site-button button-sm" onclick="deleteAllCookie();">Delete All Cookie</a> <div class="theme-box"> <h5 class="switcher-title"><span>Theme</span></h5> <h6 class="switcher-title">Standard</h6> <ul val="themeFullColor" class="color-skins-db theme-panel-save"> <li><a class="theme-skin skin-1 theme-color" href="javascript:void(0);" data-color-theme="css/skin/skin-1.css" data-logo-selector=".logo-dark img, .logo-white img, .logo-center .logo-c" data-logo-image="images/logo-black.png, images/logo.png, images/logo-center.png"></a></li><li><a class="theme-skin skin-2 theme-color" href="javascript:void(0);" data-color-theme="css/skin/skin-2.css" data-logo-selector=".logo-dark img, .logo-white img, .logo-center .logo-c, .site-header.dark .sticky-header .logo-dark img" data-logo-image="images/logo-black2.png, images/logo2.png, images/logo-center-2.png, images/logo-white-2.png"></a></li><li><a class="theme-skin skin-3 theme-color" href="javascript:void(0);" data-color-theme="css/skin/skin-3.css" data-logo-selector=".logo-dark img, .logo-white img, .logo-center .logo-c" data-logo-image="images/logo-black3.png, images/logo3.png, images/logo-center-3.png"></a></li><li><a class="theme-skin skin-4 theme-color" href="javascript:void(0);" data-color-theme="css/skin/skin-4.css" data-logo-selector=".logo-dark img, .logo-white img, .logo-center .logo-c" data-logo-image="images/logo-black4.png, images/logo4.png, images/logo-center-4.png"></a></li></ul> </div><div class="theme-box"> <h5 class="switcher-title"><span>Layout</span></h5> <ul val="layout" class="layout layout-view theme-panel-save"> <li class="wide-layout active"><a href="javascript:void(0);" dir="wide-layout">Wide</a></li><li class="boxed"><a href="javascript:void(0);" dir="boxed">Boxed</a></li></ul> </div><div class="theme-box"> <h5 class="switcher-title"><span>Nav</span></h5> <ul val="nav" class="nav-view theme-panel-save"> <li class="light-nav active"><a dir="nav-light" href="javascript:void(0);">Light</a></li><li class="dark-nav"><a dir="nav-dark" href="javascript:void(0);">Dark</a></li></ul> </div><div class="theme-box"> <h5 class="switcher-title"><span>Header</span></h5> <ul val="header" class="header-view theme-panel-save"> <li class="header-fixed active"><a href="javascript:void(0);" dir="sticky-header">Fixed</a></li><li class="header-static"><a href="javascript:void(0);" dir="sticky-no">Static</a></li></ul> </div><div class="theme-box"> <h5 class="switcher-title"><span>Skin View</span></h5> <ul class="skin-view theme-panel-save"> <li class="active"><a href="'+urlLTR+'">LTR</a></li><li class=""><a href="'+urlRTL+'">RTL</a></li></ul> </div><div class="theme-box"> <h5 class="switcher-title"><span>Background</span></h5> <h6 class="switcher-title">Color</h6> <ul val="bgColor" class="bg-color-switcher theme-panel-save"> <li><a class="bg-color-1" href="javascript:void(0);" dir="#ee3131"></a></li><li><a class="bg-color-2" href="javascript:void(0);" dir="#1e69b8"></a></li><li><a class="bg-color-3" href="javascript:void(0);" dir="#f68220"></a></li><li><a class="bg-color-4" href="javascript:void(0);" dir="#efbb20"></a></li></ul> <h6 class="switcher-title">Image</h6> <ul val="bgImage" class="background-switcher theme-panel-save"> <li><img src="images/switcher/switcher-bg/bg1.jpg" dir="images/background/bg1.jpg" alt=""></li><li><img src="images/switcher/switcher-bg/bg2.jpg" dir="images/background/bg2.jpg" alt=""></li><li><img src="images/switcher/switcher-bg/bg3.jpg" dir="images/background/bg3.jpg" alt=""></li><li><img src="images/switcher/switcher-bg/bg4.jpg" dir="images/background/bg4.jpg" alt=""></li></ul> <h6 class="switcher-title">Pattern</h6> <ul val="bgPatten" class="pattern-switcher theme-panel-save"> <li><img src="images/switcher/switcher-patterns/bg1.jpg" dir="images/pattern/pt1.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg2.jpg" dir="images/pattern/pt2.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg3.jpg" dir="images/pattern/pt3.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg4.jpg" dir="images/pattern/pt4.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg5.jpg" dir="images/pattern/pt5.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg6.jpg" dir="images/pattern/pt6.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg7.jpg" dir="images/pattern/pt7.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg8.jpg" dir="images/pattern/pt8.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg9.jpg" dir="images/pattern/pt9.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg10.jpg" dir="images/pattern/pt10.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg11.jpg" dir="images/pattern/pt11.jpg" alt=""></li><li><img src="images/switcher/switcher-patterns/bg12.jpg" dir="images/pattern/pt12.jpg" alt=""></li></ul> </div></div></div>';
	
	if($("#dzSwitcher").length == 0) {
		jQuery('body').append(dzSwitcher);
	}
}


jQuery(window).on('load',function(){
	
	//=== Switcher panal slide function	=====================//
	jQuery('.styleswitcher').animate({
		'left': '-220px'
	});
	jQuery('.styleswitcher-right').animate({
		'right': '-220px',
		'left': 'auto'
	});
	jQuery('.switch-btn').addClass('closed');
	//=== Switcher panal slide function END	=====================//
	
});
	
$(function(){		
	"use strict";
	
	addSwitcher();
	//=== Switcher panal slide function	=====================//
	jQuery('.switch-btn').on('click',function () {	
		if (jQuery(this).hasClass('open')) {
			jQuery(this).addClass('closed');
			jQuery(this).removeClass('open');
			jQuery('.styleswitcher').animate({
				'left': '-220px'
			});
			jQuery('.styleswitcher-right').animate({
				'right': '-220px',
				'left': 'auto'
			});
		} else {
			if (jQuery(this).hasClass('closed')) {
			jQuery(this).addClass('open');
			jQuery(this).removeClass('closed');
			jQuery('.styleswitcher').animate({
				'left': '0'
			});
			jQuery('.styleswitcher-right').animate({
				'right': '0',
				'left': 'auto'
			});
			}
		}	
	});
	//=== Switcher panal slide function END	=====================//

	//=== Color css change function	=====================//
    // Color changer
	
	
	
	//=== Color css change function	=====================//	
	
	//=== Background image change function	=====================//
	jQuery('.background-switcher li img').on('click',function(){
	 var imgbg = jQuery(this).attr('dir');
	 //console.<span id="IL_AD2" class="IL_AD">log</span>(imgbg);
	 jQuery('#bg').css({backgroundImage: "url("+imgbg+")"});
	 });
	//=== Background image change function	End=====================//
	
	//=== Background pattern change function	=====================//
	jQuery('.pattern-switcher li img').on('click',function(){
	 var imgbg = jQuery(this).attr('dir');
	 //console.<span id="IL_AD2" class="IL_AD">log</span>(imgbg);
	 jQuery('#bg').css({backgroundImage: "url("+imgbg+")"});
	 jQuery("#bg").css( "background-size", "auto");
	 
	 });
	//=== Background pattern change function End=====================//
	
	//=== Background pattern change function	=====================//
	jQuery('.bg-color-switcher li a').on('click',function(){
	 var bgcolor = jQuery(this).attr('dir');
	 //alert(bgcolor);
	 //console.<span id="IL_AD2" class="IL_AD">log</span>(bgcolor);
		jQuery('#bg').css('background-color', bgcolor);
		jQuery('#bg').css('background-image', '');
	 });
	//=== Background pattern change function End=====================//
	

	//=== Layout boxed & fullscreen change function	=====================//
	jQuery('.layout-view li ').on('click', function(){
		jQuery('.layout-view li ').removeClass('active');
		jQuery(this).addClass('active');	
	});
	jQuery('.skin-view li ').on('click', function(){
		jQuery('.skin-view li ').removeClass('active');
		jQuery(this).addClass('active');	
	});
	
	jQuery('.wide-layout').on('click',function() { 
		jQuery("body").addClass('wide-layout');
		jQuery("body").removeClass('boxed');
		jQuery("body").removeClass('frame').removeAttr("style");
		jQuery('#ThemeRangeSlider').hide('slow');
	});
	
	jQuery('.boxed').on('click',function() { 
		jQuery("body").addClass('boxed');
		jQuery("body").removeClass('wide-layout');
		jQuery("body").removeClass('frame').removeAttr("style");
		jQuery('#ThemeRangeSlider').hide('slow');
	});
	jQuery('.frame').on('click',function() { 
		jQuery("body").addClass('frame');
		jQuery("body").removeClass('boxed wide-layout');
		jQuery('#ThemeRangeSlider').show('slow');
		//themeFramePadding();
		jQuery(".main-bar-wraper").addClass('sticky-no');
		jQuery(".main-bar-wraper").removeClass('sticky-header');
	});
	themeFramePadding();
	//=== Layout boxed & fullscreen change function	END=====================//

	//=== Nav light & dark change function	END=====================//
	jQuery('.nav-view li ').on('click', function(){
		jQuery('.nav-view li ').removeClass('active');
		jQuery(this).addClass('active');	
	});
	
	jQuery('.light-nav').on('click',function() { 
		jQuery(".header-nav").addClass('nav-light');
		jQuery(".header-nav").removeClass('nav-dark');
	});
	jQuery('.dark-nav').on('click',function() { 
		jQuery(".header-nav").addClass('nav-dark');
		jQuery(".header-nav").removeClass('nav-light');
	});
	
	//=== Nav light & dark change function	END=====================//	
	
	/* Header View */
	jQuery('.header-view li ').on('click', function(){
		jQuery('.header-view li ').removeClass('active');
		jQuery(this).addClass('active');
	});
	
	jQuery('.header-fixed').on('click',function() { 
		jQuery(".main-bar-wraper").addClass('sticky-header');
		jQuery(".main-bar-wraper").removeClass('sticky-no');
	});
	
	jQuery('.header-static').on('click',function() { 
		jQuery(".main-bar-wraper").addClass('sticky-no');
		jQuery(".main-bar-wraper").removeClass('sticky-header');
	});
	/* Header View END */
	
	/* Menubar */
	jQuery('.menubar-view li').on('click', function(){
		jQuery('.menubar-view li').removeClass('active');
		jQuery(this).addClass('active');
	});
	
	jQuery('.menubar-left').on('click',function() { 
		jQuery('.page-wraper').addClass('sidemenu');
		jQuery('.header-nav').addClass('sidenav content-scroll');
		setTimeout(function(){
			Lavi.headerResize();
		}, 1000); 
		Lavi.customScroll();
		Lavi.dzTheme();
	});
	
	jQuery('.menubar-top').on('click',function() { 
		jQuery('.page-wraper').removeClass('sidemenu');
		jQuery('.header-nav').removeClass('sidenav');
		setTimeout(function(){
			Lavi.headerResize();
		}, 1000); 
		
		/* remove content-scroll */
		var logoheaderHTML =  jQuery('<div>').append($('.header-nav .logo-header').clone()).html(); 
		var navbarHTML = jQuery('<div>').append($('.header-nav .navbar-nav').clone()).html(); 
		var footermenuHTML = jQuery('<div>').append($('.header-nav .footer-menu').clone()).html(); 
		jQuery('.content-scroll').removeClass('content-scroll mCustomScrollbar _mCS_1 mCS_no_scrollbar');
		jQuery('.header-nav').empty();
		jQuery('.header-nav').append(logoheaderHTML).append(navbarHTML).append(footermenuHTML);
		/* remove content-scroll END */
	});
	/* Menubar END */
	
	var currentURL      = window.location.href; 
	if(currentURL.indexOf('index.html') > -1){
		jQuery('.menubar-box').remove();
	}
	
});

/* Theme Panel Save */
	
	
	var themeOption = ['themeStandardColor','themeFullColor','layout','nav','header','menubar','bgColor','bgImage','bgPatten'];
	themeOption['layout'] = ['wide','boxed','frame','frame_value','layout_value'];
	themeOption['themeStandardColor'] = ['skin-1','skin-2','skin-3','skin-4','skin-5','skin-6','skin-7','skin-8','skin-9','skin-10','themeStandardColor_value','themeStandardColor_logo'];
	themeOption['themeFullColor'] = ['css/skin/skin-1','css/skin/skin-2','css/skin/skin-3','css/skin/skin-4','css/skin/skin-5','css/skin/skin-6','css/skin/skin-7','css/skin/skin-8','css/skin/skin-9','css/skin/skin-10','themeFullColor_value','themeFullColor_logo'];
	themeOption['nav'] = ['light','dark','nav_value'];
	themeOption['header'] = ['fixed','static','header_value'];
	themeOption['menubar'] = ['left','top','menubar_value'];
	themeOption['bgColor'] = ['#ff5e14','#d2151e','#fdb400','#0086f0','#6a1a1f','#019444','#00c6ff','#e87800','#cc9f5a','#bd5ec6','bgColor_value'];
	themeOption['bgImage'] =['bg1.jpg','bg2.jpg','bg3.jpg','bg4.jpg','bgImage_value'];
	themeOption['bgPatten'] =['pt1.jpg','pt2.jpg','pt3.jpg','pt4.jpg','pt5.jpg','pt6.jpg','pt7.jpg','pt8.jpg','pt9.jpg','pt10.jpg','pt11.jpg','pt12.jpg','bgPatten_value'];

jQuery(window).on('load', function(){
	
	jQuery(document).on('click', '.theme-color', function(){
        jQuery(".skin").attr("href", jQuery(this).data('color-theme'));
		var logoSelectorArr = jQuery(this).data('logo-selector').split(',');
		var logoSrcArr		= jQuery(this).data('logo-image').split(',');
		var arrCount = logoSelectorArr.length;
		for(var i=0; i<arrCount; i++){
			jQuery(logoSelectorArr[i]).attr('src',logoSrcArr[i]);
		}
		return false;
    });
	
	jQuery(document).on('click', '.theme-panel-save li a, .theme-panel-save li img', function() {
			
			
			
			var optionData =  jQuery(this).parent().parent().attr('val');
			if(optionData == 'themeStandardColor' || optionData == 'themeFullColor'){
				var optionValue =  jQuery(this).data('color-theme');
			}else{
				var optionValue =  jQuery(this).attr('dir');
			}
			
			if(optionData == 'bgColor'){
				deleteCookie('bgImage_value');
				deleteCookie('bgPatten_value');
			}else if(optionData == 'bgImage'){
				deleteCookie('bgColor_value');
				deleteCookie('bgPatten_value');
			}else if(optionData == 'bgPatten'){
				deleteCookie('bgColor_value');
				deleteCookie('bgImage_value');
			}else if(optionData == 'themeStandardColor' || optionData == 'themeFullColor'){
				var themeLogoSelector =  jQuery(this).data('logo-selector');
				var themeLogoImage =  jQuery(this).data('logo-image');
				setCookie(optionData+'_logo_selector',themeLogoSelector,1);
				setCookie(optionData+'_logo_image',themeLogoImage,1);
				
				
				
				if(optionData == 'themeStandardColor'){
					deleteCookie('themeFullColor_value');
				}else if(optionData == 'themeFullColor'){
					deleteCookie('themeStandardColor_value');
				}
			}
			
			 
			
			setCookie(optionData+'_value',optionValue,1);
	});
	
	
	setThemePanel();
	
	//deleteAllCookie();
	//alert(document.cookie);
	
	
	
});

function themeFramePadding(){
	var themeOptionFrameValue = getCookie('frame_value');
	if(themeOptionFrameValue == ''){
		themeOptionFrameValue = 20;
	}
	
	if($("#slider-range-3").length > 0 )
	{
		
		$("#slider-range-3").unbind().slider({
				min: 20,
				max: 80,
				//value: 20,
				value: themeOptionFrameValue,
				animate:"slow",
				orientation: "horizontal",
				slide: function( event, ui ) {
					frameSlider = ui.value;
					$('#' + this.id).prev().val(frameSlider+"px");
					$( "#bg" ).css('padding',frameSlider+"px");
					setCookie('frame_value',frameSlider,1);
			   }
		});
	}
}

function setThemePanel(){
	
	var currentLogo = '';
	
	if(jQuery('.site-header.dark').length > 0){
		currentLogo = jQuery('.site-header.dark .logo-dark img').attr('src');
	}
	
	jQuery.each(themeOption, function(index, themeOptionItem) {
		themeOptionItemValue = getCookie(themeOptionItem+'_value');
		if(themeOptionItemValue != '' && themeOptionItemValue != 1){
				if(themeOptionItem == 'themeStandardColor'){
					jQuery(".skin").attr("href", themeOptionItemValue);
					//setThemeLogo(themeOptionItem);
				}else if(themeOptionItem == 'themeFullColor'){
					jQuery(".skin").attr("href", themeOptionItemValue);
					//setThemeLogo(themeOptionItem);
				}else if(themeOptionItem == 'layout'){
					jQuery('.'+themeOptionItem+'-view li').removeClass('active');
					setTimeout(function(){
						
						jQuery('.'+themeOptionItem+'-view '+' .'+themeOptionItemValue).addClass('active');	
					}, 300);
					
					
					if(themeOptionItemValue == 'frame'){
						jQuery('#ThemeRangeSlider').show('slow');
						jQuery("body").addClass(themeOptionItemValue);
						themeOptionFrameValue = getCookie('frame_value');
						if(themeOptionFrameValue == ''){
							themeOptionFrameValue = 20;
						}
						jQuery('#FramePadding').val(themeOptionFrameValue+"px");
						jQuery("#bg").css('padding',themeOptionFrameValue+"px");
					}else{
						jQuery("body").addClass(themeOptionItemValue);	
					}
				}else if(themeOptionItem == 'nav'){
					jQuery('.'+themeOptionItem+'-view li').removeClass('active');
					if( themeOptionItemValue == 'nav-light'){
						jQuery('.'+themeOptionItem+'-view').find('.light-nav').addClass('active');	
					}else if( themeOptionItemValue == 'nav-dark'){
						jQuery('.'+themeOptionItem+'-view').find('.dark-nav').addClass('active');
					}
					jQuery(".header-nav").addClass(themeOptionItemValue);
				}else if(themeOptionItem == 'header'){
					jQuery('.'+themeOptionItem+'-view li').removeClass('active');
					if( themeOptionItemValue == 'sticky-header'){
						jQuery('.'+themeOptionItem+'-view').find('.header-fixed').addClass('active');	
					}else if( themeOptionItemValue == 'sticky-no'){
						jQuery('.'+themeOptionItem+'-view').find('.header-static').addClass('active');
					}
					jQuery(".main-bar-wraper").addClass(themeOptionItemValue);
				}else if(themeOptionItem == 'menubar'){
					jQuery('.'+themeOptionItem+'-view li').removeClass('active');
					if( themeOptionItemValue == 'left'){
							jQuery('.'+themeOptionItem+'-view').find('.menubar-left').addClass('active');
							jQuery('.page-wraper').addClass('sidemenu');
							jQuery('.header-nav').addClass('sidenav content-scroll');
							
					}else if( themeOptionItemValue == 'top'){
							jQuery('.'+themeOptionItem+'-view').find('.menubar-top').addClass('active');
							jQuery('.page-wraper').removeClass('sidemenu');
							jQuery('.header-nav').removeClass('sidenav');
							 
							
							/* remove content-scroll */
							/* var logoheaderHTML =  jQuery('<div>').append($('.header-nav .logo-header').clone()).html(); 
							var navbarHTML = jQuery('<div>').append($('.header-nav .navbar-nav').clone()).html(); 
							var footermenuHTML = jQuery('<div>').append($('.header-nav .footer-menu').clone()).html(); 
							jQuery('.content-scroll').removeClass('content-scroll mCustomScrollbar _mCS_1 mCS_no_scrollbar');
							jQuery('.header-nav').empty();
							jQuery('.header-nav').append(logoheaderHTML).append(navbarHTML).append(footermenuHTML); */
					}
					
				}else if(themeOptionItem == 'bgColor' && themeOptionItemValue != ''  && themeOptionItemValue != 1){
					jQuery('#bg').css('background-color', themeOptionItemValue);
					jQuery('#bg').css('background-image', '');
				}else if(themeOptionItem == 'bgImage'  && themeOptionItemValue != '' && themeOptionItemValue != 1){
					jQuery('#bg').css({backgroundImage: "url("+themeOptionItemValue+")"});
				}else if(themeOptionItem == 'bgPatten'  && themeOptionItemValue != '' && themeOptionItemValue != 1){
					jQuery('#bg').css({backgroundImage: "url("+themeOptionItemValue+")"});
					jQuery("#bg").css( "background-size", "auto");
				}
		}
	});
	
	
	
	var ts_logo_selector = getCookie('themeStandardColor_logo_selector');
	
	var tf_logo_selector = getCookie('themeFullColor_logo_selector');
	
	
	
	if(ts_logo_selector != '' && ts_logo_selector != 1)
	{
		var ts_logo_image = getCookie('themeStandardColor_logo_image');
		
		var logoSelectorArr = ts_logo_selector.split(',');
		var logoSrcArr		= ts_logo_image.split(',');
		var arrCount = logoSelectorArr.length;
		for(var i=0; i<arrCount; i++){
			jQuery(logoSelectorArr[i]).attr('src',logoSrcArr[i]);
		}
	}
	
	if(tf_logo_selector != '' && tf_logo_selector != 1)
	{
		
		var tf_logo_image = getCookie('themeFullColor_logo_image');
		
		var logoSelectorArr = tf_logo_selector.split(',');
		var logoSrcArr		= tf_logo_image.split(',');
		var arrCount = logoSelectorArr.length;
		for(var i=0; i<arrCount; i++){
			jQuery(logoSelectorArr[i]).attr('src',logoSrcArr[i]);
		}
	}
	
	
	
	/* 
	
	jQuery(window).on('resize',function(){
		var screenWidth = $(window).width();
		if(jQuery('.site-header.dark').length > 0 && currentLogo != ''){
			if(screenWidth < 991){
				var tf_logo_image1 = getCookie('themeFullColor_logo_image');
				//alert(tf_logo_image1);
				if(tf_logo_image1 != '' && tf_logo_image1 != 1){
					var logoSelectorArr1 = tf_logo_selector.split(',');
					var logoSrcArr1		= tf_logo_image.split(',');
					var arrCount = logoSelectorArr1.length;
					for(var i=0; i<arrCount; i++){
						//alert(logoSrcArr1[i]);
						jQuery(logoSelectorArr[i]).attr('src',logoSrcArr1[i].replace('-','-white-'));
					}
				}else{
					jQuery('.sticky-header .logo-dark img').attr('src', 'images/logo-white.png');
				}
				
				
				
			}else{
				//jQuery('.logo-dark img').attr('src', currentLogo);
			}			
		} 
	});
	*/	
	

}

function setThemeLogo(themeOptionItemAttr){
	var logo = getCookie(themeOptionItemAttr+'_logo');
	if(logo != '' && logo != 1){
		jQuery(".logo-white img").attr("src", logo);
		jQuery(".logo-dark img").attr("src", logo);
		jQuery(".logo-center .logo-c").attr("src", logo);
	}
}

function setCookie(cname, cvalue, exhours) {
		var d = new Date();
		d.setTime(d.getTime() + (30*60*1000)); /* 30 Minutes*/
		var expires = "expires="+ d.toString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function deleteCookie(cname) {
		var d = new Date();
		d.setTime(d.getTime() + (1)); /* 1/1000 second*/
		var expires = "expires="+ d.toString();
		document.cookie = cname + "=1;" + expires + ";path=/";
}

function deleteAllCookie() {
	jQuery.each(themeOption, function(index, themeOptionItem) {
			deleteCookie(themeOptionItem+'_value');
	});
	
	deleteCookie('themeStandardColor_logo_selector');
	deleteCookie('themeFullColor_logo_selector');
	location.reload();
}