<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/4/1
 * Time: 9:21
 */
namespace app\user\controller;
use base\Userbase;
use ku\Upload;
use think\Cache;
use ku\Tool;
use think\Db;
use think\Session;

class Uploads extends Userbase{

    public function clubmark(){
        //防止恶意上传操作
        $user = $this->getUser();
        $uploadTimes = (int)Cache::get(__CLASS__.__FUNCTION__.$user['Id']);
        if($uploadTimes === false){
            Cache::set(__CLASS__.__FUNCTION__.$user['Id'],1,3600*2);
        }else{
            Cache::set(__CLASS__.__FUNCTION__.$user['Id'],$uploadTimes+1,3600*2);
        }
        if($uploadTimes >10){
            return $this->returnJson('多次操作被限制');
        }
        $upload = new Upload();
        $upload->setFormName('clubMark');
        $result = $upload->exec();
        if(!$result){
            return $this->returnJson('文件未上传');
        }
        $path = $upload->path('/uploads/clubs/mark/');
        $upload->buildCode();
        $code = $upload->getRetval();
        $fileName = $path.$code['code'].'.'.$upload->getFileSuffix();
        $result = $upload->moveFile($fileName);
        if(!$result){
            return $this->returnJson('文件上传失败');
        }
        $res = Tool::uploadImage($fileName,$fileName);
        if(!$res){
            return $this->returnJson('图片重生成错误');
        }
        $fileName = str_replace(PUBLIC_PATH,'',$fileName);
        return $this->returnJson('上传成功',true,1,array('fileName'=>$fileName));
    }

    public function userhead(){
        //防止恶意上传操作
        $user = $this->getUser();
        $uploadTimes = (int)Cache::get(__CLASS__.__FUNCTION__.$user['Id']);
        if($uploadTimes === false){
            Cache::set(__CLASS__.__FUNCTION__.$user['Id'],1,3600*2);
        }else{
            Cache::set(__CLASS__.__FUNCTION__.$user['Id'],$uploadTimes+1,3600*2);
        }
        if($uploadTimes >10){
            return $this->returnJson('多次操作被限制');
        }
        $upload = new Upload();
        $upload->setFormName('userHead');
        $result = $upload->exec();
        if(!$result){
            return $this->returnJson('文件未上传');
        }
        $path = $upload->path('/uploads/user/');
        $upload->buildCode();
        $fileName = $path.$user['Id'].'.'.$upload->getFileSuffix();
        $result = $upload->moveFile($fileName);
        if(!$result){
            return $this->returnJson('文件上传失败');
        }
        $res = Tool::sizeImage($fileName,$fileName,150);
        if(!$res){
            return $this->returnJson('图片重生成错误');
        }
        $fileName = str_replace(PUBLIC_PATH,'',$fileName);
        if($fileName != $user['head']){
            $res = Db::name('user')->update(['Id'=>$user['Id'],'head'=>$fileName]);
            if(!$res)
                return $this->returnJson('更新数据库失败');
            Session::delete('user');
            $user = Db::name('user')->where('Id',$user['Id'])->find();
            Session::push('user',$user);
        }
        return $this->returnJson('上传成功',true,1,array('fileName'=>$fileName));
    }



}