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
    // 选择一个球员校验
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
    // 选择主客队
    var select_value = "";
    function get_select_value(){
        select_value = $(".detail__select option:selected").val();
        $(".detail__select").change(function(){
            select_value = $(".detail__select option:selected").val();
        })
    }
    get_select_value();
    // 选择两个个球员校验
    function choose_double_check(){
        var home_check = $('input[name="homePlayer"]:checked').val();   //主队选中球员
        var away_check = $('input[name="awayPlayer"]:checked').val();   //客队选中球员
        var is_home = select_value;  //是否是主队球员在前
        if(home_check == undefined || away_check == undefined){  //没有选中任何球员情况
            alert("请选中两名球员！");
            return {flag:false};
        }else{
            if(is_home == 1){
                return {flag:true,Id1:home_check,Id2:away_check,hometeam:is_home};
            }else{
                return {flag:true,Id1:away_check,Id2:home_check,hometeam:is_home};
            }
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
    // 暂停/开始
    if($('.acting').text() == 1){
        timer = setInterval(function(){
            CountDown();
        }, 1000);
    }
    $('.start_to_stop').on('click',function(){
        http.post("/user/game/stop",
            {
                id:$('.schedule_id').text(),
                hometeam:select_value,
                second:maxtime,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    $('.home-score').text(res.data.home_score);
                    $('.away-score').text(res.data.visiting_score);
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                    clearInterval(timer);
                    if(res.data.logs[0] == "比赛继续"){
                        $('#j-start').text("死球暂停");
                        timer = setInterval(function(){
                            CountDown();
                        }, 1000); 
                    }else if(res.data.logs[0] == "死球暂停"){
                        $('#j-start').text("开始"); 
                        clearInterval(timer);
                    }else{
                        $('#j-start').text("开始"); 
                        clearInterval(timer);
                    }
                }else{
                    alert(res.msg);
                }

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
                id:$('.schedule_id').text(),
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    $('.home-score').text(res.data.home_score);
                    $('.away-score').text(res.data.visiting_score);
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }

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
                id:$('.schedule_id').text(),
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    $('.home-score').text(res.data.home_score);
                    $('.away-score').text(res.data.visiting_score);
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
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
                id:$('.schedule_id').text(),
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    $('.home-score').text(res.data.home_score);
                    $('.away-score').text(res.data.visiting_score);
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }

            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 失误
    $('#j-lost').on("click",function(){
        if(choose_check().flag){
            http.post("/user/game/lost",
            {
                id:$('.schedule_id').text(),
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 篮板
    $('#j-rebounds').on("click",function(){
        if(choose_check().flag){
            http.post("/user/game/rebounds",
            {
                id:$('.schedule_id').text(),
                playerId:choose_check().playerId,
                hometeam:choose_check().hometeam,
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 抢断
    $('#j-steals').on("click",function(){
        if(choose_double_check().flag){
            http.post("/user/game/steals",
            {
                id:$('.schedule_id').text(),
                playerId:choose_double_check().Id1,
                stealsId:choose_double_check().Id2,
                hometeam:choose_double_check().hometeam,
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 盖帽
    $('.blocks').on("click",function(){
        if(choose_double_check().flag){
            http.post("/user/game/blocks",
            {
                id:$('.schedule_id').text(),
                playerId:choose_double_check().Id1,
                blocksId:choose_double_check().Id2,
                hometeam:choose_double_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 犯规
    $('.faul').on("click",function(){
        if(choose_double_check().flag){
            http.post("/user/game/faul",
            {
                id:$('.schedule_id').text(),
                playerId:choose_double_check().Id1,
                foulId:choose_double_check().Id2,
                hometeam:choose_double_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    var logs = "";
                    // 成功函数
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    // 助攻
    $('.assists').on("click",function(){
        var data_type = 0;
        if(choose_check().flag){
            data_type = $(this).attr('data-type');
            http.post("/user/game/playing",
            {
                id:$('.schedule_id').text(),
                hometeam:choose_check().hometeam,
                type:1
            })
            .then(function(res){
                if(res.data!=""){
                    $('#j-get-player').text("");
                    var data = "";
                    for(var i= 0;i<res.data.length;i++){
                        if(res.data[i].user_id != choose_check().playerId){
                            data += "<li class='detail__team-item'><input type='radio' name='doublePlayer' checked value='"+res.data[i].user_id+"'> "+res.data[i].user_name+" #1</li>"
                        }
                    }
                    $('#j-get-player').html(data);
                    $('#modelAssist').modal('show');
                }
                console.log($(this).attr('data-type'));
                $('#j-assists').attr('data-type',data_type);
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
        }
    })
    $('#j-assists').on('click',function(){
        http.post("/user/game/assists",
            {
                id:$('.schedule_id').text(),
                playerId:choose_check().playerId,
                scoreId:$('input[name="doublePlayer"]:checked').val(),
                hometeam:choose_check().hometeam,
                type:$(this).attr('data-type')
            })
            .then(function(res){
                if(res.data!=""){
                    $('.home-score').text(res.data.home_score);
                    $('.away-score').text(res.data.visiting_score);
                    var logs = "";
                    // 成功函数
                    for(var i = 0;i<res.data.logs.length; i++){
                        logs +="<p>"+res.data.logs[i]+"</p>"
                    }
                    $('.detail__character').html(logs);
                }else{
                    alert(res.msg);
                }
            })
            .catch(function(e){
                // 失败函数
                console.log(e);
            });
    })
    // 撤销(撤销接口分数没变化，未返回数据)
    $('#j-returnBack').on("click",function(){
        http.post("/user/game/returnBack",
        {
            id:$('.schedule_id').text(),
        })
        .then(function(res){
            // if(res.data!=""){
            //     var logs = "";
            //     // 成功函数
            //     for(var i = 0;i<res.data.logs.length; i++){
            //         logs +="<p>"+res.data.logs[i]+"</p>"
            //     }
            //     $('.detail__character').html(logs);
            // }else{
            //     alert(res.msg);
            // }
        })
        .catch(function(e){
            // 失败函数
            console.log(e);
        });
    })
})