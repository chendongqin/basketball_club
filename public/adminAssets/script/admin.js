//修改密码
(function(){
    var $modal_edit = $('#myform');
    var $tip = $modal_edit.find('.help-block');
    var $oldPassword = $modal_edit.find("input[name='oldPassword']"),
        $password = $modal_edit.find("input[name='password']"),
        $rePassword = $modal_edit.find("input[name='rePassword']");
        $modal_edit.find('input').focus(function(){
            $tip.empty();
            
        });
    $modal_edit.find(".btn-primary").click(function(){
         var param = {
             'oldPassword':$oldPassword.val(),
              'password':$password.val(),
              'rePassword':$rePassword.val()
           };
        if(param['oldPassword']==''){
            $tip.html('原密码不能为空');
        }else if(param['password']==''){
            $tip.html('新密码不能为空');
        }else if(param['rePassword']!= param['password']){
            $tip.html('新旧密码不一致');
        }else{
            $.post('/admin/change/',param,function(json){
                if(json['status']){
                    alert('修改成功');
                    parent.location.reload();
                }else{
                    $tip.html(json['msg']);
                }
            },'json');
        }
    });

    $(".changeStatus").click(function(){
        if(confirm('确定执行此操作？')){
            var url = $(this).attr('rel');
            $.get(url,'',function(json){
                    if(json['status']){
                        alert(json['msg']);
                        location.href="/admin/userlist/";
                    }else{
                        alert(json['msg']);
                        return false;
                    }
                },'json');
        }
    });
    $(".deluser").click(function(){
        if(confirm('确定执行此操作？')){
            var url = $(this).attr('rel');
            $.get(url,'',function(json){
                    if(json['status']){
                        alert(json['msg']);
                        location.href="/admin/userlist/";
                    }else{
                        alert(json['msg']);
                        return false;
                    }
                },'json');
        }
    });
})();

//修改信息
(function(){
    var $modal_edit = $('#myform');
    var $tip = $modal_edit.find('.help-block');
    var $groupId = $modal_edit.find("input[name='groupId[]']"),
        $positionId = $modal_edit.find("select[name='positionId']"),
        $jobsType = $modal_edit.find("select[name='jobsType']"),
        $number = $modal_edit.find("input[name='number']"),
        $realName = $modal_edit.find("input[name='realName']"),
        $password = $modal_edit.find("input[name='password']"),
        $status = $modal_edit.find("input[name='status']"),
        $csid = $modal_edit.find("input[name='csid']"),
        $appid = $modal_edit.find("input[name='appId[]']"),
        $username = $modal_edit.find("input[name='username']"),
        $do = $modal_edit.find("input[name='do']");
        $modal_edit.find('input').focus(function(){
            $tip.empty();
            
        });
        $modal_edit.find(".submit").click(function(){
             var $groupStr = '',
                  $appIdStr = '';
            for(k in $groupId){
                if($groupId[k].checked){
                    $groupStr+=$groupId[k].value+','
                }
            }
            for(k in $appid){
                if($appid[k].checked){
                    $appIdStr+=$appid[k].value+','
                }
            }
         var param = {
              'groupId':$groupStr,
              'csid':$csid.val(),
              'positionId':$positionId.val(),
              'jobsType':$jobsType.val(),
              'number':$number.val(),
              'realName':$realName.val(),
              'password':$password.val(),
              'appid':$appIdStr,
              'username':$username.val(),
              'do':$do.val(),
              'status':$status.is(':checked') ? 1 : 0
           };

        if(param['groupId'] == ''){
            alert('请选择部门');
            return false;
        }else if(param['realName'] == ''){
            alert('真实姓名不能为空');
            return false;
        }else if(param['positionId'] == ''){
            alert('请选择职位');
            return false;
        }else if(param['username'] == ''){
            alert('用户名不能为空');
            return false;
        }else{
            $.post('/admin/save/',param,function(json){
                if(json['status']){
                    alert(json['msg']);
                    location.href="/admin/userlist/";
                }else{
                    alert(json['msg']);
                }
            },'json');
        }
    });
})();