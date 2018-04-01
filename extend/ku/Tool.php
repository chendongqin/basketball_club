<?php

namespace ku;
use \think\Session;
/**
 * 一些可能用到的小函数
 * 尽量用 "静态方法" 实现, 这样调用时就比较方便.
 *
 * @author Ghost
 */
class Tool {

    /**
     * 检测IP地址是否在配置规则内
     *
     * 注意: 如果规则为空时, 返回 true
     *
     * @param string $ip    ipv4的格式
     * @param array $rules  规则, 例如: 192.168.1.1/24, 127.0.0.1
     * @return boolean
     */
    public static function isInNetwork($ip, array $rules) {
        if (!isset($rules[0])) {
            return true;
        }

        $pass = false;
        $rules = array_flip(array_flip($rules));

        foreach ($rules as $rule) {
            $rule = trim($rule);

            if (empty($rule)) {
                continue;
            }

            $set = explode('/', trim($rule));
            $netway = (int) ((isset($set[1])) ? max(1, min($set[1], 32)) : 32);
            $ip_rule = $set[0];

            if ($netway === 32 && strcmp($ip_rule, $ip) === 0) {
                $pass = true;
                break;
            }

            $bits = 32 - $netway;

            $netmask = (0xFFFFFFFF >> $bits) << $bits;

            $ip_source = ip2long($ip_rule) & $netmask;
            $ip_target = ip2long($ip) & $netmask;

            if (strcmp($ip_target, $ip_source) === 0) {
                $pass = true;
                break;
            }
        }

        return $pass;
    }

    /**
     * 获取客户端的 IP
     *
     * @param boolean $ip2long 是否转换成为整形
     *
     * @return int|string
     */
    public static function getClientIp($ip2long = false) {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER ['HTTP_X_REAL_IP'];
            } else if (isset($_SERVER ['HTTP_X_FORWARDED_FOR'])) {
                $ip = array_pop(explode(',', $_SERVER ['HTTP_X_FORWARDED_FOR']));
            } elseif (isset($_SERVER ['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER ['HTTP_CLIENT_IP'];
            } else {
                $ip = isset($_SERVER ['REMOTE_ADDR'])?$_SERVER ['REMOTE_ADDR']:'127.0.0.1';
            }
        } else {
            if (getenv('HTTP_X_REAL_IP')) {
                $ip = getenv('HTTP_X_REAL_IP');
            } else if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = array_pop(explode(',', getenv('HTTP_X_FORWARDED_FOR')));
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }

        return $ip2long ? sprintf("%u", ip2long($ip)) : $ip;
    }

