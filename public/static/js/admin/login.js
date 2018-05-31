//验证码
$(function () {
    $("#changeCaptcha").click(function () {
        var timestamp = new Date().getTime();
        $("#captchaImg").attr('src','/index/captcha?channel=adminLogin&'+ timestamp);
    })
});

//登陆
$(function () {
    $("#login").click(function () {
        var data = $('#adminLoginForm').serialize();
        $.ajax({
            url: '/admin/index/login',
            type: 'POST',
            data: data,
            cache: false,
            dataType:'json',
            success: function (returndata) {
                if(returndata.status==true){
                    window.location.href = '/admin/management';
                }else{
                    alert(returndata.msg);
                }
            },
            error: function () {
                alert('登陆失败');
            }
        });
    })
});