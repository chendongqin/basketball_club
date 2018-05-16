<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:80:"D:\phpstudy\WWW\basketballClub\public/../application/index\view\game\index.phtml";i:1526237404;s:66:"D:\phpstudy\WWW\basketballClub\application\index\view\layout.phtml";i:1523168499;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php if (isset($title) && $title): ?><?php echo $title; else: ?><?php echo '篮球赛事管理';endif;?></title>
    <link rel="stylesheet" type="text/css" href="/static/css/header.css" />
    <div class="top-divide">
        <div><h2>让比赛变得更简单 让数据变得更清晰</h2></div>
    </div>
    <div class="nav-full">
        <div class="nav">
            <div class="nav-left">
                <span>BasketballClubs篮球赛事系统</span>
            </div>
            <div class="nav-right">
                <ul class="clearfix">
                    <li><a href="/">首页</a></li>
                    <li><a href="/index/index/event">赛事</a></li>
                    <li><a href="/index/index/clubs">球队</a></li>
                    <?php if(!empty($user)):?>
                        <li><a href="/user">个人中心</a></li>
                        <li><a href="/user/logout">登出</a></li>
                    <?php else:?>
                        <li><a href="/user/login">登陆</a></li>
                        <li><a href="/user/regist">注册</a></li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>

<link rel="stylesheet" type="text/css" href="/static/css/user/game/index.css" />
<link rel="stylesheet" type="text/css" href="/static/css/index/game/index.css" />
<script type="text/javascript" src="/static/js/jq.js"></script>
<div class="header__info">
    <div class="header__team-name home-name"><?php echo $home['name'];?></div>
    <div class="header__team-score home-score"><span id="homeScore"><?php echo $schedule['home_score'];?></span></div>
    <div class="header__time-box fl">
        <div class="header__picth"><span id="section"><?php echo $schedule['section']>4?'第'.($schedule['section']-4).'加时':'第'.$schedule['section'].'节'?></span></div>
        <div class="header__time"><span id="second"><?php echo (int)($schedule['second']/60);?>:<?php echo str_pad($schedule['second']%60,2,0);?></span></div>
    </div>
    <div class="header__team-name away-name"><?php echo $visiting['name'];?></div>
    <div class="header__team-score away-score"><span id="visitingScore"><?php echo $schedule['visiting_score'];?></span></div>
</div>
    <div class="data-right">
        <a href="/index/game/playerData?id=<?php echo $schedule['Id'];?>">球员数据</a>
    </div>
    <div class="broadcast">
        <?php foreach ($gameLogs as $gameLog):?>
            <p><?php echo $gameLog;?></p>
        <?php endforeach;?>
    </div>
<link rel="stylesheet" type="text/css" href="/static/css/footer.css" />
<div class="footer">
    <div class=" footer-top">
        <div class="footer-top-left" >
            <div class="image-text">
                <img src="/static/image/logo.png" width="100px">
                <span>BasketballClubs</span>
            </div>
            <div class="footer-top-left-div">@篮球赛事系统</div>
        </div>
        <div class="footer-top-right" >
            <div>关于我们</div>
            <img src="/static/image/jmu.png"width="100px">
        </div>
        <div class="footer-top-left footer-top-right2" >
            <span>集美大学计算机工程学院毕业设计</span>
        </div>
    </div>
    <div class="footer-bottom">
        <div>厦门市集美区集美大学计算机工程学院</div>
        <div>@jmu201421121047</div>
    </div>
</div>

</body>
</html>