<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:81:"D:\phpstudy\WWW\basketballClub\public/../application/index\view\index\clubs.phtml";i:1526210097;s:66:"D:\phpstudy\WWW\basketballClub\application\index\view\layout.phtml";i:1523168499;s:45:"../application/index/view/public/header.phtml";i:1525752071;s:44:"../application/index/view/public/pager.phtml";i:1525152262;s:45:"../application/index/view/public/footer.phtml";i:1523070513;}*/ ?>
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
<link rel="stylesheet" type="text/css" href="/static/css/index/clubs.css" />
<script type="text/javascript" src="/static/js/jq.js"></script>
<script type="text/javascript" src="/static/js/index/model.js"></script>
<script type="text/javascript" src="/static/js/index/index/clubs.js"></script>
<script type="text/javascript" src="/static/js/index/area.js"></script>

<div class="main">
    <div class="add">
        <a href="<?php echo !empty($user)?'javascript:;':'/user/login';?>" id="<?php echo isset($user)?'addClub':'';?>">创建球队</a><br>
    </div>
</div>
        <?php if(!empty($pager['data'])):foreach ($pager['data'] as $key=>$club):if($key % 2==0):?>
                    <div class="club-list">
                    <?php if( $key == count($pager['data'])-1):?>
                        <div class="club-list-c">
                    <?php endif;?>
                            <div class="club-list-l">
                <?php else:?>
                    <div class="club-list-r">
                <?php endif;?>
                        <div class="club-list-img"><img src="<?php echo empty($club['mark'])?'':$club['mark'];?>"><span class="club-title"><?php echo $club['name'];?></span></div>
                   <div  class="club-list-ul" role="club" data-captain="<?php echo $club['captain'];?>">
                        <ul>
                            <li><strong>队长：</strong><span class="captainName"></span></li>
                            <li><strong><?php echo $club['area'];?></strong></li>
                            <li>
                                <?php $players = json_decode($club['players'],true);?>
                                <span class="grey"><?php echo count($players);?></span><strong>人</strong>
                            </li>
                        </ul>
                   </div>
                    <div class="into-right">
                        <?php if(!empty($user) and isset($players[$user['Id']])):?>
                            <a href="/user/club?id=<?php echo $club['Id'];?>">进入球队</a>
                        <?php else:?>
                            <a href="<?php echo !empty($user)?'javascript:;':'/user/login';?>" class="joinClub" data-id="<?php echo $club['Id'];?>">加入球队</a>
                        <?php endif;?>
                    </div>
                </div>
                <?php if( $key == count($pager['data'])-1):?>
                      </div><!-- list-c结束-->
                <?php endif;if($key % 2==1 or $key==count($pager['data'])-1 ):?>
                    </div><!-- list结束-->
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
            <div class="nothing-clubs">
                <span>没有找到球队</span>
            </div>
        <?php endif;?>
<!--</div><!-- main结束-->


<div class="modal fade" id="modelAddClub">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">创建球队</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-groups-bordered form-static" id="fauditingForm">
                    <label class="am-color" for="name">赛事名称：</label>
                    <input type="text" placeholder="请输入队伍名称" name="name" id="name">
                    <br />
                    <label class="am-color" for="areas">地区：</label>
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
                    <br />
                    <label class="am-color" for="code">邀请码：</label>
                    <input type="text" placeholder="设置邀请码" name="code" id="code">
                    <br />
                    <label class="am-color" for="name">队伍标志：</label>
                    <input type="file" name="clubMark"  id="clubMark">
                    <input type="hidden" id="mark" name="mark" value="">
                    <br/>
                    <img src="" width="100" id="markImg"><br />
                </form>
            </div>
            <div class="modal-footer">
                <input type="button"  id="addClubButton" value="创建" >
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modelJoinClub">
    <div class="modal-dialog custom-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">加入球队</h4>
            </div>
            <div class="error" style="display: none;color: red">
                <span class="tc_error"></span>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-groups-bordered form-static" id="joinClubForm">
                    <textarea placeholder="输入邀请码或理由" name="modal_string" id="modal_string"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <input type="button"  id="joinClubButton" value="加入" >
                <input type="button"  id="applyJoinButton" value="申请加入" >
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