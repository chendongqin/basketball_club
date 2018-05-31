$(function () {
    $("#addClub").click(function () {
        $('.error').hide();
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
               }else{
                   $('.tc_error').html(returndata.msg);
                   $('.error').show();
               }
           },
           error: function () {
               $('.tc_error').html('添加失败');
               $('.error').show();
           }
       });
   })
});

$(function () {
    var idArray = $(".club-list").find(".club-list-ul");
    $.each(idArray,function(i,temp){
        var captain = $(temp).attr("data-captain");
        var data = {id:captain};
        if($("span").hasClass("captainName")){
            $.post("/index/data/userName",data,function(json){
                var captainName = $(temp).find(".captainName");
                captainName.text(json.data.name);
            });
        }
    });
});

$(function () {
    var id = 0;
   $('.joinClub').click(function () {
       $('.error').hide();
       id = $(this).attr('data-id');
       $('#modelJoinClub').modal('show');
   }) ;
   $('#joinClubButton').click(function () {
       var code = $('#modal_string').val();
       $.post('/user/index/joinClub',{id:id,code:code},function (json) {
            if(json.status == true){
                window.location.href = '/user/club?id='+id;
            }else{
                $('.tc_error').html(json.msg);
                $('.error').show();
            }
       });
   });
    $('#applyJoinButton').click(function () {
        var reason = $('#modal_string').val();
        $.post('/user/index/applyJoin',{id:id,reason:reason},function (json) {
            if(json.status == true){
               location.reload();
            }else{
                $('.tc_error').html(json.msg);
                $('.error').show();
            }
        });
    });
});