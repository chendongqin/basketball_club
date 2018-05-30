setInterval(function working(){
    var id = $('#scheduleId').val();
    $.get('/index/game/uplogs?id='+id,function (json) {
        var str = '';
        if(json.status == true && json.code==1){
            $('.broadcast').empty();
            $.each(json.data.logs,function (i,val) {
                str = str + "<p>"+val+"</p>";
            });
            $('.broadcast').append(str);
            $('.home-score').html(json.data.homeScore);
            $('.away-score').html(json.data.visitingScore);
        }
    });
},10000);