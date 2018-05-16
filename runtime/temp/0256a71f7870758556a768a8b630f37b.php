<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:79:"D:\phpstudy\WWW\basketballClub\public/../application/user\view\index\club.phtml";i:1523583009;s:65:"D:\phpstudy\WWW\basketballClub\application\user\view\layout.phtml";i:1520356497;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:48:"../application/index/view/public/user-side.phtml";i:1525914418;s:44:"../application/index/view/public/pager.phtml";i:1525152262;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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

<link rel="stylesheet" type="text/css" href="/static/css/user/event/index.css" />
<div class="main">
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
    <div class="event-list">
        <?php if(!empty($pager['data'])):foreach ($pager['data'] as $key=>$data):?>
                <div class="list-show">
                    <li><span><?php echo $data['name'];?></span ><span class="type">验证码：<?php echo $data['virefy_code'];?></span></li>
                    <li><?php echo $data['area'];?></span></li>
                    <li>球队人数：<span><?php echo count(json_decode($data['players'],true),true);?></span>人<a href="/user/club?id=<?php echo $data['Id'];?>">进入球队</a></li>
                </div>
            <?php endforeach;else:?>
            <div class="list-show nothing">
                <li><span>没有球队</span></li>
            </div>
        <?php endif;?>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/static/css/pager.css" />
<div class="pager">
    <div class="pager-total">总数：<span ><?php echo $pager['total'];?></span></div>
    <div class="pager-list">
        <?php if($pager['last_page']!=1):if($pager['current_page']!=1):?>
                <a href="<?php echo uri(['page'=>1]);?>">首页</a>
                <a href="<?php echo uri(['page'=>$pager['current_page']-1]);?>">上一页</a>
            <?php endif;if($pager['current_page']-2>=1):?>
                <a href="<?php echo uri(['page'=>$pager['current_page']-2]);?>"><?php echo $pager['current_page']-2;?></a>
                <a href="<?php echo uri(['page'=>$pager['current_page']-1]);?>"><?php echo $pager['current_page']-1;?></a>
            <?php elseif($pager['current_page'] == 2):?>
                <a href="<?php echo uri(['page'=>$pager['current_page']-1]);?>"><?php echo $pager['current_page']-1;?></a>
            <?php endif;?>
            <span><?php echo $pager['current_page'];?></span>
            <?php $end = $pager['last_page']-2>$pager['current_page']?$pager['current_page']+2:$pager['last_page'];for ($i=$pager['current_page']+1;$i<=$end;$i++):?>
                <a href="<?php echo uri(['page'=>$i]);?>"><?php echo $i;?></a>
            <?php endfor;if($pager['current_page']!=$pager['last_page']):?>
                <a href="<?php echo uri(['page'=>$pager['current_page']+1]);?>">下一页</a>
                <a href="<?php echo uri(['page'=>$pager['last_page']]);?>">尾页</a>
            <?php endif;endif;?>
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