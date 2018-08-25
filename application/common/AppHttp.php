<?php
/**
 * http工具类
 * author：xjp
 * create：2017.4.8
 */
namespace app\common;

class AppHttp{
	/**
	 * http get
	 * @param unknown $url
	 * @param number $timeout
	 * @return string|mixed
	 */
	public static function get($url, $timeout = 10){
		if(!$url) return '';
		$ssl = substr($url, 0, 8) == "https://" ? true : false;
		$ch = curl_init($url);
		if($ssl){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		$res = curl_exec($ch);
		curl_close($ch);
		if($res === false){
			/*if(curl_errno($ch) == CURLE_OPERATION_TIMEDOUT){
				throw new HttpException('请求超时');
			}*/
		}
		return $res;
	}
	/**
	 * http post
	 * @param string $url
	 * @param array $data
	 * @param bool $isOrigin
	 * @return mixed
	 */
	public static function post(string $url, $data, bool $isOrigin = false, int $timeout = 10){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		if($isOrigin){
			if(is_array($data)){
				$data = json_encode($data, JSON_UNESCAPED_UNICODE);
			}
		}else{
			$data = http_build_query($data);
		}
		$header = array(
				'Content-type: application/x-www-form-urlencoded', 'Content-length: '.strlen($data),
				'Expect:'
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
		$output = curl_exec($ch);
		curl_close($ch);
		if($output=== false){
			if(curl_errno($ch) == CURLE_OPERATION_TIMEDOUT){
				throw new HttpException('请求超时');
			}
		}
		return $output;
	}
	/**
	 * http post
	 * @param unknown $url
	 * @param unknown $data
	 * @param string $isOrigin
	 * @return mixed
	 */
	public static function postMulti($url, $data, $header=[], $isOrigin = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		if($isOrigin){
			$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		}else{
			$data = http_build_query($data);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	
	
}
class HttpException extends \Exception{
	
}
?>