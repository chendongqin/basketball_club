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
      var game_address = $('#game_address').val();
      var data={id:id,dataStr:dataStr,firstStop:firstStop,lastStop:lastStop,sectionTime:sectionTime,game_address:game_address};
      $.post('/user/schedule/groupData',data,function (returnJson) {
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
    var key = -1;
    var k = -1;
    var my = -1;
    $('.alterSchedule').click(function () {
        key = $(this).attr('data-key');
        k = $(this).attr('data-k');
        my = $(this).attr('data-my');
        var eventId = $('#eventId').val();
        var data = {key:key,k:k,my:my,eventId:eventId};
        $.post('/user/schedule/getAltGroup',data,function (returnJson) {
            if(returnJson.status==true){
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
        var url = $(this).attr('data-url');
        var game_time = $('#alter_game_time').val();
        var data = {key:key,k:k,my:my,eventId:eventId,game_time:game_time};
        console.log(data);
        $.post('/user/schedule/altGroup',data,function (returnJson) {
            if(returnJson.status==true){
                window.location.href=url;
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