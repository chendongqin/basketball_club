<?php

namespace ku;
use think\Request;
use think\File;
/**
 * UPLOAD_ERR_OK           [31000]     其值为 0，没有错误发生，文件上传成功。
 * UPLOAD_ERR_INI_SIZE     [31001]     其值为 1，上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。
 * UPLOAD_ERR_FORM_SIZE    [31002]     其值为 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。
 * UPLOAD_ERR_PARTIAL      [31003]     其值为 3，文件只有部分被上传。
 * UPLOAD_ERR_NO_FILE      [31004]     其值为 4，没有文件被上传。
 * UPLOAD_ERR_NO_TMP_DIR   [31006]     其值为 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。
 * UPLOAD_ERR_CANT_WRITE   [31007]     其值为 7，文件写入失败。PHP 5.1.0 引进。
 */
final class Upload {

    protected $_inputname = null;
    protected $_mustPost = true;

    /**
     * 最大上传大不 (KB)
     *
     * @var int
     */
    protected $_maxSize = 2048;

    /**
     * 支持的资源及类型
     *
     * @var array
     */
    protected $_supportSuffix = array('gif', 'jpg', 'jpeg', 'png');
    protected $_supportResource = array('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'image/pjpeg', 'image/x-png', 'application/octet-stream');

    /**
     * 上传解析相关数据
     *
     * @var string
     */
    protected $_file_tmpname = null;
    protected $_file_name = null;
    protected $_file_error = null;
    protected $_file_size = null;
    protected $_file_type = null;
    protected $_ext = null;
    protected $_file_suffix = 'jpg';
    protected $_width = null;
    protected $_height = null;

    /**
     * 错误信息
     *
     * @var array
     */
    protected $_err_val = array();

    /**
     * 回传数据
     *
     * @var array
     */
    protected $_ret_val = array();

    public function __construct() {
        
    }

    /**
     * 上传input 名称
     *
     * @param string $name
     * @return \Ku\Upload
     */
    public function setFormName($name) {
        $this->_inputname = (string) $name;

        return $this;
    }

    /**
     * 获取input 名称
     * @return string
     */
    public function getFormName() {
        return $this->_inputname;
    }

    /**
     * 是否必须Post
     *
     * @param boolean $is
     * @return \Ku\Upload
     */
    public function setMustPost($is) {
        $this->_mustPost = (bool) $is;

        return $this;
    }

    /**
     * 最大上传大小(KB)
     *
     * @param int $maxsize
     * @return \Ku\Upload
     */
    public function setMaxSize($maxsize) {
        $this->_maxSize = (int) $maxsize;

        return $this;
    }

    /**
     * 设置支持的后缀类型
     *
     * @param array $suffix
     * @return \Ku\Upload
     */
    public function setSupportSuffix(array $suffix) {
        $this->_supportSuffix = (array) $suffix;
        $this->_supportResource = array();

        return $this;
    }

    /**
     * 设置支持的后缀资源类型
     *
     * @param array $resource
     * @return \Ku\Upload
     */
    public function setSupportResource(array $resource) {
        $this->_supportResource = (array) $resource;

        return $this;
    }

    /**
     * 上传成功之后的回传数据
     *
     * @return \Ku\Upload
     */
    public function setRetval($retKey, $retVal) {
        $this->_ret_val[$retKey] = (string) $retVal;

        return $this;
    }

    /**
     * 错误信息
     *
     * @param string $errVal
     * @return \Ku\Upload
     */
    public function setErrval($errKey, $errVal) {
        $this->_err_val[$errKey] = (string) $errVal;

        return $this;
    }

    /**
     * 错误信息
     *
     * @return array
     */
    public function getErrval() {
        return $this->_err_val;
    }

    /**
     * 上传成功之后的回传数据
     *
     * @return array
     */
    public function getRetval() {
        return $this->_ret_val;
    }

    /**
     * 文件大小
     *
     * @return string
     */
    public function getFilesize() {
        return $this->_file_size;
    }

    /**
     * 文件类型
     *
     * @return string
     */
    public function getFiletype() {
        return $this->_file_type;
    }
    
    
    /*
     * 文件后缀名
     */
    public function getFileSuffix(){
        return $this->_file_suffix;
    }

    /**
     * 文件名
     *
     * @return string
     */
    public function getFilename() {
        return $this->_file_name;
    }

    /**
     * PHP临时文件名
     *
     * @return string
     */
    public function getFiletmpName() {
        return $this->_file_tmpname;
    }

    /**
     * 获取图片的宽
     * @return int
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * 获取图片的长
     * @return int
     */
    public function getHeight() {
        return $this->_height;
    }

    public function getExt() {
        return $this->_ext;
    }

    /**
     * 文件上传
     *
     * @param string $target
     * @return \Ku\Upload
     */
    public function moveFile($target) {
        move_uploaded_file($this->getFiletmpName(), $target);

        return $this;
    }

    /**
     * 创建安全码
     *
     * @param string $file
     * @return string
     */
    public function buildCode() {
        $code = md5(uniqid(mt_rand(10, 999999)) . '-' . (string) $this->getFiletmpName() . '-' . implode('-', $this->_supportResource));
        $this->setRetval('code', $code);

        return $code;
    }

    /**
     * 路径处理
     *
     * @param string $path
     * @return boolean|string
     */
    public function path($path) {
        $path =  PUBLIC_PATH . '/' . ltrim($path, '/');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);

            if (!file_exists($path)) {
                $this->setErrval(0xE1, '上传路径不存在');
                return false;
            }
        }

