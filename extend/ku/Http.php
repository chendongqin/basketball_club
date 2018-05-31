<?php

namespace ku;

/**
 * 简单模拟的浏览器客户端
 */
class Http {

    protected $_url = null;
    protected $_post = false;
    protected $_params = null;
    protected $_timeout = 180;
    protected $_cookie = null;
    protected $_ua = 'Ku Http';
    protected $_cookie_file = '';
    protected $_save_cookie = '';
    protected $_header = 0;

    protected $_file = '';
//    protected $_paramPost = false;

    /**
     * 设置请求URL
     *
     * @param string $url
     * @return \Ku\Http
     */
    public function setUrl($url) {
        $this->_url = (string) $url;

        return $this;
    }

    /**
     * 设置Param 数据
     *
     * @param string|array $post
     * @return \Ku\Http
     */
//    public function setParam($_params, $isPost = false) {
//        $this->_post = $isPost;
//        if(isset($_params['inputStream'])){
//            $_params['inputStream'] = new \CURLFile($_params['inputStream']);
//        }
//        if (is_array($_params)) {
//            $this->_params = http_build_query($_params);
//        } elseif (is_string($_params)) {
//            $this->_params = \html_entity_decode($_params);
//        }
//
//        return $this;
//    }

    public function setParam($_params, $isPost = false, $isJson = false) {
        $this->_post = $isPost;
        if(isset($_params['inputStream'])){
            if (class_exists('\CURLFile')) {
                $this->_file = array('inputStream' => new \CURLFile(realpath($_params['inputStream'])));
            } else {
                $this->_file = array('inputStream' => '@' . realpath($_params['inputStream']));
            }
            unset($_params['inputStream']);
            $this->_timeout=100;
        }
        if($isJson == true){
            $this->_params = json_encode($_params);
        }else{
            if (is_array($_params)) {
                $this->_params = http_build_query($_params);
            } elseif (is_string($_params)) {
                $this->_params = \html_entity_decode($_params);
            }
        }
        if($isJson){

        }
        return $this;
    }

    public function geturl(){
        return $this->_url . '?' . $this->_params;
    }

    /**
     * 设置超时
     *
     * @param smallint $time
     * @return \Ku\Http
     */
    public function setTimeout($time) {
        $this->_timeout = (int) $time;

        return $this;
    }

    /**
     * 设置COOKIE
     *
     * @param string $cookie
     * @return \Ku\Http
     */
    public function setCookie($cookie) {
        $this->_cookie = (string) $cookie;

        return $this;
    }

    public function setUa($ua = null) {
        $this->_ua = trim($ua);
    }

    public function setHeader($header) {
        $this->_header = (array) $header;
    }

    public function setCookie_file($file = '') {
        $this->_cookie_file = $file;
    }

    public function setSave_Cookie($file = '') {
        $this->_save_cookie = $file;
    }

    /**
     * 发起一个CURL请求,模拟HTTP
     *
     * @return json|null|string|array|Object
     */
    public function send()
    {
        $ch = curl_init();

        $url = $this->_url;
        if ((!$this->_post && $this->_params) or $this->_file ){
            $url = $this->_url . '?' . $this->_params;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($this->_ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->_ua);
        }

        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        if ($this->_header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
        }

        if (!empty($this->_cookie)) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->_cookie);
        }
        if (!empty($this->_save_cookie)) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->_save_cookie); //存储cookies
        }
        if (!empty($this->_cookie_file)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->_cookie_file); //将cookies带入请求
        }
        if ($this->_post) {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($this->_file) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_file);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
            }

        }
        $response = curl_exec($ch);
        curl_close($ch);
        $this->reset();

        return $response;
    }


    public function postJson(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_POST, 1);
//        var_dump($this->_params);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_params);
        $dataLen = strlen($this->_params);
//        var_dump($dataLen);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' .$dataLen)
        );
        $response = curl_exec($ch);
        curl_close($ch);
        $this->reset();
        return $response;
    }

    /**
     * 重置
     */
    protected function reset() {
        $this->_url = null;
        $this->_post = false;
        $this->_postFields = null;
        $this->_params = null;
        $this->_timeout = 1;
        $this->_save_cookie = null;
        $this->_header = null;
        $this->_cookie_file = null;
        $this->_cookie = null;
        $this->_file = null;
//        $this->_paramPost = false;
    }

}
