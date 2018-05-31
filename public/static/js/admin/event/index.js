$(function () {
    $(".auditEvent").click(function () {
        var id = $(this).attr('data-id');
        console.log(id);
        $.get('/admin/event/data?eventId='+id,function (json) {
            if(json.status == true){
                $.get('/admin/user/data?userId='+json.data.create_user,function (userJson) {
                    if(userJson.status !=true){
                        alert(userJson.msg);
                        return false;
                    }
                    $('#auditCreateUser').val(userJson.data.name);
                    $('#auditCreateUserID').val(userJson.data.idcard);
                    $('#auditName').val(json.data.name);
                    $('#auditAddress').val(json.data.address);
                    $('#auditType').val(json.data.type);
                    $('#time').val(json.data.start_time+'-'+json.data.end_time);
                    $('#posters').attr('src',json.data.posters);
                    $('.auditPass').attr('data-id',json.data.Id);
                    $('.auditNoPass').attr('data-id',json.data.Id);
                    $("#moadlAudit").modal('show');
                });
            }else{
                alert(json.msg);
            }
        })
    })
});
$(function () {
    $('.auditPass').click(function () {
        var id = $(this).attr('data-id');
        $.get('/admin/event/audit?audit=1&id='+id,function (json) {
            if(json.status!=true){
                alert(json.msg);
                return false;
            }
            location.reload();
        });
    });
    $('.auditNoPass').click(function () {
        var id = $(this).attr('data-id');
        $.get('/admin/event/audit?audit=2&id='+id,function (json) {
            if(json.status!=true){
                alert(json.msg);
                return false;
            }
            location.reload();
        })
    })
});
