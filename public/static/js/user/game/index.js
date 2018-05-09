$(function(){
    // var maxtime = 10*60; //10分钟，按秒计算，自己调整!  
    var maxtime = Number($('.header__time').text().split(':')[0])*60+Number($('.header__time').text().split(':')[1]);
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
    // 选择球员校验
    function choose_check(){
        var home_check = $('input[name="homePlayer"]:checked').val();   //主队选中球员
        var away_check = $('input[name="awayPlayer"]:checked').val();   //客队选中球员
        var check_val;   //选中球员信息
        var is_home;  //是否是主队球员
        if(home_check == undefined && away_check == undefined){  //没有选中任何球员情况
            alert("没有选中球员！");
            return {flag:false};
        }else if(home_check != undefined && away_check != undefined){   //两队都有选中球员情况
            alert("只能选中一名球员！");
            return {flag:false};
        }else{
            if(home_check){  //选中球员为主队球员情况
                check_val = home_check;
                is_home = 1;
                // alert("选中的球员号码为：主队"+check_val+"号");
            }else{   //选中球员为客队球员情况
                check_val = away_check;
                is_home = 0;
                //  alert("选中的球员号码为：客队"+check_val+"号");
            }
            return {flag:true,playerId:check_val,hometeam:is_home};
        }
    }
    //AJAX封装函数开始
    /*
     * @params url  接口路径
     * @params para  接口参数
    */
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
    //比赛ID还未填写,请求暂停主客队未填写
    // 暂停/开始
    $('.start_to_stop').on('click',function(){
        if($(this).attr('data-type')==1){
            if($('#j-start').text() == "开始"){
                timer = setInterval(function(){
                    CountDown();
                }, 1000);
                $('#j-start').text("暂停"); 
            }else{
                clearInterval(timer);
                $('#j-start').text("开始"); 
            }
        }else{
            clearInterval(timer);
            $('#j-start').text("开始"); 
        }


        http.post("/user/game/stop",
            {
                id:1,
                hometeam:0,
                second:maxtime,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                var logs = "";
                // 成功函数
                $('.home-score').text(res.data.home_score);
                $('.away-score').text(res.data.visiting_score);
                for(var i = 0;i<res.data.logs.length; i++){
                    logs +="<p>"+res.data.logs[i]+"</p>"
                }
                $('.detail__character').html(logs);

            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
    })
    // 两分球
    $('.double_score').on("click",function(){
        if(choose_check().flag){
            http.post("/user/game/getTwo",
            {
                id:1,
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                var logs = "";
                // 成功函数
                $('.home-score').text(res.data.home_score);
                $('.away-score').text(res.data.visiting_score);
                for(var i = 0;i<res.data.logs.length; i++){
                    logs +="<p>"+res.data.logs[i]+"</p>"
                }
                $('.detail__character').html(logs);

            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 三分球
    $('.three_score').on("click",function(){
        if(choose_check().flag){
            http.post("/user/game/getThree",
            {
                id:1,
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                var logs = "";
                // 成功函数
                $('.home-score').text(res.data.home_score);
                $('.away-score').text(res.data.visiting_score);
                for(var i = 0;i<res.data.logs.length; i++){
                    logs +="<p>"+res.data.logs[i]+"</p>"
                }
                $('.detail__character').html(logs);
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 球员罚球
    $('.one_score').on("click",function(){
        if(choose_check().flag){
            http.post("/user/game/getOne",
            {
                id:1,
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                var logs = "";
                // 成功函数
                $('.home-score').text(res.data.home_score);
                $('.away-score').text(res.data.visiting_score);
                for(var i = 0;i<res.data.logs.length; i++){
                    logs +="<p>"+res.data.logs[i]+"</p>"
                }
                $('.detail__character').html(logs);
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
})