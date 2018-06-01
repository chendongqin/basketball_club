$(function () {
    $('.ban').click(function () {
        var id = $(this).attr('data-id');
        var ban = $(this).attr('data-ban');
        var data ={id:id,ban:ban};
        $.post('/admin/user/ban?',data,function (json) {
            if(json.status!=true){
                alert(json.msg);
                return false;
            }
            location.reload();
        });
    })
});
