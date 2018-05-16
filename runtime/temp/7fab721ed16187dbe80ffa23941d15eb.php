<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\schedule\group.phtml";i:1526209071;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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


<script type="text/javascript" src="/static/js/jq.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/model.css" />
<script type="text/javascript" src="/static/js/index/model.js"></script>
<script type="text/javascript" src="/static/js/user/schedule/group.js"></script>
<style>
    .main{
        width:960px;
        margin:20px auto;
        text-align: center;
    }
    table{
        margin: auto;
    }
    td{
        text-align: center;
    }
    .main a:link,.main a:visited{
        text-decoration: none;
        text-align: center;
        color: #e8e8e8;
        display:inline-block;
        width:80px;
        line-height:30px;
        border: 3px green;
        border-radius: 15px;
        background: #ff665c;
    }
</style>
<div class="main">
<?php if(!empty($schedules)):foreach ($schedules as $key=>$schedule):?>
            <h3><?php echo $key;?>组赛程</h3>
            <table border="2px slider" >
                <tbody>
                    <tr>
                        <th>比赛时间</th>
                        <th>主队</th>
                        <th>客队</th>
                        <th>操作</th>
                    </tr>
                </tbody>
            <?php foreach ($schedule as $k=>$value):foreach ($value as $my=>$v):?>
                <tbody>
                    <tr>
                        <td><?php echo date('Y-m-d H:i:s',$v['game_time']);?></td>
                        <td><?php echo $joins[$v['home_team']];?></td>
                        <td><?php echo $joins[$v['visiting_team']];?></td>
                        <td><a href="javascript:;" class="alterSchedule" data-key="<?php echo $key;?>" data-k="<?php echo $k;?>" data-my="<?php echo $my;?>" >修改</a></td>
                    </tr>
                </tbody>
                <?php endforeach;endforeach;?>
            </table>
        <?php endforeach;else:?>
        <td >没有赛程</td>
    <?php endif;?>
    <button id="sureSchedules">确认赛程</button>
</div>
<div class="modal fade" id="modelSureSchedules">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">赛程导入</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form  method="post" class="" name="sureScheduleForm" id="sureScheduleForm">
                    <input type="hidden" name="id" id="eventId" value="<?php echo $event['Id'];?>">
                    <div>
                        <label >上半场暂停次数：</label>
                        <input type="text" id="firstStop" name="firstStop">
                    </div>
                    <div>
                        <label >下半场暂停次数：</label>
                        <input type="text" id="lastStop" name="lastStop">
                    </div>
                    <div>
                        <label >比赛地点：</label>
                        <input type="text" id="game_address" name="game_address">
                    </div>
                    <div>
                        <label >单节时间：</label>
                        <select id="sectionTime" name="sectionTime">
                            <option value="10">10分钟</option>
                            <option value="12">12分钟</option>
                        </select>
                    </div>
                </form>
                <div class="modal-footer">
                    <input type="button" id="actSureSchedules" value="确认赛程" >
                </div>
            </div>
        </div>
    </div>
</div>

<!--修改-->
<div class="modal fade" id="modelAlterSchedules">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">赛程修改</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form  method="post" class="" name="sureScheduleForm" id="sureScheduleForm">
                    <div>
                        <label >比赛时间：</label>
                        <input type="text" id="alter_game_time" name="alter_game_time">
                    </div>
                </form>
                <div class="modal-footer">
                    <input type="button" id="actAlterSchedules" value="确认修改" data-url="<?php echo uri(['cache'=>1])?>">
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