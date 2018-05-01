$(function () {
    $('#changeCode').click(function () {
        var id = $(this).attr('data-id');
        var code = $('#code').val();
        var data = {id:id,code:code};
        // console.log(data);
        $.post('/user/event/alterCode',data,function (json) {
            if(json.status == true){
                location.reload();
            }else{
                alert(json.msg);
            }
        })
    })

    $('.delJoins').click(function () {
        var clubId = $(this).attr('data-id');
        var id = $(this).attr('data-eventId');
        var data = {id:id,clubId:clubId};
        $.post('/user/event/delJoin',data,function (json) {
            if(json.status == true){
                location.reload();
            }else{
                alert(json.msg);
            }
        })
    });
    $('#changeTime').click(function () {
        var id = $(this).attr('data-id');
        var start = $('#startTime').val();
        var end = $('#endTime').val();
        var data = {id:id,startTime:start,endTime:end};
        $.post('/user/event/alterTime',data,function (json) {
            if(json.status == true){
                location.reload();
            }else{
                alert(json.msg);
            }
        })
    });

    $('#addSchedules').click(function () {
        $('#modelSchendule').modal('show');
    });

    $('.pass').click(function () {
        var id= $(this).attr('data-id');
        $.post('/user/event/pass',{id:id},function (json) {
            if(json.status == true)
                location.reload();
            else
                alert(json.msg);
        })
    });
    $('.refuse').click(function () {
        var id= $(this).attr('data-id');
        $.post('/user/event/refuse',{id:id},function (json) {
            if(json.status == true)
                location.reload();
            else
                alert(json.msg);
        })
    });
    $('#importSchedulesButton').click(function () {
        $('#modelImportSchendule').modal('show');
    });

});