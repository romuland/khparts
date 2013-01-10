window.addEvent ('load', function() {
	var winScroller;
	if($('backtotop')) {
		winScroller = new Fx.Scroll(window);
		$('backtotop').addEvent('click', function(e) {
			e = new Event(e).stop();
			winScroller.toTop();
		});
	}	
	if($('shop1link') || $('shop2link')) {
		winScroller = winScroller || new Fx.Scroll(window);
		$('shop1link').addEvent('click', function(e) {
			e = new Event(e).stop();
			winScroller.toElement('shop1map');
		});
		$('shop2link').addEvent('click', function(e) {
			e = new Event(e).stop();
			winScroller.toElement('shop2map');
		});
	}	
});
jQuery(document).ready(function(){
	document.documentElement.scrollTop > 100 ? jQuery('#backtotop').show():jQuery("#backtotop").hide();
	jQuery(function () {
		// scroll body to 0px on click
		jQuery('backtotop a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
	popupPositionCalcalated = false;
	popupWrapper = '#right-side-wrapper';
});