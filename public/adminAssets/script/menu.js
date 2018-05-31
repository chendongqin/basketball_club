$(document).ready(function(){
	$(".menu-content ol").each(function(i) {
        $(this).click(function() {
        	showSpan($(this));
        });
    });
});

function showSpan(objSpan)
{
	objSpan.find("span").show();
	objSpan.siblings().find("span").hide();
}