    /**
     * 获取客户端的 UA
     *
     * @return string
     */
    public static function getClientUa() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? self::filter($_SERVER['HTTP_USER_AGENT']) : '';
    }

    /**
     * 字符串处理
     *
     * @param type $str
     * @return string
     */
    public static function filter($content, $escape = true) {

        // Fix &entity\n;
        $content = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $content);
        $content = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $content);
        $content = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $content);
        $content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');
        // Remove any attribute starting with "on" or xmlns
        $content = preg_replace('#(<[^>]+?[\x00-\x20"\'\*//])(?:on|xmlns)[^>]*+>#iu', '$1>', $content);

        // Remove javascript: and vbscript: protocols
        $content = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $content);
        $content = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $content);
        $content = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $content);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $content = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $content);
        $content = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $content);
        $content = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $content);
        // Remove namespaced elements (we do not need them)
        $content = preg_replace('#</*\w+:\w[^>]*+>#i', '', $content);
        do {
            // Remove really unwanted tags
            $oldData = $content;
            $content = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $oldData);
        } while ($oldData !== $content);
        return $escape === true ? addslashes(htmlentities(trim($content))) : $content;
    }
    /**
     * 数据签名
     *
     * @param array $params
     * @return string
     */
    public static function sign(array $params, $sep = ':') {
        ksort($params);

        $str = implode($sep, $params);
        $hash = sha1($str . ':' . strrev($str) . ':' . (count($params) << 8));

        return md5($hash . $sep . substr($hash, 5, 21));
    }

    /**
     * 加密
     *
     * @param string $needle
     * @param string $secure
     * @return string
     */
    public static function encryption($needle, $secure = null) {
        $md5str = md5($needle . ':' . $secure);
        $randArr = str_split($md5str, 2);
        $randStr = strrev($randArr[mt_rand(0, count($randArr) - 1)]);
        return sha1($md5str . ':' . $randStr) . ':' . $randStr;
    }

    /**
     * 验证
     *
     * @param string $input
     * @param string $needle
     * @param string $secure
     *
     * return boolean
     */
    public static function valid($input, $needle, $secure = null) {
        $needleArr = explode(':', $needle);

        if (count($needleArr) == 2) {
            $sha1Str = sha1(md5($input . ':' . $secure) . ':' . $needleArr[1]);
            if (strcmp($sha1Str, $needleArr[0]) === 0) {
                return true;
            }
        }

        return false;
    }


    /**
     * 生成一串随机码
     *
     * @param int $length
     * @param boolean $case
     * @return string
     */
    public static function randCode($length = 12, $case = true) {
        $str = 'abcdefghijklnmopqsrtvuwxyz123456879';

        if ($case === true) {
            $str .= 'ABCDFEGHIJKLMNOPRQSTUVWXYZ';
        }

        $slen = strlen($str);
        $nstr = array();

        while ($length > 0) {
            $index = mt_rand(0, $slen);

            if (isset($str[$index])) {
                $nstr[] = $str[$index];
                $length--;
            }
        }

        return implode($nstr);
    }

    /**
     * 可逆加/解密
     *
     * @param string $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return string
     */
    public static function authCode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;

        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 创建目录
     * @param string $path
     * @return boolean
     */
    public static function makeDir($path) {
        if (DIRECTORY_SEPARATOR == "\\") {//windows os
            $path = iconv('utf-8', 'gbk', $path);
        }
        if (!$path) {
            return false;
        }
        if (file_exists($path)) {
            return true;
        }
        if (mkdir($path, 0777, true)) {
            return true;
        }
        return false;
    }


    public static function noRound(){

    }

    public static function pr($data){
        echo '<pre>';print_r($data);echo '</pre>';
    }

    /**
     * 15位身份证升至18位
     * @param $idcardNo
     * @return string
     */
    public static function riseIDcardNo($idcardNo){
        $ratio = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
        $mantissa = array(0=>1,1=>0,2=>'X',3=>9,4=>8,5=>7,6=>6,7=>5,8=>4,9=>3,10=>2);
        $first = substr($idcardNo,0,6);
        $last = substr($idcardNo,6);
        $idcardNo = $first.'19'.$last;
        $numArr = str_split($idcardNo);
        $total = 0;
        foreach ($numArr as $key =>$value){
            $total += $value * $ratio[$key];
        }
        $mod = $total % 11;
        $idcardNo = $idcardNo.$mantissa[$mod];
        return $idcardNo;
    }

    /**
     * 图片重新生成
     * @param $fileName
     * @param $save_path
     * @return bool
     */
    public static function uploadImage($fileName,$save_path){
        $imageInfo = getimagesize($fileName);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        $thumb = imagecreatetruecolor($width,$height);
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb,0,0,$width,$height,$white);
        $source = null;
        //1 = GIF，2 = JPG，3 = PNG
        switch ($type){
            case 1:
                $source = imagecreatefromgif($fileName);
                break;
            case 2:
                $source = imagecreatefromjpeg($fileName);
                break;
            case 3:
                $source = imagecreatefrompng($fileName);
                break;
            default:
                break;
        }
        imagecopyresized($thumb,$source,0,0,0,0,$width,$height,$width,$height);
        $result = imagejpeg($thumb,$save_path,100);
        return $result;
    }

    /**等比处理图片
     * @param $fileName
     * @param $save_path
     * @param int $size
     * @return bool
     */
    public static function sizeImage($fileName,$save_path,$size=100){
        $imageInfo = getimagesize($fileName);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        $heightSize = $height/$width*$size;
        $thumb = imagecreatetruecolor($size,$heightSize);
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb,0,0,$size,$heightSize,$white);
        $source = null;
        //1 = GIF，2 = JPG，3 = PNG
        switch ($type){
            case 1:
                $source = imagecreatefromgif($fileName);
                break;
            case 2:
                $source = imagecreatefromjpeg($fileName);
                break;
            case 3:
                $source = imagecreatefrompng($fileName);
                break;
            default:
                break;
        }
        imagecopyresized($thumb,$source,0,0,0,0,$size,$heightSize,$width,$height);
        $result = imagejpeg($thumb,$save_path,100);
        return $result;
    }


    public static function captcha($length,$width,$height,$channel){
        $image = imagecreatetruecolor($width,$height);
        $white = imagecolorallocate($image,255,255,255);
        imagefilledrectangle($image,0,0,$width,$height,$white);
        $string = join('',array_merge(range(0,9),range('a','z'),range('A','Z')));
        $y = $height/2;
        $code = '';
        $fontFile = PUBLIC_PATH.'/static/font/AntykwaBold.ttf';
        for($i=0;$i<$length;$i++){
            $randColor = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            $size = mt_rand(12,18);
            $angle = mt_rand(-20,20);
            $x = 5+20*$i;
            $text = str_shuffle($string){0};
            $code .= $text;
            imagettftext($image,$size,$angle,$x,$y,$randColor,$fontFile,$text);
        }
        $session = new Session();
        $session->set($channel.'_virefy_code',$code);
        header("Content-Type: image/png");
        imagepng($image);
        imagedestroy($image);
//        return $image;
    }

    public static function send($url,$params,$isPost = false){
        $http = new Http();
        $http->setParam($params,$isPost);
        $http->setUrl($url);
        return $http->send();
    }

}
