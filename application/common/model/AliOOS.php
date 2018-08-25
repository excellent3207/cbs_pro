<?php 
/**
 * 阿里云存储
 * Author xjp
 * CreateTime 2016/09/21
 */
namespace app\common\model;
use Sts\Request\V20150401\AssumeRoleRequest;
use OSS\OssClient;
use OSS\Core\OssException;
use app\common\exception\AppException;
use vod\Request\V20170321\CreateUploadVideoRequest;
use vod\Request\V20170321\GetVideoInfoRequest;
use vod\Request\V20170321\GetPlayInfoRequest;
use vod\Request\V20170321\RefreshUploadVideoRequest;
use Mts\Request\V20140618\SubmitJobsRequest;
class AliOOS{
	private $id = 'LTAIM2TXK5Qw6H5z';
	private $key = 'XV0BvswTklVXzT65aoxqyjQ5nziLsA';
	private $roleArn = 'acs:ram::1940834383900665:role/upload';
	
	const POLICY_ACTION_GET_OBJECT = 'oss:GetObject';
	const POLICY_ACTION_PUT_OBJECT = 'oss:PutObject';
	const POLICY_ACTION_DELTE_OBJECT = 'oss:DeleteObject';
	const POLICY_ACTION_LIST_PARTS = 'oss:ListParts';
	const POLICY_ACTION_ABORT_MULTIPART_UPLOAD = 'AbortMultipartUpload';
	const POLICY_ACTION_LIST_OBJECTS = 'oss:ListObjects';
	const POLICY_ACTION_VOD = 'vod:*';
	const POLICY_ACTION_ALL = 'oss:*';
	
	const BUCKET_RESOURCE = 'cbs-resource';
	
	private $endpoints = [
	    self::BUCKET_RESOURCE => 'http://cbs-resource.oss-cn-beijing.aliyuncs.com',
	];
	
