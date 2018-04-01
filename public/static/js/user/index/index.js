$(function () {
    $("#userHead").change(function () {
        var formData = new FormData();
        formData.append('userHead',$("#userHead")[0].files[0]);
        $.ajax({
            url: '/user/uploads/userhead/',
            type: 'POST',
            data: formData,
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            success: function (returndata) {
                if(returndata.status==true){
                    location.reload();
                }else{
                    alert(returndata.msg);
                }
            },
            error: function () {
                alert('上传错误');
            }
        });
    })
});
$(function () {
    $('#userHeadChange').click(function () {
        $("#userHead").click();
    })
});
