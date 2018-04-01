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
        $this->assign('title','用户中心');
        return $this->fetch('index');
    }

    public function head(){

    }


    //身份验证
    public function virefy(){
        $request = $this->request;
        $id = $request->param('id','','int');
        $userModel = Db::name('user');
        $user = $userModel->where(array('Id'=>$id))->find();
        if(empty($user)){
            return $this->returnJson('用户不存在');
        }
        $realName = $request->param('realName','','string');
        $idcard = $request->param('idcard','','string');
        if(empty($realName) or empty($idcard)){
            return $this->returnJson('身份证或号码不能为空');
        }
        $virefy = Db::name('user')->where(['name'=>$realName,'idcard'=>$idcard])->find();
        if(!empty($virefy))
            return $this->returnJson('用户已经进行过个人身份认证');
        $conf = Config::get('hfwapi');
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
        $user = $this->getUser();
        $clubIds = json_decode($user['club'],true);
        $in = implode(',',$clubIds);
        $clubs = Db::name('club')->where('Id','in',$in)->select();
//        foreach ($clubs  as $key=>$club){
//            $players = json_decode($club['players'],true);
//            $userId = array_keys($players);
//            $in = implode(',',$userId);
//            $players = Db::name('user')->where('Id','in',$in)->select();
//            $clubs[$key]['players'] = $players;
//        }
        $this->assign('clubs',$clubs);
        return $this->fetch();
    }

    public function createClub(){
        $user = $this->getUser();
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
            $string = array_merge(range(0,9),range('A','Z'));
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
        $club[] = $clubId;
        $updateUser = ['Id'=>$user['Id'],'club'=>json_encode($club)];
        $upRes = Db::name('user')->update($updateUser);
        if(!$upRes){
            Db::rollback();
            return $this->returnJson('创建球队,加入球队失败');
        }
        Db::commit();
        return $this->returnJson('创建球队成功',true,1);
    }

}