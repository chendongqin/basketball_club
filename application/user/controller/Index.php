<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 15:20
 */
namespace app\user\controller;
use base\Userbase;
use think\Session;
use think\Db;
use think\Config;
use ku\Tool;
class Index extends Userbase{

    public function index(){
        $provinces = Db::name('provinces')->select();
        $proData = array();
        foreach ($provinces as $item){
            $proData[$item['provinceid']] = $item['province'];
        }
        $this->assign('provinces',$proData);
        $this->assign('title','用户中心');
        return $this->fetch('index');
    }

    public function save(){
        $user = $this->getUser();
        $weight = $this->request->param('weight','','string');
        $height = $this->request->param('height','','string');
        if(!is_numeric($weight) or !is_numeric($height))
            return $this->returnJson('参数错误，请检查');
        $address = $this->request->param('address','','string');
        $update = ['Id'=>$user['Id'],'weight'=>$weight,'height'=>$height,'city'=>$address];
        $res = Db::name('user')->update($update);
        if(!$res)
            return $this->returnJson('保存失败，请重试');
        Session::delete('user');
        $user = Db::name('user')->where('Id',$user['Id'])->find();
        Session::push('user',$user);
        return $this->returnJson('保存成功',true,1);
    }


    //身份验证
    public function virefy(){
        $user = $this->getUser();
        $request = $this->request;
        $userModel = Db::name('user');
        $realName = $request->param('realName','','string');
        $idcard = $request->param('idcard','','string');
        if(empty($realName) or empty($idcard)){
            return $this->returnJson('身份证或号码不能为空');
        }
        $virefy = Db::name('user')->where(['name'=>$realName,'idcard'=>$idcard])->find();
        if(!empty($virefy))
            return $this->returnJson('用户已经进行过个人身份认证');
        $conf = Config::get('basketball.hfwapi');
        $data = array(
          'realName'=>$realName,
          'cardNo'=>$idcard,
          'key'=>$conf['key'],
        );
        $tool = new Tool();
        $res = $tool::send($conf['url'],$data);
        if($res === false){
            return $this->returnJson('接口查询失败，请重试');
        }
        $res = json_decode($res,true);
        if($res['result']['isok'] === false){
            return $this->returnJson('身份证不匹配');
        }
        $user['name'] = $realName;
        $user['idcard'] = $idcard;
        $user['certification']=1;
        $updateRes = $userModel->update($user);
        if(!$updateRes){
            return $this->returnJson('更新数据库失败');
        }
        Session::delete('user');
        $user = Db::name('user')->where('Id',$user['Id'])->find();
        Session::push('user',$user);
        return $this->returnJson('验证通过',true,1);
    }

    public function club(){
        $page = $this->request->param('page',1,'int');
        $user = $this->getUser();
        $user = Db::name('user')->where('Id',$user['Id'])->find();
        Session::delete('user');
        Session::set('user',$user);
        $clubIds = json_decode($user['club'],true);
        if(!empty($clubIds))
            $in = implode(',',$clubIds);
        else
            $in = [];
        $clubs = Db::name('club')
            ->where('Id','in',$in)
            ->paginate(5,false,array('page'=>$page))
            ->toArray();
        $this->assign('pager',$clubs);
        return $this->fetch();
    }

    public function createClub(){
        $user = $this->getUser();
        if ($user['certification'] !=1)
            return $this->returnJson('身份未认证');
        $clubNum = Db::name('club')->where('create_user',$user['Id'])->count();
        if($clubNum >5)
            return $this->returnJson('每人至多只能创建五个队伍');
        $request = $this->request;
        $name = $request->param('name','','string');
        if(empty($name))
            return $this->returnJson('球队名字不能为空');
        $mark = $request->param('mark','','string');
        if(!empty($mark) and !is_file(PUBLIC_PATH.$mark))
            return $this->returnJson('上传的队标文件不存在');
        $code = $request->param('code','','string');
        if(empty($code)){
            $string = join('',array_merge(range(0,9),range('A','Z')));
            for ($i=0;$i<4;$i++)
                $code .= str_shuffle($string){0};
        }
        $area =  $request->param('areas','','string');
        $virefyName = Db::name('club')->where('name',$name)->find();
        if(!empty($virefyName))
            return $this->returnJson('队名已被使用');
        $players = json_encode([$user['Id']=>$user['name']]);
        $log = array();
        $log[] = date('Y-m-d H:i:s').'  '.$user['name'].'创建球队';
        Db::startTrans();
        $add = [
            'create_user'=>$user['Id'],'name'=>$name,
            'mark'=>$mark,'virefy_code'=>$code,
            'create_time'=>time(),'captain'=>$user['Id'],
            'area'=>$area,'log'=>'','players'=>$players,
            'log'=>json_encode($log),
            ];
        $res = Db::name('club')->insert($add);
        if(!$res)
            return $this->returnJson('创建球队失败');
        $clubId = Db::name('club')->getLastInsID();
        $club = json_decode($user['club'],true);
        $club = empty($club)?array():$club;
        array_push($club,$clubId);
        $updateUser = ['Id'=>$user['Id'],'club'=>json_encode($club)];
        $upRes = Db::name('user')->update($updateUser);
        if(!$upRes){
            Db::rollback();
            return $this->returnJson('创建球队,加入球队失败');
        }
        Db::commit();
        Session::delete('user');
        $user = Db::name('user')->where('Id',$user['Id'])->find();
        Session::push('user',$user);
        return $this->returnJson('创建球队成功',true,1);
    }

