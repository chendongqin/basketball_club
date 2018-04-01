$(function () {
    $("#addClub").click(function () {
        $("#modelAddClub").modal('show');
    })
});

$(function () {
    $("#clubMark").change(function () {
        var formData = new FormData();
        formData.append('clubMark',$("#clubMark")[0].files[0]);
        $.ajax({
            url: '/user/uploads/clubmark/',
            type: 'POST',
            data: formData,
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            success: function (returndata) {
                if(returndata.status==true){
                    $("#markImg").attr('src',returndata.data.fileName);
                    $("#mark").val(returndata.data.fileName);
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
   $("#addClubButton").click(function () {
       var address = $('#province').find("option:selected").text()+
           $('#city').find("option:selected").text()+
           $('#area').find("option:selected").text();
       var name = $("#name").val();
       var mark = $('#mark').val();
       var code = $('#code').val();
       var data = {areas:address,name:name,mark:mark,code:code};
       // console.log(data);
       $.ajax({
           url: '/user/index/createClub',
           type: 'POST',
           data: data,
           cache: false,
           dataType:'json',
           success: function (returndata) {
               if(returndata.status==true){
                   window.location.href = '/index/index/clubs?name='+name;
               }
           },
           error: function () {
               alert('添加失败');
           }
       });
   })
});