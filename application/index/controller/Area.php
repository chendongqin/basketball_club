<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/10
 * Time: 0:18
 */
namespace  app\index\controller;
use base\Base;
use think\Db;
class Area extends Base{

    //获取县区
    public function index(){
        $cityId = $this->request->param('cityId','','int');
        $areas = Db::name('areas')->where(array('cityid'=>$cityId))->select();
        $data = array();
        foreach ($areas as $area){
            $data[$area['areaid']] =$area['area'];
        }
        return $this->returnJson('获取成功',true,1,$data);
    }

    //获取市
    public function city(){
        $provinceId = $this->request->param('provinceId','','int');
        $cities = Db::name('cities')->where(array('provinceid'=>$provinceId))->select();
        $data = array();
        foreach ($cities as $city){
            $data[$city['cityid']] =$city['city'];
        }
        return $this->returnJson('获取成功',true,1,$data);
    }


}