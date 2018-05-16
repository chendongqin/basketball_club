<?php if (!defined('THINK_PATH')) exit(); /*a:7:{s:80:"D:\phpstudy\WWW\basketballClub\public/../application/admin\view\user\index.phtml";i:1526282020;s:66:"D:\phpstudy\WWW\basketballClub\application\admin\view\layout.phtml";i:1520904787;s:45:"../application/admin/view/public/header.phtml";i:1520910917;s:46:"../application/admin/view/public/sidebar.phtml";i:1526281007;s:47:"../application/admin/view/public/userinfo.phtml";i:1520907425;s:44:"../application/index/view/public/pager.phtml";i:1525152262;s:45:"../application/admin/view/public/footer.phtml";i:1522509903;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="Neon Admin Panel" />
        <title><?php if (isset($title) && $title): ?><?php echo $title; else: ?><?php echo '来战吧后台管理系统'; endif; ?></title>
        <link rel="stylesheet" href="/adminAssets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css"  id="style-resource-1">
        <link rel="stylesheet" href="/adminAssets/css/font-icons/entypo/css/entypo.css"  id="style-resource-2">
        <link rel="stylesheet" href="/adminAssets/css/bootstrap.min.css"  id="style-resource-4">
        <link rel="stylesheet" href="/adminAssets/css/neon-core.min.css"  id="style-resource-5">
        <link rel="stylesheet" href="/adminAssets/css/neon-theme.min.css"  id="style-resource-6">
        <link rel="stylesheet" href="/adminAssets/css/neon-forms.min.css"  id="style-resource-7">
        <link rel="stylesheet" href="/adminAssets/css/bootstrapValidator.css"  id="style-resource-8">
        <link rel="stylesheet" href="/adminAssets/css/font-icons/font-awesome/css/font-awesome.min.css"  id="style-resource-9">
        <link rel="stylesheet" type="text/css" href="/adminAssets/plugins/datetimepicker/bootstrap-datetimepicker.min.css" />
        <link rel="stylesheet" type="text/css" href="/adminAssets/js/daterangepicker/daterangepicker-bs3.css" />
        <link rel="stylesheet" type="text/css" href="/adminAssets/style/custom/general-layout.css" />
        <link rel="stylesheet" type="text/css" href="adminAssets/style/custom/table-layout.css" />

        <script src="/adminAssets/js/jquery-1.11.0.min.js"></script>
        <script src="/adminAssets/js/bootstrapValidator.js"></script>
        <script type="text/javascript" charset="utf-8" src="/adminAssets/lib/ueditor/ueditor.config.js"></script>
        <script type="text/javascript" charset="utf-8" src="/adminAssets/lib/ueditor/ueditor.all.min.js"></script>
<!--         <script src="/assets/js/custom.js"></script>
        <script src="/assets/js/demo.js"></script>
 -->        <!--[if lt IE 9]><script src="http://themes.laborator.co/neon/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- TS1395716029: Neon - Responsive Admin Template created by Laborator -->
    </head>
    <body class="page-body" data-url="/">
        <div class="page-container page-width">

<link rel="stylesheet" type="text/css" href="/adminAssets/style/custom/neno/sidebar.css" />
<div class="sidebar-menu sidebar-width">
  <header class="logo-env">
    <div class="logo custom-logo-left">
      <a href="/admin/management"><img width="140" src="/assets/i/logo/logogif.gif" alt="" /></a>
    </div>
    <div class="sidebar-collapse custom-collapse-top">
      <a href="/admin/management" class="sidebar-collapse-icon with-animation"><i class="entypo-menu"></i></a>
    </div>
    <div class="sidebar-mobile-menu visible-xs">
      <a href="/admin/management" class="with-animation"><i class="entypo-menu"></i></a>
    </div>
  </header>
    <ul id="main-menu" class="">
        <li class="root-level">
            <a href="/admin/management/"><i class="entypo-users"></i><span>管理员列表</span></a>
        </li>
        <li class="root-level">
            <a href="/admin/event/"><i class="entypo-air"></i><span>赛事审核</span></a>
        </li>
        <li class="root-level">
            <a href="/admin/user/"><i class="entypo-users"></i><span>用户列表</span></a>
        </li>
    </ul>
</div>




<div class="main-content">
<div class="row">
    <div class="col-md-6 col-sm-8 clearfix">
        <ul class="user-info pull-left pull-none-xsm">
            <li class="profile-info dropdown">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="/adminAssets/images/avatar.jpg" alt="" class="img-circle" width="44">
                    <?php echo isset($admin['name'])?$admin['name']:'';?>
                </a>
                <ul class="dropdown-menu">
                        <li class="caret"></li>
                        <li>
                            <a href="javascript:;" class="passwordmodal">
                                <i class="entypo-lock"></i>
                                修改密码
                            </a>
                        </li>
                        <li>
                            <a href="/admin/logout/">
                                <i class="entypo-logout"></i>
                                退出
                            </a>
                        </li>
                    </ul>
            </li>
        </ul>
    </div>
    <div class="col-md-6 col-sm-4 clearfix hidden-xs">
        <ul class="list-inline links-list pull-right">
            <li>
                <a href="/admin/logout/">
                    用户退出 <i class="entypo-logout right"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
<hr>




<script type="text/javascript" src="/static/js/jq.js"></script>
<script type="text/javascript" src="/static/js/admin/event/index.js"></script>
<link rel="stylesheet" type="text/css" href="/adminStatic/style/custom/table-layout.css" />

<ol class="breadcrumb bc-3">
    <li>
        <a href="/admin/management"><i class="entypo-home"></i>首页</a>
    </li>
    <li class="active">
        <strong>用户列表</strong>
    </li>
</ol>

<h2>用户列表</h2>

<div class="dataTables_wrapper form-inline">
    <div class="row screen-sm">
        <div class="col-md-12 col-left">
            <div class="col-md-10" >
                <form action="" method="get" class="form-inline">
                    <div class="form-group">
                        <i class="entypo-search"></i>
                    </div>
                    <div class="form-group">
                        <label class="control-label">状态</label>
                        <select class="form-control"  name="ban">
                            <option value="0" <?php echo $ban == 0? 'selected':''?>>正常用户</option>
                            <option value="1" <?php echo $ban == 1? 'selected':''?>>禁用用户</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="name" value="<?php echo empty($name)?'':$name;?>" placeholder="请输入管理员名称" class="form-control">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="search">查询</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-striped custom-table-font-size">
        <thead class="th-color">
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>身份证</th>
            <th>邮箱</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody class="td-color" id="dataTable">
        <?php if(!empty($pager['data'])):foreach ($pager['data'] as $key=>$value):?>
                <tr>
                    <td><?php echo $value['Id'];?></td>
                    <td><?php echo $value['name'];?></td>
                    <td><?php echo $value['idcard'];?></td>
                    <td><?php echo $value['email'];?></td>
                    <?php if($value['ban']==0):?>
                        <td>正常</td>
                        <td><a href="javascript:;" class="btn btn-red  btn-xs  ban"  data-id="<?php echo $value['Id'];?>" data-ban="1">禁用</a></td>
                    <?php else:?>
                        <td>禁用</td>
                        <td><a href="javascript:;" class="btn btn-green  btn-xs  noban"  data-id="<?php echo $value['Id'];?>" data-ban="0">禁用</a></td>
                    <?php endif;?>
                </tr>
            <?php endforeach;else:?>
            <td colspan="17"><center>没有找到用户</center></td>
        <?php endif;?>
        </tbody>
    </table>
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
</div>

    <!-- 修改密码 start -->
        <div class="modal fade" id="alterPassword">
            <div class="modal-dialog custom-modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">修改密码</h4>
                    </div>
                    <div class="modal-body dealbag-modal-body">
                        <form class="form-horizontal form-groups-bordered form-static" id="passwordForm">
                            <div class="form-group" id="originalPassword">
                                <label for="alterTitle" class="col-md-4 control-label">原始密码</label>
                                <div class="col-md-5">
                                    <input type="password" name="oldpwd" id="oldpwd" class="form-control" placeholder="请输入原始密码">
                                </div>
                            </div>
                            <div class="form-group" id="newPassword">
                                <label for="alterTitle" class="col-md-4 control-label">新密码</label>
                                <div class="col-md-5">
                                    <input type="password" name="newpwd" id="newpwd" class="form-control" placeholder="请输入新密码">
                                </div>
                            </div>
                            <div class="form-group" id="renewPassword">
                                <label for="alterClearno" class="col-md-4 control-label">确认密码</label>
                                <div class="col-md-5">
                                    <input type="password" name="renewpwd" id="renewpwd" class="form-control" placeholder="请输入新密码">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-7">
                                <input type="button" name="savePassword" id="savePassword" value="保存" class="btn btn-default">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- 用户详情 end -->
        <script src="/adminAssets/js/gsap/main-gsap.js" id="script-resource-1"></script>
        <script src="/adminAssets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js" id="script-resource-2"></script>
        <script src="/adminAssets/js/bootstrap.js" id="script-resource-3"></script>
        <script src="/adminAssets/js/joinable.js" id="script-resource-4"></script>
        <script src="/adminAssets/js/resizeable.js" id="script-resource-5"></script>
        <script src="/adminAssets/js/neon-api.js" id="script-resource-6"></script>
        <script src="/adminAssets/js/neon-custom.js" id="script-resource-10"></script>
        <script src="/adminAssets/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
        <script src="/adminAssets/js/daterangepicker/daterangepicker.js"></script>
        <script src="/adminAssets/js/bootstrap-datepicker.js"></script>
        <script src="/adminAssets/js/bootstrap-colorpicker.min.js"></script>
        <script src="/adminAssets/js/bootstrap-timepicker.min.js"></script>
        <script src="/adminAssets/js/daterangepicker/moment.min.js"></script>
        <script src="/adminAssets/script/verify-defined.js"></script>
        <script src="/adminAssets/script/records-per-page.js"></script>
        <script src="/adminAssets/script/admin-user.js"></script>
        <script src="/adminAssets/js/daterangepicker/daterangepicker.js"></script>
    </body>
</html>
