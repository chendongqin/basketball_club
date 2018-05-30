setInterval(function working(){
    var id = $('#scheduleId').val();
    $.get('/index/game/upplayerData?id='+id,function (json) {
        var str1 = '';
        var str2 = '';
        if(json.status == true && json.code==1){
            $('.home-player').empty();
            $.each(json.data.players.home,function (i,val) {
                str1 = str1 + '<tr>\n' + '                <td>\n';
                if(val.is_playing==1)
                    str1 = str1+ '√';
                str1 = str1+ val.player_name+
                    '                </td>'
                    +'<td>'+ val.score+ '</td>'
                    +'<td>'+ val.rebounds+ '</td>'
                    +'<td>'+ val.assists+ '</td>'
                    +'<td>'+ val.hit+'/'+ val.shoot+'</td>'
                    +'<td>'+ val.three_hit+'/'+ val.three_shoot+'</td>'
                    +'<td>'+ val.penalty_hit+'/'+ val.penalty_shoot+ '</td>'
                    +'<td>'+ val.blocks+ '</td>'
                    +'<td>'+ val.steals+ '</td>'
                    +'<td>'+ val.lost+ '</td>'
                    +'<td>'+ val.foul+ '</td>'
                    +'<td>'+ (val.playing_time/60).toFixed(1)+ '</td>'
                ;
            });
            $('.home-player').append(str1);
            $('.visiting-player').empty();
            $.each(json.data.players.visiting,function (i,val) {
                str2 = str2 + '<tr>\n' + '                <td>\n';
                if(val.is_playing==1)
                    str1 = str1+ '√';
                str2 = str2+ val.player_name+
                    '                </td>'
                    +'<td>'+ val.score+ '</td>'
                    +'<td>'+ val.rebounds+ '</td>'
                    +'<td>'+ val.assists+ '</td>'
                    +'<td>'+ val.hit+'/'+ val.shoot+'</td>'
                    +'<td>'+ val.three_hit+'/'+ val.three_shoot+'</td>'
                    +'<td>'+ val.penalty_hit+'/'+ val.penalty_shoot+ '</td>'
                    +'<td>'+ val.blocks+ '</td>'
                    +'<td>'+ val.steals+ '</td>'
                    +'<td>'+ val.lost+ '</td>'
                    +'<td>'+ val.foul+ '</td>'
                    +'<td>'+ (val.playing_time/60).toFixed(1)+ '</td>'
                ;
            });
            $('.visiting-player').append(str2);
            $('.home-score').html(json.data.homeScore);
            $('.away-score').html(json.data.visitingScore);
        }
    });
},30000);