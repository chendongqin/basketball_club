<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:81:"D:\phpstudy\WWW\basketballClub\public/../application/index\view\index\index.phtml";i:1523001700;s:66:"D:\phpstudy\WWW\basketballClub\application\index\view\layout.phtml";i:1523168499;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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

<link rel="stylesheet" type="text/css" href="/static/css/index/index.css" />
<script type="text/javascript" src="/static/myfocus/myfocus-2.0.4.full.js"></script>
<div class="logo">
    <div class="logo-left">
        <img src="/static/image/logo.png"  width="90px" alt="logo"><span>BasketballClubs</span>
    </div>
    <div class="logo-right">
        <img src="/static/image/email.png"  width="50px"><span>我们的邮箱:</span><span>913294974@qq.com</span>
    </div>
</div>
<div class="ad-box">
    <div id="boxID">
        <!-- 载入中的Loading图片(可选) -->
        <div class="loading"><img src="/static/image/loading.gif" alt="请稍候..." /></div>
        <!-- 内容列表 -->
        <div class="pic">
            <ul>
                <li><a href="#"><img src="/static/image/ad/event.jpg" alt="赛事更简单"/></a></li>
                <li><a href="#"><img src="/static/image/ad/data.jpg" alt="数据更完整" /></a></li>
                <li><a href="#"><img src="/static/image/ad/team.jpg" alt="团队更灵活" /></a></li>
                <li><a href="#"><img src="/static/image/ad/games1.jpg" alt="比赛更精彩" /></a></li>
                <li><a href="#"><img src="/static/image/ad/players.jpg" alt="球员可发展" /></a></li>
                <li><a href="#"><img src="/static/image/ad/fans.jpg" alt="比赛随时看" /></a></li>
            </ul>
        </div>
    </div>
</div>
<div class="present">
    <div class="present-left">
        <div class="present-title">
            <img src="/static/image/small/qc.png" width="96px">
            <h2>赛事管理</h2>
        </div>
        <div class="present-content-1">
            <ul>
                <li>举办赛事一键提交</li>
                <li>赛程安排一键搞定</li>
                <li>消息通告一键发送</li>
                <li>比赛记录轻松操作</li>
                <li>比赛数据清晰明了</li>
            </ul>
        </div>
        <div class="present-content-2">
            <span>涵盖了举办小型赛事比赛的所需操作，利用互联网+的特点，轻松解决人为的繁琐处理。</span>
        </div>
    </div>
    <div class="present-main">
        <div class="present-title">
            <img src="/static/image/small/champion.png" width="96px">
            <h2>球队管理</h2>
        </div>
        <div class="present-content-1">
            <ul>
                <li>快速建队轻松拉人</li>
                <li>参加赛事一键处理</li>
                <li>球队动向清晰明了</li>
                <li>球员数据实时查看</li>
                <li>球队赛程随时了解</li>
            </ul>
        </div>
        <div class="present-content-2">
            <span>球队负责人可以通过途径加入比赛，邀请和处理申请的球员。球队动向在日志清晰记录，负责人还可以通过球员数据合理安排球队发展。</span>
        </div>
    </div>
    <div class="present-right">
        <div class="present-title">
            <img src="/static/image/small/player.png" width="96px">
            <h2>球员数据</h2>
        </div>
        <div class="present-content-1">
            <ul>
                <li>数据统计全面</li>
                <li>数据分析全面</li>
                <li>比赛及时应变</li>
                <li>数据实时查看</li>
                <li>历史数据可查</li>
            </ul>
        </div>
        <div class="present-content-2">
            <span>将球员的数据按照国际联赛的格式进行统计，每场比赛数据可查看，提高了数据的可保留性。</span>
        </div>
    </div>
</div>

<script>
    myFocus.set({
        id: 'boxID',
        autoZoom:true,
        pattern: 'mF_YSlider',//焦点图风格的名称
        time: 3,//切换时间间隔(秒)
        trigger: 'mouseover',//触发切换模式:'click'(点击)/'mouseover'(悬停)
        delay: 100,//'mouseover'模式下的切换延迟(毫秒)
        height:450,
        easing:'easeOutElastic',
        txtHeight: 'default'//标题高度设置(像素),'default'为默认CSS高度，0为隐藏
    });
</script>

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