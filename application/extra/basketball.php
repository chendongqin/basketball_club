<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/11
 * Time: 23:44
 */
return[
    //好服务api
    'hfwapi'                =>[
        'key'       =>'8c898007aa214b3fb829680c097f138e',
        'url'       =>'http://apis.haoservice.com/idcard/VerifyIdcard',
    ],
    //赛事
    'event_types'        =>[
        0=>'小组赛(常规赛)',
        1=>'排名赛(季后赛)',
        2=>'淘汰赛'
    ],
    //赛事审核状态
    'event_status' =>[
        0=>'待审核',
        1=>'审核通过',
        2=>'审核不通过' ,
        3=>'审核再申请',
        4=>'再申请失败'
    ],

    'email'=>[
        'email'=>'913294974@qq.com',
        'name'=>'来战吧篮球',
        'password'=>'zmffdroymnjkbbai'
    ],

];