	public function sts($policy_actions, $paths){
	    include_once env('app_path').'common/aliyun-sdk/aliyun-php-sdk-core/Config.php';
		
		$tokenExpire = '900';
		if($paths === '*'){
			$paths = '*';
		}else{
			array_walk($paths, function(&$v, $k){
				$v = 'acs:oss:*:*:'.$v;
			});
		}
		$policy = json_encode(['Statement' => [
				[
						'Action' => $policy_actions,
						'Effect' => 'Allow',
						'Resource' => $paths
				]
		]]);
		
		$iClientProfile = \DefaultProfile::getProfile("cn-beijing", $this->id, $this->key);
		$client = new \DefaultAcsClient($iClientProfile);
		
		$request = new AssumeRoleRequest();
		$request->setRoleSessionName("client_name");
		$request->setRoleArn($this->roleArn);
		$request->setPolicy($policy);
		$request->setDurationSeconds($tokenExpire);
		$response = $client->getAcsResponse($request);
		$rows = [];
		$rows['AccessKeyId'] = $response->Credentials->AccessKeyId;
		$rows['AccessKeySecret'] = $response->Credentials->AccessKeySecret;
		$rows['Expiration'] = $response->Credentials->Expiration;
		$rows['SecurityToken'] = $response->Credentials->SecurityToken;
		return $rows;
	}
	/**
	 * 分片上传本地文件
	 * @param string $bucket 云存储bucket
	 * @param string $target 存储到oss的文件名（包括目录）,例如content/test.mp3 不能前面加/
	 * @param string $source 本地文件路径
	 * @throws AppException
	 * @return 文件url
	 */
	public function multiUploadFile(string $bucket, string $target,  string $source){
	    require_once env('app_path').'common/oss/OssClient.php';
		try {
			$ossClient = new OssClient($this->id, $this->key, $this->endpoints[$bucket], true);
			$ossClient->multiuploadFile($bucket, $target, $source);
			return $target;
		} catch (OssException $e) {
			throw new AppException($e->getMessage());
		}
	}
	/**
	 * 获取bucket域名
	 * @param string $bucket
	 * @return string
	 */
	public function getBucketDomain(string $bucket){
		return $this->endpoints[$bucket];
	}
	private function init_vod_client() {
		include_once env('app_path').'common/aliyun-sdk/aliyun-php-sdk-core/Config.php';
		$regionId = 'cn-shanghai';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
		$iClientProfile = \DefaultProfile::getProfile($regionId, $this->id, $this->key);
		return new \DefaultAcsClient($iClientProfile);
	}
	/**
	 * 创建点播视频凭证
	 * @return unknown|mixed
	 */
	public function create_upload_video(){
		$client = $this->init_vod_client();
		$request = new CreateUploadVideoRequest();
		$time = date('YmdHis').'-'.rand(1000, 9999);
		//视频源文件标题(必选)
		$request->setTitle("课程".$time);
		//视频源文件名称，必须包含扩展名(必选)
		$request->setFileName($time.".mp4");
		//视频源文件字节数(可选)
		//$request->setFileSize(0);
		//视频源文件描述(可选)
		//$request->setDescription("视频描述");
		//自定义视频封面URL地址(可选)
		//$request->setCoverURL("http://cover.sample.com/sample.jpg");
		//上传所在区域IP地址(可选)
		//$request->setIP("127.0.0.1");
		//视频标签，多个用逗号分隔(可选)
		//$request->setTags("标签1,标签2");
		//视频分类ID(可选)
		//$request->setCateId(0);
		$response = $client->getAcsResponse($request);
		//return $this->get_play_info('d257fb04e6c4431c8144d0f2216b5daf');
		return $response;
	}
	/**
	 * 刷新视频凭证
	 * @param unknown $vid
	 * @return unknown|mixed
	 */
	public function refresh_upload_video($vid){
		$client = $this->init_vod_client();
		$request = new RefreshUploadVideoRequest();
		//视频ID(必选)
		$request->setVideoId($vid);
		$response = $client->getAcsResponse($request);
		return $response;
	}
	/**
	 * 获取点播视频信息
	 * @param unknown $videoId
	 * @return unknown|mixed
	 */
	public function get_video_info($videoId) {
		$client = $this->init_vod_client();
		$request = new GetVideoInfoRequest();
		$request->setVideoId($videoId);
		$request->setAcceptFormat('JSON');
		return $client->getAcsResponse($request);
	}
	public function get_play_info($videoId) {
		$client = $this->init_vod_client();
		$request = new GetPlayInfoRequest();
		$request->setVideoId($videoId);
		$request->setAuthTimeout(3600*24);    // 播放地址过期时间（只有开启了URL鉴权才生效），默认为3600秒，支持设置最小值为3600秒
		$request->setAcceptFormat('JSON');
		return $client->getAcsResponse($request);
	}
	/**
	 * 媒体转码
	 */
	public function mts($inputObj, $outputObj){
		$inputObj = trim($inputObj, '/');
		$outputObj = trim($outputObj, '/');
		include_once env('app_path').'common/aliyun-sdk/aliyun-php-sdk-core/Config.php';
		$access_key_id = $this->id;
		$access_key_secret = $this->key;
		$mps_region_id = 'cn-beijing';
		$pipeline_id = '15dd63c0bff14a6ba19b366f0d66ce54';
		$template_id = 'd684743d50ba42aeb4fd2adeb57afbed';
		$oss_location = 'oss-cn-beijing';
		$oss_bucket = 'syh-resource';
		$oss_input_object = $inputObj;
		$oss_output_object = $outputObj;
		$clientProfile = \DefaultProfile::getProfile(
				$mps_region_id,                   # 您的 Region ID
				$access_key_id,                   # 您的 AccessKey ID
				$access_key_secret                # 您的 AccessKey Secret
				);
		$client = new \DefaultAcsClient($clientProfile);
		$request = new SubmitJobsRequest();
		$request->setAcceptFormat('JSON');
		$input = array('Location' => $oss_location,
				'Bucket' => $oss_bucket,
				'Object' => urlencode($oss_input_object));
		$request->setInput(json_encode($input));
		$output = array('OutputObject' => urlencode($oss_output_object));
		$output['TemplateId'] = $template_id;
		$outputs = array($output);
		$request->setOUtputs(json_encode($outputs));
		$request->setOutputBucket($oss_bucket);
		$request->setOutputLocation($oss_location);
		$request->setPipelineId($pipeline_id);
		try {
			$response = $client->getAcsResponse($request);
		} catch(ServerException $e) {
			print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
		} catch(ClientException $e) {
			print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
		}
	}
	private function gmt_iso8601($time) {
		$dtStr = date("c", $time);
		$mydatetime = new \DateTime($dtStr);
		$expiration = $mydatetime->format(\DateTime::ISO8601);
		$pos = strpos($expiration, '+');
		$expiration = substr($expiration, 0, $pos);
		return $expiration."Z";
	}
}
?>