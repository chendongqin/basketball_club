<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/13
 * Time: 10:50
 */
namespace  app\admin\controller;
use base\Adminbase;
use think\Db;
use think\Config;

class User extends Adminbase{


    public function index(){
        $name = $this->request->param('name','','string');
        $ban = $this->request->param('ban',0,'int');
        $this->assign('name',$name);
        $this->assign('ban',$ban);
        $where = ['ban'=>$ban];
        if(!empty($name))
            $where['name'] = $name;
        $users = Db::name('user')->where($where)->paginate(15,false)->toArray();
        $this->assign('pager',$users);
        return $this->fetch();

    }

    public function data(){
        $userId = $this->request->param('userId',0,'int');
        if(empty($userId)){
           return $this->returnJson('用户不存在');
        }
        $user = Db::name('user')->where(array('Id'=>$userId))->find();
        if(empty($user)){
            return $this->returnJson('用户不存在');
        }
        unset($user['password']);
        $user['ban'] == 0?'正常用户':'禁用用户';
        return $this->returnJson('获取成功',true,1,$user);
    }

   /* private $xing = ['陈','赵','李','刘','王','黄','陆','伍','高','杨','曾','任','何','肖','范'];
    private $ming1 = '煦、顼、瑄、妍、嫣、熙、曦、熹、晞、歆、馨、馥、笑、潇、越、悦、瑶、翼、奕、婳、瑾、婧、姁、珂、晗、毓、瑛、雅、赟、蕴、嫱、婵、娉、嫒 、嬟、妙、岚、澜、婉、枫、芬、昭、珊、琀、翚、嫽、琳、霖、翎、昕、璇、绚、霏、清';
    private $ming2 = '尧炫、贤言、幽嘉、月拂、秀佑、敬佑、昊瀚、航国、航方、佑阳、辰诚、文宁、钦豪、继海、豪庆、建康、航望、钦聪、敬腾、航启、钦浩、昌豪、贤思、敬超、星隆、君庆、钦余、航方08、宏敬、贤玮、辰腾、敬辰、冠先、晨章、皓轩、江顺、晨腾、贤瀚、彬瀚、辰啸11、彬晨、波延、贤宏、幽玮、继阳、航辰、皓伟、继睿、乐鸿、乐庆、昊隆、航华、皓远、昌鸿、伯星、乐睿15、皓遥、尚均、皓宁、晨秀、敬佑、昌浩、嘉海、江聪、昊隆、晨耀、敬章、明谦18、林豪、晨叶、月逸、尚瑜、尚坤、晨凡、晨泽、冠亨、信庆、茂明、文先、豪豪';


    public function systemAdd(){
        $ming1 = explode('、',$this->ming1);
        $ming2 = explode('、',$this->ming2);
        $emailStr = 'test@qq.com';
        for ($i=0;$i<50;$i++){
            shuffle($this->xing);
            $name = $this->xing[0];
            $tmp = mt_rand(1,2);
            if($tmp==1){
                shuffle($ming1);
                $name .= $ming1[0];
            }
            else{
                shuffle($ming2);
                $name .= $ming2[0];
            }
            $email = 'a'.str_pad($i,3,"0",STR_PAD_LEFT).$emailStr;
            $password = sha1('abc123'.substr($email,0,4));
            $idcard = 3508 .(string)mt_rand(10,30);
            $idcard .= mt_rand(1975,2000);
            $idcard .= str_pad(mt_rand(1,12),2,"0",STR_PAD_LEFT);
            $idcard .= str_pad(mt_rand(1,28),2,"0",STR_PAD_LEFT);
            $idcard .= mt_rand(1001,9999);
            $add=[
                'name'=>$name,'email'=>$email,
                'certification'=>1,'idcard'=>$idcard,'password'=>$password
            ];
            $res = Db::name('user')->insert($add);
            if(!$res){
                return false;
            }
        }
    }*/

}