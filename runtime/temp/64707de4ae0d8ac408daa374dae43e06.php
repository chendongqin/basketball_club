<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:85:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\event\management.phtml";i:1526216480;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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
<script type="text/javascript" src="/static/js/user/event/manage.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/user/event/manage.css" />
<link rel="stylesheet" type="text/css" href="/static/css/model.css" />
<script type="text/javascript" src="/static/js/index/model.js"></script>
<div class="title">
    <span><?php echo $event['name'];?></span>
    <div class="title-r">
    <?php if(empty($schedules)):if($event['type']===0):?>
                <a href="javascript:;" id="addSchedules" data-id="<?php $event['Id']?>">安排赛程</a>
                <a href="javascript:;" id="importSchedulesButton" data-id="<?php $event['Id']?>">导入赛程</a>
        <?php elseif($event['type']===2):?>
                <a href="/user/schedule/outSchedule" >安排赛程</a>
                <a href="javascript:;" id="importSchedulesButton" data-id="<?php $event['Id']?>">导入赛程</a>
        <?php endif;elseif($event['type']!==0):?>
            <a href="javascript:;" id="overGroup" data-id="<?php $event['Id']?>">下一轮</a>
    <?php endif;?>
        <a href="javascript:;" id="eventWork" data-id="<?php $event['Id']?>">直播管理</a>
    </div>
</div>
<div class="manage-center">
    <div class="event-alter">
<!--        <li>--><?php //echo $event['name'];?><!--</li>-->
        <li><?php echo $types[$event['type']];?></li>
        <li><input type="text" value="<?php echo $event['virefy_code'];?>" id="code"><button data-id="<?php echo $event['Id'];?>" id="changeCode">修改</button></li>
        <li><?php echo $event['address'];?></li>
        <li>开始：<input type="text" id="startTime" value="<?php echo $event['start_time'];?>"></li>
        <li>结束：<input type="text" id="endTime" value="<?php echo $event['end_time'];?>"><button data-id="<?php echo $event['Id'];?>" id="changeTime">修改</button></li>
    </div>
    <div class="event-apply">
        <h3>申请列表</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>球队</th>
                <th>申请理由</th>
                <th>申请时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <?php if(!empty($applys)):foreach ($applys as $key=>$apply):?>
                    <thead>
                    <tr>
                        <td><?php echo $key+1;?></td>
                        <td><?php echo $apply['clubName'];?></td>
                        <td><?php echo $apply['reason'];?></td>
                        <td><?php echo date('Y-m-d H:i:s',$apply['time']);?></td>
                        <td>
                            <a href="javascript:;" class="pass" data-id="<?php echo $apply['Id'];?>">通过</a>
                            <a href="javascript:;" class="refuse" data-id="<?php echo $apply['Id'];?>">拒绝</a>
                        </td>
                    </tr>
                    </thead>
                <?php endforeach;endif;?>
        </table>
    </div>
</div>
<div class="joins-table">
    <h3>参赛队伍</h3>
    <table>
        <tbody>
        <tr>
            <th>队徽</th>
            <th>队名</th>
            <th>队长</th>
            <th>人数</th>
            <th>地区</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </tbody>
        <?php if (!empty($joins)):foreach ($joins as $key =>$join):?>
                <tbody>
                <tr>
                    <td><img src="<?php echo $join['mark'];?>" width="100"></td>
                    <td><?php echo $join['name'];?></td>
                    <td><?php echo idOfFiler('user',['Id'=>$join['captain']]);?></td>
                    <td><?php echo count(json_decode($join['players'],true));?></td>
                    <td><?php echo $join['area'];?></td>
                    <td><?php echo date('Y-m-d H:i:s',$join['create_time']);?></td>
                    <td>
                        <?php if($event['status']===0):?>
                            <button data-eventId="<?php echo $event['Id'];?>" data-id="<?php echo $join['Id'];?>" class="delJoins">删除</button>
                        <?php endif;?>
                    </td>
                </tr>
                </tbody>
            <?php endforeach;else:?>
            <td colspan="16"><center>没有球队加入</center></td>
        <?php endif;?>
    </table>
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
                    <td><?php echo idOfFiler('club',['Id'=>$schedule['home_team']])?></td>
                    <td><?php echo idOfFiler('club',['Id'=>$schedule['visiting_team']])?></td>
<!--                    <td><span class="homeTeam" data-id=" --><?php //echo $schedule['home_team'];?><!--"></span></td>-->
<!--                    <td><span class="visitingTeam" data-id=" --><?php //echo $schedule['visiting_team'];?><!--"></span></td>-->
                    <td><?php echo $schedule['over']==1?$schedule['home_sorce'].' : '.$schedule['visiting_sorce']:'未开赛';?></td>
                    <td>
                        <?php if($schedule['over']==1):?>
                            <a href="/user/schedule/detail?id=<?php echo $schedule['Id'];?>">查看详情</a>
                        <?php else:?>
                            <a href="/user/game?id=<?php echo $schedule['Id'];?>">进入比赛</a>
                        <?php endif;?>
                    </td>
                </tr>
                </thead>
            <?php endforeach;endif;?>
    </table>
</div>

<div class="modal fade" id="modelSchendule">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">赛程安排</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                    <form  method="post" action="/user/schedule/group?id=<?php echo $event['Id'];?>" class="" name="scheduleForm" id="scheduleForm">
    <!--                    <input type="hidden" name="id" value="--><?php //echo $event['Id'];?><!--">-->
                        <div>
                            <label >比赛开始时间：</label>
                            <input type="text" id="bTime" name="bTime">
                        </div>
                        <div>
                            <label >比赛间隔时间：</label>
                            <input type="text" id="freeTime" name="freeTime">
                        </div>
                        <div>
                        <label >赛程安排：</label>
                            <select id="divideTime" name="divideTime">
                                <?php ;for ($i=1;$i<=6;$i++):?>
                                    <option value="<?php echo $i?>"><?php echo $i.'天一赛程';?></option>
                                <?php endfor;?>
                                <option value="7">1周一赛程</option>
                                <option value="14">2周一赛程</option>
                                <option value="30">1月一赛程</option>
                            </select>
                        </div>
                        <div>
                            <label >每组一日最多<input type="text" id="dayTimes" name="dayTimes">赛</label>
                        </div>
                        <div>
                            <label >分<input type="text" id="num" name="num">组</label>
                        </div>
                        <div>
                            <label >小组比赛是否同时进行：</label>
                            <select name="groupWith">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <input type="submit"  value="安排赛程" >
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modelImportSchendule">
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
                <form  method="post" action="/user/schedule/import?id=<?php echo $event['Id'];?>" class="" name="importScheduleForm" id="importScheduleForm" enctype="multipart/form-data">
                    <input type="file" name="importSchedules">
                    <div class="modal-footer">
                        <a href="/user/schedule/downTemp" class="c">模板下载</a>
                        <input type="submit"  value="导入赛程" >
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modelJishutai">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">技术台管理</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form  method="post" class="" name="modelJishutaiForm" id="modelJishutaiForm">
                    <input type="text" name="jstWorker" id="jstWorker" >
                    <input type="button"  value="添加" id="jstWorkerAdd" data-id="<?php echo $event['Id'];?>">
                </form>
                <?php foreach ($workers as $worker):?>
                    <p>
                        <?php echo $worker['name'];?>
                        <a href="javascript:;" class="worker-list" data-id="<?php echo $worker['Id'];?>" >删除</a>
                    </p>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="eventId" value="<?php echo $event['Id'];?>">



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