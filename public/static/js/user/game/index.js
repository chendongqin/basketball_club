$(function(){
    var maxtime = 10*60; //10分钟，按秒计算，自己调整!  
    var timer;    //定时器
    // 倒计时方法 
    function CountDown() {
        if (maxtime >= 0) {
            --maxtime;
            minutes = Math.floor(maxtime / 60);
            seconds = Math.floor(maxtime % 60);
            if(minutes<10){
                minutes = "0"+minutes;
            }
            if(seconds<10){
                seconds = "0"+seconds;
            }
            msg = minutes + ":" + seconds;
            $('.header__time').text(msg);
        } else{
            clearInterval(timer);
            alert("时间到，结束!");
        }
    }
    // 开始计时
    $('#j-start').on('click',function(){
        if($(this).text() == "开始"){
            timer = setInterval(function(){
                CountDown();
            }, 1000);
            $(this).text("暂停"); 
        }else{
            clearInterval(timer);
             $(this).text("开始"); 
        }
    })
    // 死球停止
    $('#j-stop').on('click',function(){
        clearInterval(timer);  
    })
    // 球员操作示例
    $('#test').on('click',function(){
        var home_check = $('input[name="homePlayer"]:checked').val();   //主队选中球员
        var away_check = $('input[name="awayPlayer"]:checked').val();   //客队选中球员
        var check_val;   //最终球员信息
        if(home_check == undefined && away_check == undefined){  //没有选中任何球员情况
            alert("没有选中球员！");
        }else if(home_check != undefined && away_check != undefined){   //两队都有选中球员情况
            alert("只能选中一名球员！");
        }else{
            if(home_check){  //选中球员为主队球员情况
                check_val = home_check;
                alert("选中的球员号码为：主队"+check_val+"号");
            }else{   //选中球员为客队球员情况
                check_val = away_check;
                 alert("选中的球员号码为：客队"+check_val+"号");
            }
            // 使用AJAX将信息传递到服务端的操作放在这里

        }
    })


    //AJAX封装函数开始
    function Http(){
    }
    Http.prototype.get = function(url,para){
        return request("GET",url,para);
    }
    Http.prototype.post = function(url,para){
        return request("POST",url,para);
    }
    Http.prototype.put = function(url,para){
        return request("PUT",url,para);
    }
    Http.prototype.delete = function(url,para){
        return request("DELETE",url,para);
    }
    function request(type,url,para){
        return new Promise(function(resolve, reject ){
            $.ajax({
                url: url,
                type: type,
                data: para,
                dataType: 'json',
                success: function(res){
                    resolve(res);
                },
                error: function(err){
                    reject(err);
                }    
            });
        })
    }
    var http = new Http;
    //AJAX封装函数结束
    
    //AJAX使用示例
    http.get("https://www.kuaidimao.com/xapi/epscompany",{data:11})
    .then(function(res){
        // 成功函数
        console.log(res);
    })
    .catch(function(e){
        // 失败函数
        console.log(e);
    });
})