$(function () {
    $("#logout").click(function () {
        $.get('/user/logout',function (json) {
            if(json.status===true){
                location.reload();
            }
        })
    })
});