        return $path;
    }

    /**
     * 自定义完整路径(根目录)
     *
     * @param string $path
     * @return boolean|string
     */
    public function fullPath($path) {
        $path = PUBLIC_PATH . '/' . ltrim($path, '/');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);

            if (!file_exists($path)) {
                $this->setErrval(0xE1, '上传路径不存在');
                return false;
            }
        }
        return $path;
    }

    /**
     * 文件上传
     *
     * @params callback $callback
     * @return boolean
     */
    public function exec($callback = null,$suffix='') {
        $request = Request::instance();
        if ($request->isPost() === !$this->_mustPost) {
            return false;
        }
        $passed = $this->parseFile($request->file($this->_inputname));
        if ($passed === true) {
            if (is_callable($callback)) {
            	if(!empty($suffix)){
            		$passed = call_user_func_array($callback, array($this,$suffix));
            	}else{
            		$passed = call_user_func_array($callback, array($this));
            	}
            	
            }
            return $passed;
        }

        return false;
    }

    /**
     * 解析文件流
     *
     * @param Tmp $file
     * @return boolean
     */
    protected function parseFile($file) {
        if($file instanceof File){
            $file = $file->getInfo();
        }
        $name = (isset($file['name'])) ? $file['name'] : null;
        $tmpName = (isset($file['tmp_name'])) ? $file['tmp_name'] : null;
        $error = (isset($file['error'])) ? $file['error'] : UPLOAD_ERR_NO_FILE;
        $size = (isset($file['size'])) ? ceil($file['size'] / 1024) : 0;
        $type = (isset($file['type'])) ? $file['type'] : null;
        
        if ($error !== 0) {
            $this->setErrval(28101, '图片已损坏');
            return false;
        }

        if (!is_uploaded_file($tmpName) || $name === null || $tmpName === null || mb_strlen($name) < 1) {
            $this->setErrval(28102, '服务被拒绝');
            return false;
        }

        if ($size < 0 || $size > $this->_maxSize) {
            $this->setErrval(28103, '上传的文件大小必须小于' . round($this->_maxSize / 1024) . 'MB');
            return false;
        }
        $imgInfo = @getimagesize($tmpName);
        $this->_width = isset($imgInfo[0]) ? $imgInfo[0] : 0;
        $this->_height = isset($imgInfo[1]) ? $imgInfo[1] : 0;
        $typeName = explode('.', $name);
        $shuffix = array_pop($typeName);
        if (!in_array($type, $this->_supportResource) || !in_array(strtolower($shuffix), $this->_supportSuffix)) {
            $this->setErrval(28104, '文件格式/文件类型不支持, 仅支持' . implode(',', $this->_supportSuffix));
            return false;
        }

        $this->_file_name = $name;
        $this->_file_tmpname = $tmpName;
        $this->_file_error = $error;
        $this->_file_size = $size;
        $this->_file_type = $type;
        $this->_ext = strtolower($shuffix);
        $fileSuffixType=array(
            'image/gif'=>'gif',
            'image/jpg'=>'jpg',
            'image/jpeg'=>'jpg',
            'image/png'=>'png',
            'image/pjpeg'=>'jpg',
            'image/x-png'=>'png',
        );
        if(array_key_exists($type, $fileSuffixType)){
            $this->_file_suffix = $fileSuffixType[$type];
        }
        
        return true;
    }

}
