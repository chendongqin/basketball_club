<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/3/4
 * Time: 14:41
 */
namespace app\admin\controller;
use base\Adminbase;
class  Management extends Adminbase{

    public function index(){
        return $this->fetch('index');
    }

}