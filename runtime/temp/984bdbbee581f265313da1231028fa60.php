<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\game\setstart.phtml";i:1525577656;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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

<link rel="stylesheet" type="text/css" href="/static/css/user/game/setstart.css" />
<script type="text/javascript" src="/static/js/jq.js"></script>
<script type="text/javascript" src="/static/js/user/game/setstart.js"></script>
<h3 class="header__title">首发设置</h3>
<div class="lineup__list">
    <div class="lineup__item">
        <div class="lineup__name">[主队]<?php echo $home['name'];?></div>
        <form action=""  method="get" class="lineup__form">
            <?php $i=0;foreach ($homePlayers as $key=>$player):?>
                <div class="lineup__form-item">
                    <input type="checkbox" name="homePlayer" value="<?php echo $key;?>" <?php echo $i>4?'':'checked';?> /> <?php echo $player.' #'.$homeNo[$key];?>
                </div>
                <?php $i++;endforeach;?>
        </form>
    </div>
    <div class="lineup__item" >
        <div class="lineup__name">[客队]<?php echo $visiting['name'];?></div>
        <form action="" method="get" class="lineup__form">
            <?php $i=0;foreach ($visitingPlayers as $key=>$player):?>
                <div class="lineup__form-item">
                    <input type="checkbox" name="visitingPlayer" value="<?php echo $key;?>" <?php echo $i>4?'':'checked';?> /> <?php echo $player.' #'.$visitingNo[$key];?>
                </div>
                <?php $i++;endforeach;?>
        </form>
    </div>
</div>
<div class="lineup__btn-group">
    <span class="lineup__btn" id="setStartButton" data-id="<?php echo $schedule['Id'];?>">提交</span>
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