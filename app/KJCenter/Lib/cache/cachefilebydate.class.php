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
require_once('cachefile.class.php');
/**
 +------------------------------------------------------------------------------
 * 文件类型缓存类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class CacheFileByDate extends CacheFile
{//类定义开始

	/**
     +----------------------------------------------------------
     * 读取缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
	public function get($name)
	{
		$filename   =   $this->filename($name);
		if (!$this->isConnected() || !is_file($filename)) {
			return false;
		}
		$this->Q(1);
		$content    =   file_get_contents($filename);
		if( false !== $content) {
			$expire  =  substr($content,8, 14);
			$thistime = date('YmdHis',time());
			if($expire != -1 && $thistime > $expire) {
				//缓存过期删除缓存文件
				unlink($filename);
				return false;
			}
			//if(C('DATA_CACHE_CHECK')) {//开启数据校验
			//    $check  =  substr($content,20, 32);
			//    $content   =  substr($content,52, -3);
			//    if($check != md5($content)) {//校验错误
			//        return false;
			//    }
			//}else {
			$content   =  substr($content,22, -3);
			//}
			//if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
			//启用数据压缩
			//    $content   =   gzuncompress($content);
			//}
			$content    =   unserialize($content);
			return $content;
		}
		else {
			return false;
		}
	}

	/**
     +----------------------------------------------------------
     * 写入缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 -1 为永久
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function set($name,$value,$expire='')
	{
		$this->W(1);
		if('' === $expire) {
			$expire =  $this->expire;
		}
		$filename   =   $this->filename($name);
		$data   =   serialize($value);
		$data    = "<?php\n//".$expire.$data."\n if(!defined('EASY_ROOT')) {exit('Access Denied');}\n?>";
		$result  =   file_put_contents($filename,$data);
		if($result) {
			clearstatcache();
			return true;
		}else {
			return false;
		}
	}
}//类定义结束
?>