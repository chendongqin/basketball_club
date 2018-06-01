$(function () {
    $('#addAdmin').click(function () {
       $('#moadladdAdmin').modal('show');
    });
    $('.addAdminButton').click(function () {
        var name = $('#addname').val();
        var mobile = $('#addmobile').val();
        var password = $('#addpassword').val();
        var data ={name:name,mobile:mobile,password:password};
        $.post('/admin/management/addAdmin?',data,function (json) {
            if(json.status!=true){
                alert(json.msg);
                return false;
            }
            location.reload();
        });
    })
});
