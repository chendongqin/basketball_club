$(function () {
    $('#sureSchedules').click(function () {
        $('#modelSureSchedules').modal('show');
    });
   $('#actSureSchedules').click(function () {
       var id = $('#eventId').val();
      var dataStr = $('#code').val();
      var firstStop = $('#firstStop').val();
      var lastStop = $('#lastStop').val();
      var sectionTime = $('#sectionTime').val();
      var data={id:id,dataStr:dataStr,firstStop:firstStop,lastStop:lastStop,sectionTime:sectionTime};
      $.post('/user/schedule/importData',data,function (returnJson) {
          if(returnJson.status==true){
              window.location.href = '/user/event/management?id='+id;
          }else{
              $('.tc_error').html(returnJson.msg);
              $('.error').show();
          }
      });
   });
});
$(function () {
    var id = -1;
    $('.alterSchedule').click(function () {
        id = $(this).attr('data-id');
        var eventId = $('#eventId').val();
        var data = {id:id,eventId:eventId};
        $.post('/user/schedule/getAltSchedule',data,function (returnJson) {
            if(returnJson.status==true){
                $('#alter_game_place').val(returnJson.data.game_address);
                $('#alter_game_time').val(UnixToDate(returnJson.data.game_time,true));
            }else{
                $('.tc_error').html(returnJson.msg);
                $('.error').show();
            }
        });
        $('#modelAlterSchedules').modal('show');
    });
    $('#actAlterSchedules').click(function () {
        var eventId = $('#eventId').val();
        var game_address = $('#alter_game_place').val();
        var game_time = $('#alter_game_time').val();
        var data = {id:id,eventId:eventId,game_time:game_time,game_address:game_address};
        $.post('/user/schedule/altSchedule',data,function (returnJson) {
            if(returnJson.status==true){
                location.reload();
            }else{
                $('.tc_error').html(returnJson.msg);
                $('.error').show();
            }
        });
    });
});

function UnixToDate(unixTime, isFull, timeZone) {
    if (typeof (timeZone) == 'number'){
        unixTime = parseInt(unixTime) + parseInt(timeZone) * 60 * 60;
    }
    var time = new Date(unixTime * 1000);
    var ymdhis = "";
    ymdhis += time.getFullYear() + "-";
    ymdhis += (time.getMonth()+1) + "-";
    ymdhis += time.getDate();
    if (isFull === true){
        ymdhis += " " + time.getHours() + ":";
        ymdhis += time.getMinutes() + ":";
        ymdhis += time.getSeconds();
    }
    return ymdhis;
}