$(function () {
   $('#changeCaptain') .click(function () {
       $('.error').hide();
       $('#modelChangeCaptain').modal('show');
   })
});
$(function () {
    $('#changeCaptainButton') .click(function () {
        var id = $('#clubId').val();
        var playerId = $('#playerId').val();
        var data = {id:id,playerId:playerId};
        $.post('/user/club/changeCaptain',data,function (json) {
            if(json.status == true){
                location.reload();
            }else{
                $('.tc_error').html(json.msg);
                $('.error').show();
            }
        });
    })
});
$(function () {
   var id = $('#captainName').attr('data-id');
   $.get('/index/data/userName?id='+id,function (json) {
       $('#captainName').html(json.data.name);
   });
});
$(function () {
   $('.pass').click(function () {
       var id= $(this).attr('data-id');
        $.post('/user/club/dealApply',{id:id,deal:1},function (json) {
            if(json.status == true)
                location.reload();
            else
                alert(json.msg);
        })
   });
    $('.refuse').click(function () {
        var id= $(this).attr('data-id');
        $.post('/user/club/dealApply',{id:id,deal:0},function (json) {
            if(json.status == true)
                location.reload();
            else
                alert(json.msg);
        })
    });
    $('.delPlaers').click(function () {
        var id = $('#clubId').val();
        var playerId= $(this).attr('data-id');
        $.post('/user/club/delplayer',{id:id,playerId:playerId},function (json) {
            if(json.status == true)
                location.reload();
            else
                alert(json.msg);
        })
    });
});