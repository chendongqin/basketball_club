$(function () {
    $("#addEvent").click(function () {
        var address = $('#province').find("option:selected").text()+
            $('#city').find("option:selected").text()+
            $('#area').find("option:selected").text();
            $('#address').val(address);
        var data = $('#addEventForm').serialize();
        // console.log(data);
        $.ajax({
            url: '/user/event/actadd',
            type: 'POST',
            data: data,
            cache: false,
            dataType:'json',
            success: function (returndata) {
                alert(returndata.msg);
                if(returndata.status==true){
                    window.location.href = '/user/event';
                }
            },
            error: function () {
                alert('添加失败');
            }
        });
    });
});

$(function () {
   $('#postersFile').change(function () {
       var formData = new FormData();
       formData.append('postersFile',$("#postersFile")[0].files[0]);
       $.ajax({
           url: '/user/event/posters/',
           type: 'POST',
           data: formData,
           async: true,
           cache: false,
           contentType: false,
           processData: false,
           success: function (returndata) {
               if(returndata.status==true){
                   $("#postersImg").attr('src',returndata.data.fileName);
                   $("#posters").val(returndata.data.fileName);
               }else{
                   alert(returndata.msg);
               }
           },
           error: function () {
               alert('上传错误');
           }
       });
   });
});