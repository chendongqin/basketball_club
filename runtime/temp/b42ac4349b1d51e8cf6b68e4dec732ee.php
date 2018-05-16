<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:81:"D:\phpstudy\WWW\basketballClub\public/../application/index\view\index\event.phtml";i:1523251982;s:66:"D:\phpstudy\WWW\basketballClub\application\index\view\layout.phtml";i:1523168499;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:44:"../application/index/view/public/pager.phtml";i:1525152262;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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

<link rel="stylesheet" type="text/css" href="/static/css/index/event.css" />
<script type="text/javascript" src="/static/js/jq.js"></script>
<script type="text/javascript" src="/static/js/index/index/event.js"></script>

<div class="event-seacher">
    <div class="seacher-logo"><img src="/static/image/logo.png" height="70px"></div>
    <div class="seacher-select">
        <ul>
            <li>
                <select id="areaSearch">
                    <option value="" <?php echo empty($myarea)?'selected':'';?>>地区</option>
                    <?php foreach ($areas as $key=>$value):?>
                        <option value="<?php echo $value?>" <?php echo $myarea == $value?'selected':'';?>><?php echo $value?></option>
                    <?php endforeach;?>
                </select>
            </li>
            <li>
                <select id="typeSearch">
                    <option value="-1" <?php echo $mytype == -1?'selected':'';?>>类型</option>
                    <?php foreach ($types as $key=>$value):?>
                        <option value="<?php echo $key?>" <?php echo $mytype == $key?'selected':'';?>><?php echo $value?></option>
                    <?php endforeach;?>
                </select>
            </li>
            <li>
                <select id="statusSearch">
                    <option value="-1" <?php echo $mystatus == -1?'selected':'';?>>状态</option>
                    <?php foreach ($status as $key=>$value):?>
                        <option value="<?php echo $key?>" <?php echo $mystatus == $key?'selected':'';?>><?php echo $value?></option>
                    <?php endforeach;?>
                </select>
            </li>
        </ul>
    </div>
    <div class="seacher-text">
        <div class="search-bk">
            <form action="" method="get">
                <input type="hidden" value="" id="formType" name="type">
                <input type="hidden" value="" id="formArea" name="area">
                <input type="hidden" value="" id="formStatus" name="status">
                <input type="text" name="name"  class="search-text-1">
            </form>
        </div>
    </div>
</div>
<?php if(!empty($pager['data'])):foreach ($pager['data'] as $key =>$value):if($key%2 ==0):?>
            <div class="event-list">
                <div class="list-image">
                    <img src="/static/image/event/kobe_left1.png" height="300px">
                </div>
                <div class="list-main">
                    <div class="list-main-top">
                        <img src="<?php echo empty($value['posters'])?'':$value['posters'];?>" >
                    </div>
                    <div class="list-main-silder-r">
                        <ul>
                            <li id="event-hot"><?php echo $showStatus[$value['status']];?><span class="join-num">参赛队伍<?php echo count(json_decode($value['join_clubs'],true));?></span></li>
                            <li><?php echo $value['name'];?></li>
                            <li><?php echo $types[$value['type']];?></li>
                            <li><?php echo $value['address'];?></li>
                        </ul>
                    </div>
                    <div class="list-main-bottom-l">
                        <img src="/static/image/small/time.png" width="32px"/><span><?php echo $value['start_time'].' 至 '.$value['end_time'];?></span>
                    </div>
                    <div class="list-main-bottom-r">
                        <a href="/index/event?id=<?php echo $value['Id'];?>">详情查看</a>
                    </div>
                </div>
                <div class="list-image">
                    <img src="/static/image/event/jordan_right1.png" height="300px" class="list-image-right">
                </div>
            </div>
        <?php else:?>
            <div class="event-list">
                <div class="list-image">
                    <img src="/static/image/event/jordan_left1.png" height="300px">
                </div>
                <div class="list-main">
                    <div class="list-main-top">
                        <img src="<?php echo empty($value['posters'])?'':$value['posters'];?>" >
                    </div>
                    <div class="list-main-silder-r">
                        <ul>
                            <li id="event-hot"><?php echo $showStatus[$value['status']];?><span class="join-num">参赛队伍<?php echo count(json_decode($value['join_clubs'],true));?></span></li>
                            <li><?php echo $value['name'];?></li>
                            <li><?php echo $types[$value['type']];?></li>
                            <li><?php echo $value['address'];?></li>
                        </ul>
                    </div>
                    <div class="list-main-bottom-l">
                        <img src="/static/image/small/time.png" width="32px"/><span><?php echo $value['start_time'].' 至 '.$value['end_time'];?></span>
                    </div>
                    <div class="list-main-bottom-r">
                        <a href="/index/event?id=<?php echo $value['Id'];?>">详情查看</a>
                    </div>
                </div>
                <div class="list-image">
                    <img src="/static/image/event/kobe_right1.png" height="300px" class="list-image-right">
                </div>
            </div>
        <?php endif;endforeach;?>
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
<?php else:?>
<div class="nothing">
    <div class="nothing-top"><span>没有找到赛事</span></div>
    <div class="nothing-bottom">
        <a href="/user/event/add">前往添加</a>
    </div>
</div>
<?php endif;?>




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