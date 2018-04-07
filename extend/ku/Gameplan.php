<?php
/**
 * Created by PhpStorm.
 * User: Viter
 * Date: 2018/4/3
 * Time: 21:02
 */
namespace ku;

class Gameplan {

    private $config = [
        'group'=>[
            'num'=>0,
            'dayTimes'=>0,
            'promotion'=>1,
        ],
    ];
    private $_group = array();

    private $_groupSchedules = array();

    public function setGroups($groups = []){
        $this->_group = $groups;
        return $this;
    }

    public function getGroups(){
        return $this->_group;
    }

    public function groupGame($joins = [],$config=[]){
        if(empty($joins))
            return null;
        $count = count($joins);
        if($count<2)
            return null;
        shuffle($joins);
        $battles = [];
        $config = array_merge( $this->config['group'],$config);
        if($config['num'] ===0){
            $num = $this->divideGroup($count,$config['promotion']);
        }else{
            $num = $config['num'];
        }
        $group = [];
        $key = 0;
        $average = $count/$num;
        for ($i = 0;$i<$num;$i++){
            if( $i==0 or $i==$num-1)
                $averageValue = ceil($average);
            else
                $averageValue = floor($average);
            $end = $key + $averageValue;
            $end = $end>$count?$count:$end;
            for ($j=$key;$j<$end;$j++){
                $group[$i][]= $joins[$key];
                $key ++;
            }
        }
        $this->setGroups($group);
        $schedules = [];
        foreach ($group as $data){
            $schedules[] = $this->groupSchedules($data,$config['dayTimes']);
            $this->_groupSchedules = null;
        }
        return $schedules;
    }
    public  function orderGame($joins = array()){

    }
    public  function outGame($joins = array()){
        if(empty($joins))
            return null;
        $count = count($joins);
        shuffle($joins);
        $battles = [];
        if($count % 2!==0)
            $count -=1;
        for ($i=0;$i<$count;$i=$i+2){
            $battles[] = ['home_team'=>$joins[$i],'visiting_team'=>$joins[$i+1]];
        }
        $join = end($joins);
        $battles[] = ['home_team'=>$join,'visiting_team'=>0];
        return $battles;
    }

    public function divideGroup($count,$promotion){
        $num = 1;
            if($count<4 )
                return $num;
            elseif ( $count<=8){
                $mod = $count%2;
                $num = $mod<=$promotion?floor($count/2):ceil($count/2);
            }
            elseif ($count<=16) {
                $mod = $count%4;
                $num = $mod<=$promotion?floor($count/4):ceil($count/4);
            }
            elseif($count<32){
                $mod = $count%6;
                $num = $mod<=$promotion?floor($count/6):ceil($count/6);
            }
            elseif ($count<=64){
                $mod = $count%8;
                $num = $mod<=$promotion?floor($count/8):ceil($count/8);
            }else{
                $num = floor(sqrt($count));
            }
            if($num % 2!=0){
                $numSub = $num-1;
                $numInner = $num +1;
                $num = round($count /$numInner)>$promotion?$numInner:$numSub;
            }
            return $num;
    }

    public function groupSchedules($groups = [],$times){
        if(empty($groups))
            return null;
        $count = count($groups);
        $virefy = [];
        for ($i=0;$i<$count;$i++){
            for($j=$i+1;$j<$count;$j++)
                $virefy[] = [$groups[$i],$groups[$j]];
        }
        shuffle($virefy);
        $times = $times ==0?(int)($count/2):$times;
        while (!empty($virefy)){
            $virefy = $this->chooseSchedules($virefy,$times);
        }
        return $this->_groupSchedules;
    }

    private function chooseSchedules($virefy=[],$times){
        $exist = [];
        $schedules = [];
        for ($i=0;$i<$times;$i++){
            foreach ($virefy as $key=>$value){
                if(!in_array($value[0],$exist) and !in_array($value[1],$exist)){
                    array_push($exist,$value[0]);
                    array_push($exist,$value[1]);
                    $schedules[] = $key%2==0?['home_team'=>$value[1],'visiting_team'=>$value[0]]:['home_team'=>$value[0],'visiting_team'=>$value[1]];
                    unset($virefy[$key]);
                }
                continue;
            }
        }
        $this->_groupSchedules[] = $schedules;
        return $virefy;
    }

}