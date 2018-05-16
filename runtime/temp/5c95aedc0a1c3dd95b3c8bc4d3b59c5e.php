<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:79:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\club\index.phtml";i:1525948300;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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
<script type="text/javascript" src="/static/js/user/club/index.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/user/club/index.css" />
<div class="main-club">
    <div class="club-left">
        <div class="club-title">
            <div class="title-img">
                <img src="<?php echo empty($club['mark'])?'/static/image/logo.png':$club['mark'];?>" height="150px">
            </div>
            <div class="title-text">
                <li>
                    <?php echo $club['name'];?>
                </li>
                <li>
                    <?php echo $club['area'];?>
                </li>
                <li>
                    队长：
                    <a href="/user/club/player?id=<?php echo $club['captain'];?>" id="captainName" data-id="<?php echo $club['captain'];?>"></a>
                    <?php if($user['Id'] == $club['captain']):?>
                        <button id="changeCaptain">更换队长</button>
                    <?php endif;?>

                </li>
            </div>
        </div>
        <div class="club-log">
            <h3>球队日志</h3>
            <?php $log =  json_decode($club['log'],true);foreach ($log as $value):?>
                <li><span style="color: grey"><?php echo $value;?></span></li>
            <?php endforeach;?>
        </div>
        <div class="club-apply">
            <h3>
                申请列表
                <?php if($user['Id'] == $club['captain']):?>
                    <span id="changeCode">(邀请码：<?php echo $club['virefy_code'];?>)</span>
                <?php endif;?>
            </h3>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>用户</th>
                    <th>申请理由</th>
                    <th>申请时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php if(!empty($applys)):foreach ($applys as $key=>$apply):?>
                        <thead>
                        <tr>
                            <td><?php echo $key+1;?></td>
                            <td><?php echo $apply['user_name'];?></td>
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
    <div class="club-right">
        <h3 style="color: honeydew;">队员列表</h3>
        <?php $nos = json_decode($club['players_no'],true);foreach ($players as $userId=>$name):?>
            <li>
                <span style="color: grey"><?php echo $userId==$club['captain']?'队长：':'队员：';?></span>
                <a href="/index/game/detail?userId=<?php echo $userId;?>"><span><?php echo $name.' '.$nos[$userId].'号';?></span></a>
                <?php if($user['Id']==$club['captain'] and $userId!=$club['captain']):?>
                    <a href="javascript:;" class="delPlaers" data-id="<?php echo $userId;?>">踢出</a>
                <?php endif;?>
            </li>
        <?php endforeach;?>
        <a href="javascript:;" id="changeNo" >更换号码</a>
    </div>

</div>

<div class="modal fade" id="modelChangeCaptain">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">更换队长</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form class="" id="changeCaptainForm">
                    <input type="hidden" id="clubId" value="<?php echo $club['Id'];?>">
                    <label >成员：</label>
                    <select id="playerId">
                        <?php foreach ($players as $key=>$player):?>
                            <option value="<?php echo $key?>"><?php echo $player;?></option>
                        <?php endforeach;?>
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button"  id="changeCaptainButton" value="更换" >
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modelChangeNo">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">更换号码</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form  method="post" class="" name="modelJishutaiForm" id="modelJishutaiForm">
                    <div>
                        <input type="text" name="changeNoValue" id="changeNoValue" value="<?php echo $nos[$user['Id']];?>">
                    </div>
                </form>
                <div class="modal-footer">
                    <input type="button"  value="更换" id="changeNoButton">
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