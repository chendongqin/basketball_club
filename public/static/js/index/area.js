$(function () {
   $("#province").change(function () {
       var provinceId =  $("#province").val();
       $.get("/index/area/city?provinceId="+provinceId,function(data) {
           $("#city").empty();
           var str = "<option value=''>选择市区</option>";
           $.each(data.data, function (i, val) {
                   str += "<option value='" + i + "'>" + val + "</option>";
           });
           $("#city").append(str);
       })
   });
});
$(function () {
    $("#city").change(function () {
        var cityId =  $("#city").val();
        $.get("/index/area?cityId="+cityId,function(data) {
            $("#area").empty();
            var str = "<option value=''>选择县区</option>";
            $.each(data.data, function (i, val) {
                str += "<option value='" + i + "'>" + val + "</option>";
            });
            $("#area").append(str);
        })
    });
});