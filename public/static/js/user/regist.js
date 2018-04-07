$(function () {
   $("#sendEmail").click(function () {
       var email = $('#email').val();
       $.get('/index/email/regist?email='+email,function (json) {
           if(json.status===false){
               alert(json.msg);
           }
       })
   }) 
});


$(function () {
    $("#regist").click(function () {
        var data = $('#registForm').serialize();
        $.ajax({
            url: '/user/regist/i',
            type: 'POST',
            data: data,
            cache: false,
            dataType:'json',
            success: function (returndata) {
                alert(returndata.msg);
                if(returndata.status==true){
                   window.location.href = '/user/login';
                }
            },
            error: function () {
                alert('注册失败');
            }
        });

    })
});