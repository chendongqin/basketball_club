$(function () {
    $('#setStartButton').click(function () {
        var id=$(this).attr('data-id');
        var homeStarts="";
        $("input:checkbox[name='homePlayer']:checked").each(function () {
            homeStarts+=$(this).val()+","
        });
        var visitingStarts="";
        $("input:checkbox[name='visitingPlayer']:checked").each(function () {
            visitingStarts+=$(this).val()+","
        });
        var data = {id:id,homeStarts:homeStarts,visitingStarts:visitingStarts};
        $.post('/user/game/setStart',data,function (returnData) {
            if(returnData.status==true)
                location.reload();
            else
                alert(returnData.msg);
        })
    });
});