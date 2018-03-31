function iframeHeight(){
	var iframeH = $(window).height() - 70;
	if($("#leftContainer").length){$("#leftContainer").css("height",iframeH);}
	$("#rightContainer").css("height",iframeH);
}

function initPageRedirect(){
	$(".sidebar-offcanvas").find("a").bind("click",function(){
		var redirectURL = $(this).attr("rel");
		$("#rightContainer", window.top.document).attr("src",redirectURL);
		$(".sidebar-offcanvas").find("a").parent().removeClass("active");
		$(this).parent().addClass("active");
		return false;
	});
}

$(function(){
	iframeHeight();
	$(window).scroll(function(){iframeHeight();});
	$(window).resize(function(){iframeHeight();});
	$('.collapse').collapse();
	initPageRedirect();
});
function checkAllBox(obj,num)
{
	if($(obj).attr('checked')=='checked')
	{
		$(".check"+num).each(function(){
			$(this).attr('checked',true);
		});
	}else
	{
		$(".check"+num).each(function(){
			$(this).attr('checked',false);
		});
	}
}

function checkLimit(obj,string)
{
	if($(obj).attr('checked')=='checked')
	{
		if(string=='')
		{
			return false;
		}
		var arr = string.split(',');
		for(var i=0;i<arr.length;i++)
		{
			$("#app"+arr[i]).attr('checked',true);
		}
	}else
	{
		if(string=='')
		{
			return false;
		}
		var arr = string.split(',');
		for(var i=0;i<arr.length;i++)
		{
			$("#app"+arr[i]).attr('checked',false);
		}
	}
}

function itemToggle(num)
{
	$("#item"+num).toggle();
}

function showAppList()
{
	$(".appList").toggle();
}

function checkPosition(even)
{
	if($(even).attr('checked')=='checked')
	{
		$(even).parent().find(".check1").attr('checked',true);
	}else
	{
		$(even).parent().find(".check1").attr('checked',false);
	}
}