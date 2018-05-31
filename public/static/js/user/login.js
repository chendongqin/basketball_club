//验证码
$(function () {
    $("#changeCaptcha").click(function () {
        var timestamp = new Date().getTime();
        $("#captchaImg").attr('src','/index/captcha?channel=login&'+ timestamp);
    })
});

//登陆
$(function () {
   $("#login").click(function () {
       var data = $('#loginForm').serialize();
       $.ajax({
           url: '/user/login/i',
           type: 'POST',
           data: data,
           cache: false,
           dataType:'json',
           success: function (returndata) {
               if(returndata.status==true){
                   history.back(-1);
               }else{
                   alert(returndata.msg);
               }
           },
           error: function () {
               alert('注册失败');
           }
       });
   })
});