    public function joinClub(){
        $user =$this->getUser();
        if($user['certification']!==1)
            return $this->returnJson('您还未进行身份认证');
        $id = $this->request->param('id','','int');
        $code = $this->request->param('code','','string');
        $club  = Db::name('club')->where('Id',$id)->find();
        if(empty($club))
            return $this->returnJson('球队不存在');
        if(strcmp($code,$club['virefy_code'])!==0)
            return $this->returnJson('邀请码不正确');
        $players = json_decode($club['players'],true);
        if(isset($players[$user['Id']]))
            return $this->returnJson('已经加入球队，无需重复操作');
        $players[$user['Id']] = $user['name'];
        $logs = json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].' 加入球队';
        array_unshift($logs,$log);
        $update = ['Id'=>$id,'players'=>json_encode($players),'log'=>json_encode($logs)];
        Db::startTrans();
        $res = Db::name('club')->update($update);
        if(!$res)
            return $this->returnJson('加入失败，请重试');
        $join_clubs = json_decode($user['club'],true);
        if(empty($join_clubs))
            $join_clubs=array();
        array_push($join_clubs,$club['Id']);
        $userUpdate = ['Id'=>$user['Id'],'club'=>json_encode($join_clubs)];
        $res = Db::name('user')->update($userUpdate);
        if(!$res){
            Db::rollback();
            return $this->returnJson('加入失败，请重试');
        }
        Db::commit();
        Session::delete('user');
        $user = Db::name('user')->where('Id',$user['Id'])->find();
        Session::push('user',$user);
        return $this->returnJson('加入成功',true,1);
    }

    public function applyJoin(){
        $user =$this->getUser();
        if($user['certification']!==1)
            return $this->returnJson('您还未进行身份认证');
        $id = $this->request->param('id','','int');
        $reason = $this->request->param('reason','','string');
        $club  = Db::name('club')->where('Id',$id)->find();
        if(empty($club))
            return $this->returnJson('球队不存在');
        $virefy = Db::name('club_apply')->where(['user_id'=>$user['Id'],'club_id'=>$id])->find();
        if(!empty($virefy))
            return $this->returnJson('您已经申请过加入该球队，请不要重复操作');
        $add = ['user_id'=>$user['Id'],'user_name'=>$user['name'],'reason'=>$reason,'club_id'=>$id,'time'=>time()];
        Db::startTrans();
        $res = Db::name('club_apply')->insert($add);
        if(!$res)
            return $this->returnJson('申请失败，请重试!');
        $logs = json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].' 申请加入球队';
        array_unshift($logs,$log);
        $update = ['Id'=>$club['Id'],'log'=>json_encode($logs)];
        $res = Db::name('club')->update($update);
        if(!$res){
            Db::rollback();
            return $this->returnJson('申请失败，请重试!');
        }
        Db::commit();
        return $this->returnJson('申请成功',true,1);
    }

    public function outClub(){
        $clubId = $this->request->param('id',0,'int');
        $club = Db::name('club')->where('Id',$clubId)->find();
        if(empty($club))
            return $this->returnJson('球队不存在');
        $user = $this->getUser();
        if($user['Id']===$club['captain'])
            return $this->returnJson('队长无法退出');
        $players = (array)json_decode($club['players'],true);
        if(!isset($players[$user['Id']]))
            return $this->returnJson('系统错误');
        unset($players[$user['Id']]);
        $logs = (array)json_decode($club['log'],true);
        $log = date('Y-m-d H:i:s').' '.$user['name'].' 退出球队';
        array_unshift($logs,$log);
        $cUpdate = ['Id'=>$club['Id'],'log'=>json_encode($logs),'players'=>json_encode($players)];
        $userClubs = (array)json_decode($user['club'],true);
        $key = array_search($clubId,$userClubs);
        if($key===false)
            return $this->returnJson('系统错误');
        unset($userClubs[$key]);
        $uUpdate = ['Id'=>$user['Id'],'club'=>json_encode($userClubs)];
        Db::startTrans();
        $cRes = Db::name('club')->update($cUpdate);
        if(!$cRes)
            return $this->returnJson('退出失败');
        $uRes = Db::name('user')->update($uUpdate);
        if(!$uRes){
            Db::rollback();
            return $this->returnJson('退出失败');
        }
        Db::commit();
        Session::delete('user');
        $user = Db::name('user')->where('Id',$user['Id'])->find();
        Session::push('user',$user);
        return $this->returnJson('退出成功',true,1);
    }

}