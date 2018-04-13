$(function () {
   $('#joinGame').click(function () {
       $('.error').hide();
       var id = $('#eventId').val();
       var str = '<label class="" for="clubId">参加球队：</label>';
       $('.chooseClub').empty();
       $.get('/user/club/captainClub?eventId='+id,function (json) {
            if(json.status==true){
                str = str +'<select id="clubId">';
                $.each(json.data,function(key,val){
                    str = str+'<option value="'+val.Id+'">'+val.name+'</option>';
                });
                str = str + '</select>';
            }else{
                str = json.msg;
            }
           $('.chooseClub').append(str);
       });
       $('#modelAddGame').modal('show');
   }) ;
});
$(function () {
   $('#addGameButton').click(function () {
       var id = $('#eventId').val();
       var code = $('#code').val();
       var clubId = $('#clubId').val();
       var data = {id:id,code:code,clubId:clubId};
       // console.log(data);
       $.post('/user/club/joinEvent',data,function (json) {
           if(json.status == true){
               window.location.href = '/index/event?id='+id;
           }else{
                $('.tc_error').html(json.msg);
                $('.error').show();
           }
       })
   });
    $('#applyGameButton').click(function () {
        var id = $('#eventId').val();
        var reason = $('#code').val();
        var clubId = $('#clubId').val();
        var data = {id:id,reason:reason,clubId:clubId};
        // console.log(data);
        $.post('/user/club/applyEvent',data,function (json) {
            if(json.status == true){
                window.location.href = '/index/event?id='+id;
            }else{
                $('.tc_error').html(json.msg);
                $('.error').show();
            }
        })
    });
});