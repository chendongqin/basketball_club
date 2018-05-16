<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:81:"D:\phpstudy\WWW\basketballClub\public/../application/index\view\event\index.phtml";i:1526237169;s:66:"D:\phpstudy\WWW\basketballClub\application\index\view\layout.phtml";i:1523168499;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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

<link rel="stylesheet" type="text/css" href="/static/css/model.css" />
<script type="text/javascript" src="/static/js/jq.js"></script>
<script type="text/javascript" src="/static/js/index/model.js"></script>
<script type="text/javascript" src="/static/js/index/event/add_game.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/index/event/index.css" />
<div class="add">
    <a href="<?php echo !empty($user)?'javascript:;':'/user/login';?>" id="<?php echo isset($user)?'joinGame':'';?>">加入比赛</a><br>
</div>
<div class="event-main">
    <div class="event">
        <div class="event-text">
            <li><span><?php echo $event['name'];?></span></li>
            <li><span><?php echo $event['address'];?></span></li>
            <li><span><?php echo $types[$event['type']];?></span></li>
            <li><span>举办方：<?php echo $createUser['name'];?></span></li>
            <li><span>开始：<?php echo $event['start_time'];?></span></li>
            <li><span>结束：<?php echo $event['end_time'];?></span></li>
        </div>
        <div class="event-logo">
            <img src="<?php echo $event['posters'];?>" width="390px">
        </div>
        <div class="event-desc">
            <p>简介：</p>
            <p><?php echo $event['describe'];?></p>
        </div>
    </div>
    <div class="event-joins">
        <span>参赛队伍</span>
        <?php if(!empty($joinClubs)):foreach ($joinClubs as $key=>$joinClub):?>
            <li ><?php echo $joinClub;?></li>
        <?php endforeach;endif;?>
    </div>
</div>
<div class="schedule">
    <div class="schedule-title">赛程表</div>
    <table>
        <thead>
        <tr>
            <?php if($event['type']==0):?>
                <th>小组</th>
            <?php endif;?>
            <th>比赛时间</th>
            <th>主队</th>
            <th>客队</th>
            <th>比分</th>
            <th>操作</th>
        </tr>
        </thead>
        <?php if(!empty($schedules)):$group=range('A','Z');foreach ($schedules as $key=>$schedule):?>
                <thead>
                <tr>
                    <?php if($event['type']==0):?>
                        <td><?php echo is_int($schedule['group'])?$group[$schedule['group']]:$schedule['group'];?></td>
                    <?php endif;?>
                    <td><?php echo date('Y-m-d H:i:s',$schedule['game_time']);?></td>
                    <td><?php echo idOfFiler('club',['Id'=>$schedule['home_team']],'name')?></td>
                    <td><?php echo idOfFiler('club',['Id'=>$schedule['visiting_team']],'name')?></td>
                    <td><?php echo $schedule['over']==1?$schedule['home_sorce'].' : '.$schedule['visiting_sorce']:'未开赛';?></td>
                    <td>
                        <?php if($schedule['over']==1):?>
                            <a href="/index/game/index?id=<?php echo $schedule['Id'];?>">查看详情</a>
<!--                        --><?php //else:?>
<!--                            <a href="/user/game?id=--><?php //echo $schedule['Id'];?><!--">进入比赛</a>-->
                        <?php elseif($schedule['acting']!=0 or $schedule['acting']!=3):?>
                            <a href="/index/game/index?id=<?php echo $schedule['Id'];?>">查看直播</a>
                        <?php endif;?>
                    </td>
                </tr>
                </thead>
            <?php endforeach;endif;?>
    </table>
</div>

<div class="modal fade" id="modelAddGame">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">加入比赛</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form class="" id="joinGameForm">
                    <input type="hidden" name="eventId" id="eventId" value="<?php echo $event['Id'];?>">
                    <div>
                        <textarea  name="code" id="code" ></textarea>
                    </div>
                    <div class="chooseClub">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button"  id="addGameButton" value="加入" >
                <input type="button"  id="applyGameButton" value="申请" >
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