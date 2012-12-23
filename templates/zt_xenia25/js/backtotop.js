jQuery(function($){
			$(document).ready(function(){
				$(function () {
				var scrollDiv = document.createElement("div");
				$(scrollDiv).attr("id", "toTop").html("BACK_TO_TOP").appendTo("body");    
				$(window).scroll(function () {
						if ($(this).scrollTop() != 0) {
							$("#toTop").fadeIn();
						} else {
							$("#toTop").fadeOut();
						}
					});
					$("#toTop").click(function () {
						$("body,html").animate({
							scrollTop: 0
						},
						800);
					});
				});
			});
});