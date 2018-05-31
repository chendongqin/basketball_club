<?php

namespace ku;

class Verify {

    /**
     * 验证是否邮箱地址
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmail($email) {
        return (bool)(preg_match('/^[a-z0-9][a-z0-9\_\.]*[a-z0-9\_]\@[a-z0-9]+\.[a-z][a-z\.]+/i', $email));
    }

    /**
     * 验证手机号
     *
     * @param string $phone
     * @return boolean
     */
    public static function isMobile($phone) {
        return (bool)(preg_match('/^(\+86)?(1[3|4|5|7|8][0-9]{9})$/', $phone));
    }

    /**
     * 验证QQ
     *
     * @param number $qq
     * @return boolean
     */
    public static function isQQ($qq) {
        return (bool)(preg_match('/^[1-9][0-9]{4,13}$/', $qq));
    }

    /**
     * 验证是否是URL
     *
     * @param string $url
     */
    public static function isUrl($url) {
        $pregURL = '/^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;\+#]*[\w\-\@?^=%&amp;\+#])?/';

        return (bool)(preg_match($pregURL, $url));
    }

    /**  
     * 验证是否是域名
     *
     * @param string $domain
     * @return boolean
     */
    public static function isDomain($domain) {
        return (bool)(preg_match('/^([a-z0-9]+\.)*([a-z0-9][a-z0-9\-]*)\.([a-z]{2,9})$/i', $domain));
    }
    
    /**
     * 验证是否是否中文
     *
     * @param string $domain
     * @return boolean
     */
    public static function isCn($domain) {
        return (bool)(preg_match('/^[\x7f-\xff]+/', $domain));
    }
    
    public static function isIpv4($ip){
        return (bool)(preg_match('/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/', $ip));
    }
    
    public static function isMore(){
        $args = func_get_args();
        $value = isset($args[0])?$args[0]:0;
        $more = isset($args[1])?$args[1]:0;
       return boolval($value>=$more);
        
    }
    
    public static function isLess(){
        $args = func_get_args();
         $value = isset($args[0])?$args[0]:0;
        $more = isset($args[1])?$args[1]:0;
       return boolval($value<=$more);
        
    }

    public static function isPasswd($password){
        return (bool)(preg_match('/(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,30}/', $password));
    }
    
}
