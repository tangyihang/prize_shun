<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
require('cache.class.php');
/**
 +------------------------------------------------------------------------------
 * Think公共函数库
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */

// 错误输出
function halt($error) {
	if(1)   exit ($error);
	exit;
}

// 自定义异常处理
function throw_exception($msg,$type='Exception',$code=0)
{
	if(1)   exit($msg);
	if(class_exists($type,false))
	throw new $type($msg,$code,true);
	else
	halt($msg);        // 异常类型不存在则输出错误信息字串
}

// 取得对象实例 支持调用类的静态方法
function get_instance_of($name,$method='',$args=array())
{
	static $_instance = array();
	$identify   =   empty($args)?$name.$method:$name.$method.to_guid_string($args);
	if (!isset($_instance[$identify])) {
		if(class_exists($name)){
			$o = new $name();
			if(method_exists($o,$method)){
				if(!empty($args)) {
					$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
				}else {
					$_instance[$identify] = $o->$method();
				}
			}
			else
			$_instance[$identify] = $o;
		}
		else
		halt('_CLASS_NOT_EXIST_:'.$name);
	}
	return $_instance[$identify];
}

/**
 +----------------------------------------------------------
 * 系统自动加载ThinkPHP基类库和当前项目的model和Action对象
 * 并且支持配置自动加载路径
 +----------------------------------------------------------
 * @param string $name 对象类名
 +----------------------------------------------------------
 * @return void
 +----------------------------------------------------------
 */

// 优化的require_once
function require_cache($filename)
{
	static $_importFiles = array();
	$filename   =  realpath($filename);
	if (!isset($_importFiles[$filename])) {
		if(file_exists_case($filename)){
			require $filename;
			$_importFiles[$filename] = true;
		}
		else
		{
			$_importFiles[$filename] = false;
		}
	}
	return $_importFiles[$filename];
}

// 全局缓存设置和读取
// 过期时间为秒
function cachedata($name,$value='',$expire='',$type='file',$path = './Cache') {
	static $_cache = array();
	//alias_import('Cache');
	//取得缓存对象实例
	$cache  = Cache::getInstance($type,array('expire'=>$expire,'temp'=>$path,'classname'=>'stamp'));
	if('' !== $value) {
		if(is_null($value)) {
			// 删除缓存
			$result =   $cache->rm($name);
			if($result)   unset($_cache[$type.'_'.$name]);
			return $result;
		}else{
			// 缓存数据
			$cache->set($name,$value,$expire);
			$_cache[$type.'_'.$name]     =   $value;
		}
		return ;
	}
	if(isset($_cache[$type.'_'.$name]))
	return $_cache[$type.'_'.$name];
	// 获取缓存数据
	$value      =  $cache->get($name);
	$_cache[$type.'_'.$name]     =   $value;
	return $value;
}

// 全局缓存设置和读取
// 过期时间为日期，精确到秒
function cachedate($name,$value='',$expire='',$type='file',$path = './Cache') {
	static $_cache = array();
	//alias_import('Cache');
	//取得缓存对象实例
	$cache  = Cache::getInstance($type,array('expire'=>$expire,'temp'=>$path,'classname'=>'date'));
	if('' !== $value) {
		if(is_null($value)) {
			// 删除缓存
			$result =   $cache->rm($name);
			if($result)   unset($_cache[$type.'_'.$name]);
			return $result;
		}else{
			// 缓存数据
			$cache->set($name,$value,$expire);
			$_cache[$type.'_'.$name]     =   $value;
		}
		return ;
	}
	if(isset($_cache[$type.'_'.$name]))
	return $_cache[$type.'_'.$name];
	// 获取缓存数据
	$value      =  $cache->get($name);
	$_cache[$type.'_'.$name]     =   $value;
	return $value;
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name,$value='',$path='./Cache') {
	static $_cache = array();
	$filename   =   $path.$name.'.php';
	if('' !== $value) {
		if(is_null($value)) {
			// 删除缓存
			return unlink($filename);
		}else{
			// 缓存数据
			$dir   =  dirname($filename);
			// 目录不存在则创建
			if(!is_dir($dir))  mkdir($dir);
			return file_put_contents($filename,"<?php\nreturn ".var_export($value,true).";\n?>");
		}
	}
	if(isset($_cache[$name])) return $_cache[$name];
	// 获取缓存数据
	if(is_file($filename)) {
		$value   =  include $filename;
		$_cache[$name]   =   $value;
	}else{
		$value  =   false;
	}
	return $value;
}

// 根据PHP各种类型变量生成唯一标识号
function to_guid_string($mix)
{
	if(is_object($mix) && function_exists('spl_object_hash')) {
		return spl_object_hash($mix);
	}elseif(is_resource($mix)){
		$mix = get_resource_type($mix).strval($mix);
	}else{
		$mix = serialize($mix);
	}
	return md5($mix);
}

// 循环创建目录
function mk_dir($dir, $mode = 0755)
{
	if (is_dir($dir) || @mkdir($dir,$mode)) return true;
	if (!mk_dir(dirname($dir),$mode)) return false;
	return @mkdir($dir,$mode);
}

?>