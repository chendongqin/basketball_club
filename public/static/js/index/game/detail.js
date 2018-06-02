$(function(){
    require.config({
        paths: {
            echarts: 'http://echarts.baidu.com/build/dist'
        }
    });

    var userId = $('#playerId').val();
    $.get('/index/game/per?userId='+userId,function (data)    {
        var daArr = data.data;
        require(
            [
                'echarts',
                'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
            ],
            function (ec) {
                // 基于准备好的dom，初始化echarts图表
                var myChart = ec.init(document.getElementById('main'));

                var option = {
                    title : {
                        text: '近10场比赛效率值分析',
                    },
                    tooltip : {
                        trigger: 'axis'
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            boundaryGap : false,
                            data : ['0','1','2','3','4','5','6','7','8','9','10']
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value',
                            axisLabel : {
                                formatter: '{value}'
                            }
                        }
                    ],
                    series : [
                        {
                            name: 'per',
                            type: 'line',
                            data: daArr,
                            markPoint: {
                                data: [
                                    {type: 'max', name: '最大值'},
                                    // {type: 'min', name: '最小值'}
                                ]
                            }
                        }
                    ]
                };

                // 为echarts对象加载数据
                myChart.setOption(option);
            }
        );

    } )
});



// 使用

