<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:79:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\game\index.phtml";i:1526233800;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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
<script type="text/javascript" src="/static/js/jq.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/model.css" />
<script type="text/javascript" src="/static/js/index/model.js"></script>
<script type="text/javascript" src="/static/js/user/game/index.js"></script>
<div class="header__info">
    <div class="header__team-name home-name"><?php echo $home['name'];?></div>
    <div class="header__team-score home-score"><?php echo $schedule['home_score'];?></div>
    <div class="header__time-box fl">
        <div class="header__picth"><?php echo $schedule['section']>4?'第'.($schedule['section']-4).'加时':'第'.$schedule['section'].'节'?></div>
        <div class="header__time"><?php echo (int)($schedule['second']/60);?>:<?php echo str_pad($schedule['second']%60,2,0);?></div>
    </div>
    <div class="header__team-name away-name"><?php echo $visiting['name'];?></div>
    <div class="header__team-score away-score"><?php echo $schedule['visiting_score'];?></div>
</div>
<div class="detail__box">
    <div class="detail__team fl">
        <div class="detail__team-name"><?php echo $home['name'];?></div>
        <form action="" method="get" class="lineup__form">
            <?php foreach ($homePlayers as $key=>$player):?>
                <div class="detail__team-item">
                    <?php if($playerStatus[$key]['is_playing']==1):?>
                        <input type="radio" name="homePlayer" value="<?php echo $key;?>" />
                        <?php echo $player.' #'.$homeNo[$key];endif;?>
                </div>
            <?php endforeach;?>
            <div class="detail__team-item">
                <input type="reset" class="detail__btn" value="重置">
            </div>
        </form>
    </div>
    <div class="detail__character">
        <?php if(!empty($logs)):foreach ($logs as $log):?>
                <p><?php echo $log;?></p>
            <?php endforeach;endif;?>
    </div>
    <div class="detail__team fr">
        <div class="detail__team-name"><?php echo $visiting['name'];?></div>
        <form action="" method="get"  style="text-align: right;">
            <?php foreach ($visitingPlayers as $key=>$player):?>
                <div class="detail__team-item">
                    <?php if($playerStatus[$key]['is_playing']==1):?>
                        <?php echo $player.' #'.$visitingNo[$key];?>
                        <input type="radio" name="awayPlayer" value="<?php echo $key;?>" />
                    <?php endif;?>
                </div>
            <?php endforeach;?>
            <div class="detail__team-item">
                <input type="reset" class="detail__btn" value="重置">
            </div>
        </form>
    </div>
</div>
<div class="schedule_id" style="display:none;"><?php echo $schedule['Id'];?></div>
<div class="acting" style="display:none;"><?php echo $schedule['acting'];?></div>
<div class="detail__btn-group">
    <select class="detail__select">
        <option value ="1">主队</option>
        <option value ="0">客队</option>
    </select>
    <span class="detail__btn replace">换人</span>
    <span class="detail__btn" id="j-returnBack">撤销</span>
    <span class="detail__btn start_to_stop" data-type="1">暂停</span>
    <span class="detail__btn start_to_stop" data-type="0" id="j-start"><?php echo $schedule['acting']==1?'死球停止':'开始';?></span>
    <?php if($schedule['second']==0):?>
        <span class="detail__btn" id="j-next"><?php echo ($schedule['section']+1)>4?'进入第'.($schedule['section']-3).'加时':'进入第'.($schedule['section']+1).'节'?></span>
    <?php endif;if($schedule['second']==0 and $schedule['section']>=4):?>
        <span class="detail__btn" id="j-over">结束比赛</span>
    <?php endif;?>
    <span class="detail__btn" id="out">退出直播</span>
    <br>
    <span class="detail__btn double_score" data-type="0">中投命中</span>
    <span class="detail__btn double_score" data-type="1">突破命中</span>
    <span class="detail__btn double_score" data-type="2">篮下命中</span>
    <span class="detail__btn double_score" data-type="3">补篮命中</span>
    <span class="detail__btn three_score" data-type="1">三分命中</span>
    <span class="detail__btn one_score" data-type="1">罚球命中</span>
    <br>
    <span class="detail__btn double_score" data-type="4">中投未中</span>
    <span class="detail__btn double_score" data-type="5">上篮未中</span>
    <span class="detail__btn double_score" data-type="6">篮下未中</span>
    <span class="detail__btn double_score" data-type="7">补篮未中</span>
    <span class="detail__btn three_score" data-type="0">三分未中</span>
    <span class="detail__btn one_score" data-type="0">罚球未中</span>
    <br>
    <span class="detail__btn assists" data-type="0">助攻</span>
    <span class="detail__btn assists" data-type="1">三分助攻</span>
    <span class="detail__btn" id="j-lost">失误</span>
    <span class="detail__btn" id="j-rebounds">篮板</span>
    <span class="detail__btn" id="j-steals">抢断</span>
    <span class="detail__btn blocks" data-type="0">盖帽</span>
    <span class="detail__btn blocks" data-type="1">三分盖帽</span>
    <br>
    <span class="detail__btn faul" data-type="0">普通犯规</span>
    <span class="detail__btn faul" data-type="1">犯规罚球</span>
    <span class="detail__btn faul" data-type="4">三分犯规</span>
    <span class="detail__btn faul" data-type="2">2+1</span>
    <span class="detail__btn faul" data-type="3">3+1</span>
</div>

<div class="modal fade" id="modelAssist">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">选择被助攻球员</h4>
            </div>
            <div id="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <div class="detail__team1">
                    <ul style="text-align: left;margin:0;padding:0" id="j-get-player">
                    </ul>
                    <div class="detail__team-item">
                        <input type="button" class="detail__btn" id="j-assists" value="确定" data-dismiss="modal" aria-hidden="true">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modelReplace">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">换人</h4>
            </div>
            <div id="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <div class="detail__team1">
                    <ul style="text-align: left;margin:0;padding:0" id="j-replace-player">
                    </ul>
                    <div class="detail__team-item">
                        <input type="button" class="detail__btn" id="j-replace" value="确定" data-dismiss="modal" aria-hidden="true">
                    </div>
                </div>
            </div>
        </div>
    </div>
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