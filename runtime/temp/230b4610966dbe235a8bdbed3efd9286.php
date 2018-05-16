<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\regist\index.phtml";i:1523335538;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>用户注册</title>
    <script type="text/javascript" src="/static/js/jq.js"></script>
    <script type="text/javascript" src="/static/js/user/regist.js"></script>
    <link rel="stylesheet" type="text/css" href="/assets/css/amazeui.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/css/app.css" />
    <style>
        .bg-regist{
            background: url(/assets/i/bg/regist.jpg);
            background-size:1400px;
            background-repeat:no-repeat;
        }
        .header {
            text-align: center;
        }
        .header h1 {
            font-size: 200%;
            color: #aa4b00;
            margin-top: 30px;
        }
        .header p {
            font-size: 14px;
        }
        .am-f-center {
            text-align: center;
        }
        .am-send-text{
            width:110px;
        }
        .am-color{
            color: #14a6ef;
        }

    </style>
</head>
<body class="bg-regist">
    <div class="header">
        <div class="am-g">
            <a href="/"><h1>BasketballClubs</h1></a>
            <p class="am-color">The game became simpler and the data became clearer<br/>比赛变得更简单，数据变得更清晰</p>
        </div>
        <hr  />
    </div>
    <div class="am-f-center">
        <form name="registForm" id="registForm">
            <div class="am-form-group">
                <label  class="am-color" for="email">邮箱：</label>
                <input type="text" name="email" id="email">
                <a  class="am-btn-secondary"  href="javascript:;" id="sendEmail">发送验证</a>
            </div>
            <div class="am-form-group">
                <label  class="am-color" for="password">密码：</label>
                <input type="password" name="password" id="password">
            </div>
            <div class="am-form-group">
                <label  class="am-color" for="secondPassword">重复密码：</label>
                <input type="password" name="secondPassword" id="secondPassword">
            </div>
            <div class="am-form-group">
                <label  class="am-color" for="emailCode">邮箱验证码：</label>
                <input  type="text" name="emailCode" id="emailCode">
            </div>
            <div class="am-form-group">
                <input class="am-btn-success" type="button" id="regist" value="注册">
            </div>
        </form>
    </div>
</body>
</html>