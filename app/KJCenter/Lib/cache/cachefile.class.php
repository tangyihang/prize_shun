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
class CacheFile extends Cache
{//类定义开始

	/**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
	public function __construct($options='')
	{
		if(!empty($options['temp'])){
			$this->options['temp'] = $options['temp'];
		}else {
			$this->options['temp'] = $options['temp'];
		}
		$this->expire = isset($options['expire'])?$options['expire'] : 10;
		if(substr($this->options['temp'], -1) != "/")    $this->options['temp'] .= "/";
		$this->connected = is_dir($this->options['temp']) && is_writeable($this->options['temp']);
		//$this->type = strtoupper(substr('Cache',6));
		$this->init();
	}

	/**
     +----------------------------------------------------------
     * 初始化检查
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function init()
	{
		$stat = stat($this->options['temp']);
		$dir_perms = 0007777; // Get the permission bits.
		$file_perms = 0000666; // Remove execute bits for files.

		// 创建项目缓存目录
		if (!is_dir($this->options['temp'])) {
			if (!  mkdir($this->options['temp']))
			return false;
			chmod($this->options['temp'], $dir_perms);
		}
	}

	/**
     +----------------------------------------------------------
     * 是否连接
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	protected function isConnected()
	{
		return $this->connected;
	}

	/**
     +----------------------------------------------------------
     * 取得变量的存储文件名
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
	protected function filename($name)
	{
		$name	=	md5($name);
		$filename	=	$this->prefix.$name.'.php';
		return $this->options['temp'].$filename;
	}

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
		// 在继承类里实现
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
		// 在继承类里实现
	}

	/**
     +----------------------------------------------------------
     * 删除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function rm($name)
	{
		return unlink($this->filename($name));
	}

	/**
     +----------------------------------------------------------
     * 清除缓存
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name 缓存变量名
     +----------------------------------------------------------
     * @return boolen
     +----------------------------------------------------------
     */
	public function clear()
	{
		$path   =  $this->options['temp'];
		if ( $dir = opendir( $path ) )
		{
			while ( $file = readdir( $dir ) )
			{
				$check = is_dir( $file );
				if ( !$check )
				unlink( $path . $file );
			}
			closedir( $dir );
			return true;
		}
	}

}//类定义结束
?>