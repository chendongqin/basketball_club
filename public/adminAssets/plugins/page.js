/**
 * 
 */
function bindEnter(event,objInput,perPage)
{
	if (event.keyCode==13)
	{
		var pageNum=objInput;
		gotoPageLib(pageNum,perPage);
	}
}

function gotoPage(objInput,perPage)
{
	var pageNum=$(objInput).parent().find("input.pagelist-input").val();
	gotoPageLib(pageNum,perPage);
}

function gotoPageLib(pageNum,perPage)
{
	var url=document.location.href;
	var page='';
	var pageIcon=url.indexOf("per_page");
	if(pageNum=='' || isNaN(pageNum))
	{
		alert('请输入页码');
		return false;
	}
	var fuhao=url.indexOf("?");
	if(pageIcon==-1)
	{
		if(fuhao==-1)
		{
			page=url+'/?op=list&per_page='+perPage*(pageNum-1);
		}
		else
		{
			page=url+'&per_page='+perPage*(pageNum-1);
		}
	}
	else
	{
		var number='';
		var start=url.indexOf("per_page=");
		var end=url.indexOf("&",start);
		if(end==-1)
		{
			number=url.substring(Number(start)+9);
		}
		else
		{
			number=url.substr(Number(start)+9,end);
		}
		page=url.replace("per_page="+number,"per_page="+perPage*(pageNum-1));
	}
	window.location.href=page;
}