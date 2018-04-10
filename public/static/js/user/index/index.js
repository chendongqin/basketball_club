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
$(function () {
    $('#saveUser').click(function () {
        var address = $('#address').val();
        var weight = $('#weight').val();
        var height = $('#height').val();
        var data = {weight:weight,height:height,address:address};
        $.post('/user/index/save',data,function (json) {
            if(json.status == true){
                location.reload();
            }else{
                alert(json.msg);
            }
        });
    })
});
$(function () {
    $('#changeCity').click(function () {
        $('.city').empty();
        $('.changeCity').show();
    })
});
$(function () {
    $('#province').change(function () {
        var address = $('#province').find("option:selected");
        $('#address').val(address);
    });
    $('#city').change(function () {
        var address = $('#province').find("option:selected").text()+
            $('#city').find("option:selected").text();
        $('#address').val(address);
    });
    $('#area').change(function () {
        var address = $('#province').find("option:selected").text()+
            $('#city').find("option:selected").text()+
            $('#area').find("option:selected").text();
        $('#address').val(address);
    });
});

$(function () {
   $('#idcardVirefy').click(function () {
      $('#virefyUserModel').modal('show');
   });
   $('#virefy').click(function () {
      var realName = $('#model_name').val();
      var idcard = $('#model_idcard').val();
      if(realName =='' || idcard ==''){
          $('.tc_error').html('请输入数据');
          $('#error').show();
      }
      $.post('/user/index/virefy',{idcard:idcard,realName:realName},function (json) {
         if(json.status === true)
             location.reload();
         else{
             $('.tc_error').html(json.msg);
             $('#error').show();
         }
      });
   });
});
