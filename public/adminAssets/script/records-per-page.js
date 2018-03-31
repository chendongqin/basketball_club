/* 浏览条数设置 start */
(function(){
	$("#selectPage").change(function () {
		// var my_href=window.location.href;
		// var index=my_href.indexOf("?");
		// var index_pagelimit=my_href.indexOf("pagelimit");
		// var index_finance=my_href.indexOf("finance");
		// if(index_finance>-1){
		// 	if(index_pagelimit>-1){
		// 			var first_half=my_href.substring(0,index_pagelimit);
		// 			var new_href=first_half+"pagelimit/"+$("#selectPage").val();
		// 	}else{
		// 		var new_href=my_href+"/pagelimit/"+$("#selectPage").val();
		// 	}
		// }else{
		// 	if(index>-1){
		// 	console.log("2");
		// 	//有浏览条数设置
		// 		if(index_pagelimit>-1){
		// 			//    /pagelimit/xx/?
		// 			if(index>index_pagelimit){
		// 				var first_half=my_href.substring(0,index_pagelimit+10);
		// 				var second_half=my_href.substring(index)
		// 				var new_href=first_half+$("#selectPage").val()+second_half;
		// 			}else{
		// 				var first_half=my_href.substring(0,index_pagelimit);
		// 				var new_href=first_half+"pagelimit="+$("#selectPage").val();
		// 			}
		// 		}else{
		// 			var new_href=my_href+"&pagelimit="+$("#selectPage").val();
		// 		}
		// 	}else{
		// 		if(index_pagelimit>-1){
		// 			var first_half=my_href.substring(0,index_pagelimit);
		// 			var new_href=first_half+"pagelimit/"+$("#selectPage").val();
		// 		}else{
		// 			var new_href=my_href+"pagelimit/"+$("#selectPage").val();
		// 		}
		// 	}
		// }
		var my_href=window.location.href;
		var index=my_href.indexOf("?");
		var index_pagelimit=my_href.indexOf("pagelimit");

		if(index > -1){
			if(index_pagelimit > -1){
				if(index>index_pagelimit){
					var first_half=my_href.substring(0,index_pagelimit+10);
					var second_half=my_href.substring(index)
					var new_href=first_half+$("#selectPage").val()+second_half;
				}else{
					var first_half=my_href.substring(0,index_pagelimit);
					var new_href=first_half+"pagelimit="+$("#selectPage").val();
				} 
			}else{
				var new_href=my_href+"&pagelimit="+$("#selectPage").val();
			}
		}else{
				if(index_pagelimit>-1){
					var first_half=my_href.substring(0,index_pagelimit);
					var new_href=first_half+"pagelimit/"+$("#selectPage").val();
				}else{
					if(my_href[my_href.length-1] == "/"){
						var new_href=my_href+"pagelimit/"+$("#selectPage").val();
					}else{
						var new_href=my_href+"/pagelimit/"+$("#selectPage").val();
					}
					
				}
			}
		window.location.href=new_href;
	});
})();
/* 浏览条数设置 end */

/**
 * sessionLocastorage存储
 */
$(function(){
	var url = window.location.href;
	var type = 2;
	if(url.indexOf("/user/finance/voucher") > -1){
		if(sessionStorage.getItem("num") == null){
			show_loading_bar(30);

			//init
			$("#num").text("");
			$("#money").text("");
			$("#useMoney").text("");
			$("#unuseMoney").text("");
			$("#unExpireMoney").text("");
			$("#isExpireMoney").text("");
			//代金券列表统计
			$.get("/user/finance/listtotal?type="+type,function(data) {
				var _data =data.data;
				$("#num").text(_data.num);
				$("#money").text(_data.money);
				$("#useMoney").text(_data.useMoney);
				sessionStorage.setItem("num", _data.num);
				sessionStorage.setItem("money", _data.money);
				sessionStorage.setItem("useMoney", _data.useMoney);
			});
			//未使用金额
			$.get("/user/finance/unusertotal?type="+type,function(data) {
				var _data =data.data;
				$("#unuseMoney").text(_data.unuseMoney);
				sessionStorage.setItem("unuseMoney", _data.unuseMoney);
			});
			//一个月金额
			$.get("/user/finance/expiremoney?type="+type,function(data) {
				var _data =data.data;
				if(_data.isNum != null && _data.unNum != null){
					$("#unnumId").css("display","inline-block");
					$("#isnumId").css("display","inline-block");
					$("#unnum").text(_data.unNum);
					$("#isnum").text(_data.isNum);
				}
				$("#unExpireMoney").text(_data.unExpireMoney);
				$("#isExpireMoney").text(_data.isExpireMoney);
				sessionStorage.setItem("unExpireMoney", _data.unExpireMoney);
				sessionStorage.setItem("isExpireMoney", _data.isExpireMoney);
				show_loading_bar(100);
			});	
		}else{
			$("#num").text(sessionStorage.getItem("num"));
			$("#money").text(sessionStorage.getItem("money"));
			$("#useMoney").text(sessionStorage.getItem("useMoney"));
			$("#unuseMoney").text(sessionStorage.getItem("unuseMoney"));
			$("#unExpireMoney").text(sessionStorage.getItem("unExpireMoney"));
			$("#isExpireMoney").text(sessionStorage.getItem("isExpireMoney"));
		}
		//清空数据
		setInterval(function(){
			sessionStorage.clear();
		}, 10000);
	}
});
