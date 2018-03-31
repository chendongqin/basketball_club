/**
 * 表单校验
 */
// $("#passwordForm").bootstrapValidator({
//     feedbackIcons: {
//         valid: 'glyphicon glyphicon-ok',
//         invalid: 'glyphicon glyphicon-remove',
//         validating: 'glyphicon glyphicon-refresh'
//     },
//     fields: {
//         newpwd: {
//             validators: {
//                 identical: {
//                     field: 'renewpwd',
//                     message: '密码不一致'
//                 }
//             }
//         },
//         renewpwd: {
//             validators: {
//                 identical: {
//                     field: 'newpwd',
//                     message: '密码不一致'
//                 }
//             }
//         }
//     }
// });
function errorPrompt(divId,inputId,msg) {
    deleteError(divId,inputId);
    $("#"+divId).addClass("has-feedback has-error");
    $("#"+inputId).after("<i class='form-control-feedback glyphicon glyphicon-remove'></i><small class='help-block'>" + msg + "</small>");
    $("#"+inputId).one("click",function() {
        deleteError(divId,inputId);
        $("#"+divId).removeClass("has-feedback has-error");
    })
}


function deleteError(divId , inputId){
    $("#"+divId).find("i").remove();
    $("#"+divId).find("small").remove();
    $("#"+divId).removeClass("has-feedback has-error");
}
(function () {
    $(".passwordmodal").click(function () {
        //清空
        $("#passwordForm")[0].reset();
        deleteError("originalPassword","oldpwd");
        deleteError("newPassword","newpwd");
        deleteError("renewPassword","renewpwd");

        $("#alterPassword").modal('show');
    });
    $('#savePassword').click(function() {
        // var bv=$("#passwordForm").data("bootstrapValidator");
        // bv.validate();
        // if(bv.isValid()){
            var oldpwd = $("#oldpwd").val(),
                newpwd = $("#newpwd").val(),
                renewpwd = $("#renewpwd").val();
            var id;
            $.ajax({
                url: "/index/admin/password/",
                data: {
                    oldpwd: oldpwd,
                    newpwd: newpwd,
                    renewpwd: renewpwd
                },
                dataType: "json",
                type: "post",
                success: function(data) {
                    if (data.status == true) {
                        window.location.href = "/logout/";
                    } else {
                        if (data.msg.indexOf("原密码") > -1) {
                            divId = "originalPassword";
                            inputId = "oldpwd";
                            errorPrompt(divId, inputId, data.msg);
                        } else if (data.msg.indexOf("新密码") > -1) {
                            errorPrompt("newPassword", "newpwd", data.msg);
                            errorPrompt("renewPassword", "renewpwd", data.msg);
                        }
                    }
                },
                error: function(data) {
                    alert('修改失败');
                },
            })
        // }
    })
})();