<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:85:"D:\phpstudy\WWW\basketballClub\public/../application/index\view\game\playerdata.phtml";i:1527516713;s:66:"D:\phpstudy\WWW\basketballClub\application\index\view\layout.phtml";i:1523168499;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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
<link rel="stylesheet" type="text/css" href="/static/css/table.css" />
<script type="text/javascript" src="/static/js/jq.js"></script>
<script type="text/javascript" src="/static/js/index/game/player.js"></script>
<style>
    .players-data{
        width: 960px;
        margin:20px auto;
        text-align: center;
    }
</style>
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
<div class="players-data" >
    <h3><?php echo $home['name'];?></h3>
    <table>
        <thead>
            <tr>
                <th>姓名</th>
                <th>得分</th>
                <th>篮板</th>
                <th>助攻</th>
                <th>投篮</th>
                <th>三分</th>
                <th>罚球</th>
                <th>盖帽</th>
                <th>抢断</th>
                <th>失误</th>
                <th>犯规</th>
                <th>上场时间</th>
            </tr>
        </thead>
        <thead class="home-player">
        <?php foreach ($homeData as $value):?>
            <tr>
                <td>
                    <?php echo $value['is_playing']==1?'√':''?>
                    <?php echo idOfFiler('user',['Id'=>$value['user_id']]);?>
                </td>
                <td><?php echo $value['score'];?></td>
                <td><?php echo $value['rebounds'];?></td>
                <td><?php echo $value['assists'];?></td>
                <td><?php echo $value['hit'].'/'.$value['shoot'];?></td>
                <td><?php echo  $value['three_hit'].'/'.$value['three_shoot'];?></td>
                <td><?php echo $value['penalty_hit'].'/'.$value['penalty_shoot'];?></td>
                <td><?php echo $value['blocks'];?></td>
                <td><?php echo $value['steals'];?></td>
                <td><?php echo $value['lost'];?></td>
                <td><?php echo $value['foul'];?></td>
                <td><?php echo number_format($value['playing_time']/60,1,'.','');?></td>
            </tr>
        <?php endforeach;?>
        </thead>
    </table>
    <h3><?php echo $visiting['name'];?></h3>
    <table>
        <thead>
        <tr>
            <th>姓名</th>
            <th>得分</th>
            <th>篮板</th>
            <th>助攻</th>
            <th>投篮</th>
            <th>三分</th>
            <th>罚球</th>
            <th>盖帽</th>
            <th>抢断</th>
            <th>失误</th>
            <th>犯规</th>
            <th>上场时间</th>
        </tr>
        </thead>
        <thead class="visiting-player">
        <?php foreach ($visitingData as $value):?>
            <tr>
                <td>
                    <?php echo $value['is_playing']==1?'√':''?>
                    <?php echo idOfFiler('user',['Id'=>$value['user_id']]);?>
                </td>
                <td><?php echo $value['score'];?></td>
                <td><?php echo $value['rebounds'];?></td>
                <td><?php echo $value['assists'];?></td>
                <td><?php echo $value['hit'].'/'.$value['shoot'];?></td>
                <td><?php echo  $value['three_hit'].'/'.$value['three_shoot'];?></td>
                <td><?php echo $value['penalty_hit'].'/'.$value['penalty_shoot'];?></td>
                <td><?php echo $value['blocks'];?></td>
                <td><?php echo $value['steals'];?></td>
                <td><?php echo $value['lost'];?></td>
                <td><?php echo $value['foul'];?></td>
                <td><?php echo number_format($value['playing_time']/60,1,'.','');?></td>
            </tr>
        <?php endforeach;?>
        </thead>
    </table>
</div>
<input id="scheduleId" type="hidden" value="<?php echo $schedule['Id'];?>">

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