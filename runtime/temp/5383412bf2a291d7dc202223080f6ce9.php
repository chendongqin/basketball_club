<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:80:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\index\index.phtml";i:1525440566;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:48:"../application/index/view/public/user-side.phtml";i:1525914418;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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
<script type="text/javascript" src="/static/js/user/index/index.js"></script>
<script type="text/javascript" src="/static/js/index/area.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/model.css" />
<script type="text/javascript" src="/static/js/index/model.js"></script>
<link rel="stylesheet" type="text/css" href="/static/css/user/index.css" />
<div class="main-user">
    <link rel="stylesheet" type="text/css" href="/static/css/user/side.css" />
<div id="user-side">
    <img src="/static/image/logo.png">
    <li><a href="/user">个人信息</a></li>
    <li><a href="/user/event">我的赛事</a></li>
    <li><a href="/user/index/club">我的球队</a></li>
    <li><a href="/index/game/detail?userId=<?php echo $user['Id'];?>">我的数据</a></li>
    <li><a href="/user/event/data">技术台</a></li>
<!--    <li><a href="/user/apply">消息通知</a></li>-->
    <div class="logo-title">
        <span>BasketballClubs</span>
    </div>
</div>
    <div class="user-content">
        <div class="user-top">
            <div class="div-img">
                <img src="<?php echo empty($user['head'])?'/uploads/user/default.jpg':$user['head'];?>" id="userHeadChange" width="200px">
                <input type="file" id="userHead" name="userHead" style="display:none">
            </div>
            <li>账号：<span style="color: gray"><?php echo $user['email']?></span></li>
            <?php if($user['certification'] === 1):?>
                <li>姓名：<?php echo $user['name']?></li>
                <li>身份证：<?php echo substr_replace($user['idcard'],'***********',4,11);?></li>
            <?php else:?>
                <li>姓名：    <input type="button" value="身份认证" id="idcardVirefy"></li>
            <?php endif;?>
        </div>
        <div class="user-bottom">
            <div class="user-form">
                <form name="saveUserForm" id="saveUserForm">
                    <label>身高：<input type="text" id="height" name="height" value="<?php echo $user['height']?>"></label>
                    <label>体重：<input type="text" id="weight" name="weight" value="<?php echo $user['weight']?>"></label>
                    <label>城市：<?php echo $user['city'];?>  <a href="javascript:;" id="changeCity">更换</a></label>
                    <input type="hidden" name="address" id="address" value="<?php echo $user['city'];?>">
                    <div class="changeCity" style="display:none">
                        <select  id="province" >
                            <option value="0">选择省区</option>
                            <?php foreach ($provinces as $key=>$value):?>
                                <option value="<?php echo $key;?>" ><?php echo $value;?></option>
                            <?php endforeach;?>
                        </select>
                        <select  id="city" >
                            <option value="0">选择市区</option>
                        </select>
                        <select  id="area" >
                            <option value="0">选择县区</option>
                        </select>
                    </div>
                    <a href="javascript:;" id="saveUser" >保存</a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="virefyUserModel">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">身份认证</h4>
            </div>
            <div id="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-groups-bordered form-static" id="virefyUserForm">
                    <div>
                        <label class="am-color" for="name">姓名：</label>
                        <input type="text"  name="model_name" id="model_name">
                    </div>
                    <div>
                        <label class="am-color" for="name">身份证号：</label>
                        <input type="text"  name="model_idcard" id="model_idcard">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button"  id="virefy" value="认证" >
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