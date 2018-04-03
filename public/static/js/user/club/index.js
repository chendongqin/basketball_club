$(function () {
   $('#changeCaptain') .click(function () {
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
                $('#error').show();
            }
        });
    })
});