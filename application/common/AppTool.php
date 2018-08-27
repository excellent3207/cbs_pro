<?php 
/**
 * 工具类
 * author：xjp
 * create：2017.4.8
 */
namespace app\common;

class AppTool{
	public static function formatUrl($uri){
		if(!$uri) return '';
		if(strpos($uri, 'http://') !== false || strpos($uri, 'https://') !== false){
			return $uri;
		}else{
			return 'https://cbs-resource.oss-cn-beijing.aliyuncs.com/'.$uri;
		}
	}
	/**
	 * 格式化图片地址
	 * @param string $uri
	 * @return string
	 */
	public static function formatImgUrl($uri){
		if(!$uri) return '';
		if(strpos($uri, 'http://') !== false || strpos($uri, 'https://') !== false){
			return $uri;
		}else{
			$pos = strpos($uri, 'img');
			if($pos !== 0 && $pos !== 1){
				$uri = 'img/'.trim($uri, '/');
			}else{
				$uri = trim($uri, '/');
			}
			return 'https://cbs-resource.oss-cn-beijing.aliyuncs.com/'.$uri;
		}
	}
	/**
	 * 格式化文件地址
	 * @param string $uri
	 * @return string
	 */
	public static function formatMediaUrl($uri){
		if(!$uri) return '';
		if(strpos($uri, 'http://') !== false || strpos($uri, 'https://') !== false){
			return $uri;
		}else{
			$pos = strpos($uri, 'file');
			if($pos !== 0 && $pos !== 1){
				$uri = 'file/'.trim($uri, '/');
			}else{
				$uri = trim($uri, '/');
			}
			return 'https://cbs-resource.oss-cn-beijing.aliyuncs.com/'.$uri;
		}
	}
	/**
	 * 格式化音频地址
	 * @param string $uri
	 * @return string
	 */
	public static function formatAudioUrl($uri){
		if(!$uri) return '';
		if(strpos($uri, 'http://') !== false || strpos($uri, 'https://') !== false){
			return $uri;
		}else{
			if(strpos($uri, 'audio_mts') !== 0){
				$pos = strpos($uri, 'audio');
				if($pos !== 0 && $pos !== 1){
					$uri = 'audio/'.trim($uri, '/');
				}else{
					$uri = trim($uri, '/');
				}
			}
			return 'https://cbs-resource.oss-cn-beijing.aliyuncs.com/'.$uri;
		}
	}
	/**
	 * 富文本编辑器域名
	 * @return string
	 */
	public static function kindEditorDomain(){
		return 'https://cbs-resource.oss-cn-beijing.aliyuncs.com';
	}
	/**
	 * 格式化价格
	 * @param int $money
	 * @return string
	 */
	public static function formatMoney($money){
		if($money%100 !== 0){
			return sprintf("%.2f",$money/100);
		}else{
			return intval($money/100);
		}
	}
	/**
	 * 格式化时间
	 * @param int $time
	 * @return string
	 */
	public static function formatTime(int $time){
		if(!$time) return '';
		$formatTime = '';
		$interval = $_SERVER['REQUEST_TIME'] - $time;
		if($interval >= 3600 * 24){
			$formatTime = date('Y/m/d', $time);
		}elseif($interval >= 3600){
			$formatTime = floor($interval/3600).'小时前';
		}else{
			$interval = floor($interval/60);
			if($interval == 0){
				$formatTime='刚刚';
			}else{
				$formatTime = $interval.'分钟前';
			}
		}
		return $formatTime;
	}
	/**
	 * 格式化数量
	 * @param unknown $count
	 * @return string|number
	 */
	public static function formatNum($count){
		$count = intval($count);
		if($count >= 100000){
			$count = '10万+';
		}elseif($count >=10000){
			$num = floor($count/10000);
			$count = $num.'万+';
		}
		return $count;
	}
	/**
	 * 判断是否是新内容
	 * @param unknown $time
	 * @return number
	 */
	public static function checkIsNew($time){
		if(!$time) return 0;
		return time() - 24 * 3600 < $time ? 1 : 0;
	}
	/**
	 * 字符串随机数
	 * @param int $length
	 * @return string
	 */
	public static function randString(int $length = 8){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';
		$str ='';
		for ( $i = 0; $i < $length; $i++ ){
			// 这里提供两种字符获取方式
			// 第一种是使用 substr 截取$chars中的任意一位字符；
			// 第二种是取字符数组 $chars 的任意元素
			// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $str; 
	}
	/**
	 * 基于redis的锁定阻塞
	 */
	public static function lockBlock($key, $callback){
	    $redis = SyhRedis::instance();
	    $islock = $redis->hget("lockBlock", $key);
	    while(empty($redis->hget("lockBlock", $key))){
	        $redis->hset("lockBlock", $key, 1);
	        $callback();
	        $redis->hdel("lockBlock", $key);
	        break;
	    }
	}
}
?>