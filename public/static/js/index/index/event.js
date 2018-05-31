$(function () {
    var url = window.location.search;
    $('#areaSearch').change(function () {
        var area = $('#areaSearch').val();
        if(url.indexOf("?") >= 0){
            var oldarea = getUrlParam('area');
            if(oldarea===null){
                window.location.href = url+'&area='+area;
            }
            else {
                url = url.replace('area='+oldarea,'area='+area);
                window.location.href = url;
            }
        }
        else
            window.location.href = '?area='+area;
    });
    $('#typeSearch').change(function () {
        var type = $('#typeSearch').val();
        if(url.indexOf("?") >= 0){
            var oldtype = getUrlParam('type');
            if(oldtype===null)
                window.location.href = url+'&type='+type;
            else {
                url = url.replace('type='+oldtype,'type='+type);
                window.location.href = url;
            }
        }
        else
            window.location.href = '?type='+type;
    });
    $('#statusSearch').change(function () {
        var status = $('#statusSearch').val();
        if(url.indexOf("?") >= 0){
            var oldstatus = getUrlParam('status');
            if(oldstatus===null)
                window.location.href = url+'&status='+status;
            else {
                url = url.replace('status='+oldstatus,'status='+status);
                window.location.href = url;
            }
        }
        else
            window.location.href = '?status='+status;
    });
});

function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg); //匹配目标参数
    if (r != null) return r[2]; return null; //返回参数值
}