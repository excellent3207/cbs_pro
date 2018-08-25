<?php
/**
 * 微信模型（逻辑模型）
 * Author xjp
 * CreateTime 2017/08/31
 */
namespace app\common\model;
use app\common\AppRedis;
use think\Log;
use app\common\AppHttp;
abstract class AbsWx{
    protected $appid = '';
    protected  $secret = '';
    protected $wxCacheKey = '';
    protected $wxCacheTiket = '';
    
    function __construct(){
        $this->appid = $this->appid();
        $this->secret = $this->secret();
        $this->wxCacheKey = $this->appid.':key';
        $this->wxCacheTiket = $this->appid.':tiket';
    }
    
    /**
     * 返回公众号appid，直接返回一个字符串
     */
    abstract protected function appid():string;
    /**
     * 返回公众号密钥，直接返回一个字符串
     * @return string
     */
    abstract protected function secret():string;
    /**
     * 普通消息事件，即用户输入文本消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $content 用户输入内容
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    abstract protected function textCallback($toUser, $fromUser, $content, $msgid, $createtime);
    /**
     * 图片消息事件，即用户输入图片消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $picUrl 图片链接（由微信系统生成）
     * @param string $mediaid 图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * @param string $msgid 消息ID 消息id，64位整型
     * @param int $createtime 消息时间
     */
    abstract protected function imageCallback($toUser, $fromUser, $picUrl, $mediaid, $msgid, $createtime);
    /**
     * 声音消息事件，即用户输入语音消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $mediaid 声音mediaid，可以调用多媒体文件下载接口拉取数据。
     * @param string $format 声音格式，如amr，speex等
     * @param string $recognition 语音识别结果，UTF8编码，没有开启语音识别功能此字段为空
     * @param string $msgid 消息ID
     * @param int $createtime 消息时间
     */
    abstract protected function voiceCallback($toUser, $fromUser, $mediaid, $format, $recognition, $msgid, $createtime);
    /**
     * 视频消息事件，即用户输入视频消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $mediaid 视频mediaid，可以调用多媒体文件下载接口拉取数据。
     * @param string $thumbMediaId 视频缩略图
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    abstract protected  function videoCallback($toUser, $fromUser, $mediaid, $thumbMediaId, $msgid, $createtime);
    /**
     * 小视频消息事件，即用户输入小视频
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $mediaid 小视频mediaid，可以调用多媒体文件下载接口拉取数据。
     * @param string $thumbMediaId 视频缩略图
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    abstract protected function shortvideoCallback($toUser, $fromUser, $mediaid, $thumbMediaId, $msgid, $createtime);
    /**
     * 地理位置消息事件，即用户输入地理位置
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $locationX 地理位置维度
     * @param string $locationY 地理位置经度
     * @param string $scale 地图缩放大小
     * @param string $label 地理位置信息
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    abstract protected function locationCallback($toUser, $fromUser, $locationX, $locationY, $scale, $label, $msgid, $createtime);
    /**
     * 链接消息事件，即用户输入链接
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $title 消息标题
     * @param string $description 消息描述
     * @param string $url 消息链接
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    abstract protected function linkCallback($toUser, $fromUser, $title, $description, $url, $msgid, $createtime);
    /**
     * 用户关注事件回调
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param int $createtime 消息时间
     */
    abstract protected function subscribeCallback($toUser, $fromUser, $createtime);
    /**
     * 用户取消关注事件回调
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param int $createtime 消息时间
     */
    abstract protected function unsubscribeCallback($toUser, $fromUser, $createtime);
    /**
     * 用户扫描二维码未关注用户 关注公众号后事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $param 带参二维码的参数值，即：scene_id或scene_str
     * @param string $tiket 二维码的ticket，可用来换取二维码图片
     * @param int $createtime 消息时间
     */
    abstract protected function scanUnsubscribeCallback($toUser, $fromUser,$param,$tiket, $createtime);
    /**
     * 用户扫描二维码已关注用户 直接进入公众号 事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $param 带参二维码的参数值，即：scene_id或scene_str
     * @param string $tiket 二维码的ticket，可用来换取二维码图片
     * @param int $createtime 消息时间
     */
    abstract protected function scanSubscribeCallback($toUser, $fromUser,$param,$tiket, $createtime);
    /**
     * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $latitude 地理位置纬度
     * @param string $longitude 地理位置经度
     * @param string $precision 地理位置精度
     * @param int $createtime 消息时间
     */
    abstract protected function getLocationCallback($toUser, $fromUser,$latitude, $longitude, $precision, $createtime);
    /**
     * 点击菜单拉取消息时的事件推送
     * @param unknown $toUser 开发者微信号
     * @param unknown $fromUser 用户openid
     * @param unknown $eventKey 事件KEY值，与自定义菜单接口中KEY值对应
     * @param int $createtime 消息时间
     */
    abstract protected function clickMenuMsgCallback($toUser, $fromUser, $eventKey, $createtime);
    /**
     * 点击菜单跳转链接时的事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $eventKey 事件KEY值，与自定义菜单接口中KEY值对应
     * @param int $createtime 消息时间
     */
    abstract protected function clickMenuViewCallback($toUser, $fromUser, $url, $createtime);
    /**
     * 推送模版消息 事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $msgid 消息ID
     * @param string $status 消息状态 success 成功  userblock 用户拒绝 systemfail 发送状态为发送失败（非用户拒绝）
     * @param int $createtime 消息时间
     */
    abstract protected function templateSendMsgCallback($toUser, $fromUser, $msgid, $status, $createtime);
    /**
     * jssdk config
     */
    public function createJsConfig(string $url){
        $config = array(
            'jsapi_ticket' => $this->getJsApiTicket(),
            'noncestr' => md5(time() + 'linglanshuyuan'),
            'timestamp' => time(),
            'url' => $url
        );
        $config['signature'] = sha1(urldecode(http_build_query($config)));
        $config['appid'] = $this->appid;
        return $config;
    }
    /**
     * 获取永久带参二维码
     * @param string $sceneid
     * 		$isSceneStr为false 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     * 		$isSceneStr为true 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
     * @param string $isSceneStr 是否是字符串形式
     * @throws WxException
     * @return string 二维码url
     */
    public function getLongCode($sceneid, $isSceneStr = true):string{
        return $this->getCodeBase(true, $sceneid, 0, $isSceneStr);
    }
    /**
     * 获取临时带参二维码
     * @param string $sceneid
     * 		$isSceneStr为false 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
     * 		$isSceneStr为true 场景值ID（字符串形式的ID），字符串类型，长度限制为1到64
     * @param int $expire 该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
     * @param string $isSceneStr 是否是字符串形式
     * @return string 二维码url
     */
    public function getTmpCode($sceneid, $expire = 30, $isSceneStr = true){
        return $this->getCodeBase(false, $sceneid, $expire, $isSceneStr);
    }
    /**
     * 获取带参二维码基本方法
     * @param unknown $isLong
     * @param unknown $sceneid
     * @param unknown $expire
     * @param string $isSceneStr
     * @throws WxException
     * @return string
     */
    private function getCodeBase($isLong, $sceneid, $expire, $isSceneStr = true):string{
        $token = $this->getToken();
        if(!$token) throw new WxException('获取二维码token失败');
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$token;
        if($isLong){
            $data = ['action_name'	=> 'QR_LIMIT_STR_SCENE'];
        }else{
            $data = ['action_name'	=> 'QR_STR_SCENE', 'expire_seconds', $expire];
        }
        
        if($isSceneStr){
            $data['action_info']['scene']['scene_str'] = $sceneid;
        }else{
            $data['action_info']['scene']['scene_id'] = $sceneid;
        }
        $res = AppHttp::post($url, $data, true);
        $res = json_decode($res, true);
        if(!isset($res['ticket'])) throw new WxException('获取二维码ticket失败');
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$res['ticket'];
    }
    /**
     * 微信服务器验证
     * @param string $echoStr
     * @param string $signature
     * @param string $timestamp
     * @param string $nonce
     * @param string $token
     * @return string
     */
    public function valid($echoStr, $signature, $timestamp, $nonce, $token){
        if($this->checkSignature($signature, $timestamp, $nonce, $token)){
            return $echoStr;
        }else{
            return '';
        }
    }
    /**
     * 接收微信服务器响应处理
     * @param unknown $postData
     * @throws WxException
     * @return string
     */
    public function responseMsg($postData){
        if(empty($postData)) throw new WxException('数据不能为空');
        $textTpl = '';
        $msgType = trim($postData['MsgType']);
        switch($msgType){
            case 'text':
                $this->textCallback($postData['ToUserName'], $postData['FromUserName'], $postData['Content'], $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'image':
                $this->imageCallback($postData['ToUserName'], $postData['FromUserName'], $postData['PicUrl'], $postData['MediaId'], $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'voice':
                $recognition = $postData['Recognition']??'';
                $this->voiceCallback($postData['ToUserName'], $postData['FromUserName'], $postData['MediaId'], $postData['Format'], $recognition,
                    $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'video':
                $this->videoCallback($postData['ToUserName'], $postData['FromUserName'], $postData['MediaId'], $postData['ThumbMediaId'],
                $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'shortvideo':
                $this->shortvideoCallback($postData['ToUserName'], $postData['FromUserName'], $postData['MediaId'], $postData['ThumbMediaId'],
                $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'location':
                $this->locationCallback($postData['ToUserName'], $postData['FromUserName'], $postData['Location_X'], $postData['Location_Y'], $postData['Scale'],
                $postData['Label'], $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'link':
                $this->linkCallback($postData['ToUserName'], $postData['FromUserName'], $postData['Title'], $postData['Description'], $postData['Url'],
                $postData['MsgId'], $postData['CreateTime']);
                break;
            case 'event':
                switch($postData['Event']){
                    case 'subscribe':
                        $this->subscribeCallback($postData['ToUserName'], $postData['FromUserName'], $postData['CreateTime']);
                        if(isset($postData['Ticket'])){
                            $param = str_replace('qrscene_', '', $postData['EventKey']);
                            $this->scanUnsubscribeCallback($postData['ToUserName'], $postData['FromUserName'],$param,
                                $postData['Ticket'], $postData['CreateTime']);
                        }
                        break;
                    case 'unsubscribe':
                        $this->unsubscribeCallback($postData['ToUserName'], $postData['FromUserName'], $postData['CreateTime']);
                        break;
                    case 'SCAN':
                        $this->scanSubscribeCallback($postData['ToUserName'], $postData['FromUserName'],$postData['EventKey'],
                        $postData['Ticket'], $postData['CreateTime']);
                        break;
                    case 'LOCATION':
                        $this->getLocationCallback($postData['ToUserName'], $postData['FromUserName'], $postData['Latitude'],
                        $postData['Longitude'], $postData['Precision'], $postData['CreateTime']);
                        break;
                    case 'CLICK':
                        $this->clickMenuMsgCallback($postData['ToUserName'], $postData['FromUserName'], $postData['EventKey'],
                        $postData['CreateTime']);
                        break;
                    case 'VIEW':
                        $this->clickMenuViewCallback($postData['ToUserName'], $postData['FromUserName'], $postData['EventKey'],
                        $postData['CreateTime']);
                        break;
                    case 'TEMPLATESENDJOBFINISH':
                        $status = '';
                        switch($postData['Status']){
                            case 'success':
                                $status = 'success';
                                break;
                            case 'failed:user block':
                                $status = 'userblock';
                                break;
                            case 'failed:system failed':
                                $status = 'systemfail';
                                break;
                        }
                        $this->templateSendMsgCallback($postData['ToUserName'], $postData['FromUserName'], $postData['MsgID'],
                            $status, $postData['CreateTime']);
                        break;
                }
        }
        return $textTpl;
    }
    /*******************************************发送客服消息*************************************************
     * 当用户和公众号产生特定动作的交互时（具体动作列表请见下方说明），微信将会把消息数据推送给开发者，
     * 开发者可以在一段时间内（目前修改为48小时）调用客服接口
     * 事件：
     * 1、用户发送信息
     * 2、点击自定义菜单（仅有点击推事件、扫码推事件、扫码推事件且弹出“消息接收中”提示框这3种菜单类型是会触发客服接口的）
     * 3、关注公众号
     * 4、扫描二维码
     * 5、支付成功
     * 6、用户维权
     *********************************************************************************************/
    /**
     * 发送客服文本消息
     * @param string $toUser 用户openid
     * @param string $content 文本内容
     */
    public function sendKfMsgText($toUser, $content){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'text',
            'text' => ['content' => $content]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服图片消息
     * @param string $toUser 用户openid
     * @param string $mediaid 媒体ID
     */
    public function sendKfMsgImage($toUser, $mediaid){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'image',
            'image' => ['media_id' => $mediaid]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服语音消息
     * @param string $toUser 用户openid
     * @param string $mediaid 媒体ID
     */
    public function sendKfMsgVoice($toUser, $mediaid){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'voice',
            'voice' => ['media_id' => $mediaid]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服视频消息
     * @param string $toUser 用户openid
     * @param string $mediaid 媒体ID
     * @param string $thumbMediaid 缩略图ID
     * @param string $title 视频标题
     * @param string $description 视频描述
     */
    public function sendKfMsgVideo($toUser, $mediaid, $thumbMediaid, $title, $description){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'video',
            'video' => ['media_id' => $mediaid, 'thumb_media_id' => $thumbMediaid, 'title' => $title, 'description' => $description]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服音乐消息
     * @param string $toUser 用户openid
     * @param string $thumbMediaid 缩略图ID
     * @param string $title 音乐标题
     * @param string $description 音乐描述
     * @param string $musicurl 音乐地址
     * @param string $hqmusicurl 高品质音乐地址
     */
    public function sendKfMsgMusic($toUser, $thumbMediaid, $title, $description, $musicurl, $hqmusicurl){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'music',
            'music' => ['media_id' => $mediaid, 'thumb_media_id' => $thumbMediaid,
                'title' => $title, 'description' => $description, 'musicurl' => $musicurl, 'hqmusicurl' => $hqmusicurl]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服图文消息
     * @param string $toUser 用户openid
     * @param array $articles 文章数组，格式为：
     * 	[
     * 		[
     * 			"title" => "标题",
     * 			"description":"描述",
     * 			"url":"跳转地址",
     * 			"picurl":"图片地址"
     * 		],
     * 		[...]...
     * ]
     */
    public function sendKfMsgNews($toUser, array $articles){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'news',
            'news' => [
                'articles' => $articles
            ]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服图文消息--从素材库选择
     * @param string $toUser 用户openid
     * @param string $mediaid 媒体ID
     */
    public function sendKfMsgMpNews($toUser, $mediaid){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'mpnews',
            'mpnews' => [
                'media_id' => $mediaid
            ]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服卡券消息
     * @param string $toUser 用户openid
     * @param string $cardid 卡券ID
     */
    public function sendKfMsgCard($toUser, $cardid){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'wxcard',
            'wxcard' => [
                'card_id' => $cardid
            ]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送小程序卡片（要求小程序与公众号已关联）
     * @param unknown $toUser 用户openid
     * @param unknown $title 卡片标题
     * @param unknown $appid 小程序的appid，要求小程序的appid需要与公众号有关联关系
     * @param unknown $pagepath 小程序的页面路径，跟app.json对齐，支持参数，比如pages/index/index?foo=bar
     * @param unknown $thumbMediaId 缩略图ID
     */
    public function sendKfMsgMinipro($toUser, $title, $appid, $pagepath, $thumbMediaId){
        $data = [
            'touser' => $toUser,
            'msgtype' => 'miniprogrampage',
            'miniprogrampage' => [
                'title' => $title,
                'appid' => $appid,
                'pagepath' => $pagepath,
                'thumb_media_id' => $thumbMediaId
            ]
        ];
        $this->sendKfMsg($data);
    }
    /**
     * 发送客服消息基本方法
     * @param array $data 发送的数据
     */
    private function sendKfMsg(array $data){
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$token;
        $res = AppHttp::post($url, $data, true);
        $data = json_decode($res, true);
        if(!empty($data)){
            if($data['errcode'] != 0){
                throw new WxException($data['errmsg']);
            }
        }else{
            throw new WxException('发送客服消失返回值错误');
        }
        return true;
    }
    /**
     * 发送模版消息基础方法
     * @param unknown $toUser 用户openid
     * @param unknown $templateId 模版消息的模版ID
     * @param unknown $url 消息跳转的url
     * @param unknown $data 模版数据，格式为['first' => ['value' => '恭喜你', 'color' => '#173177'], 'second' => [],...]
     * @param string $appid 所需跳转到的小程序appid（该小程序appid必须与发模板消息的公众号是绑定关联关系）,不传代表不跳转小程序
     * @param string $pagepath 所需跳转到小程序的具体页面路径，支持带参数,（示例index?foo=bar）
     * @throws WxException
     * @return string 消息ID
     */
    protected function sendTemplateMsgBase($toUser, $templateId, $url, $data, $appid = '', $pagepath = ''):string{
        $token = $this->getToken();
        $sendData = [
            'touser' => $toUser,
            'template_id' => $templateId,
            'url' => urldecode($url),
            'data' => $data
        ];
        if($appid){
            $sendData['miniprogram'] = ['appid' => $appid, 'pagepath' => $pagepath];
        }
        $params = json_encode($sendData,JSON_UNESCAPED_UNICODE);
        $fp = fsockopen('api.weixin.qq.com', 80, $error, $errstr, 1);
        $http = "POST /cgi-bin/message/template/send?access_token={$token} HTTP/1.1\r\nHost: api.weixin.qq.com\r\nContent-type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($params) . "\r\nConnection:close\r\n\r\n$params\r\n\r\n";
        fwrite($fp, $http);
        fclose($fp);
        return true;
    }
    /**
     * 通过openid获取用户信息
     * @param unknown $openid
     * @return boolean|mixed
     */
    public function getInfoByOpenid($openid){
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$openid.'&lang=zh_CN ';
        $data = AppHttp::get($url);
        $dataArr = json_decode($data, true);
        if(isset($dataArr['errcode']) && $dataArr['errcode'] == 40001){
            $this->clearTokenCache();
            $data = AppHttp::get($url);
            $dataArr = json_decode($data, true);
        }
        if(empty($dataArr) || isset($dataArr['errcode'])){
            Log::record('通过openid获取用户信息失败：'.$data, 'log');
            return false;
        }
        return $dataArr;
    }
    /**
     * 获取短链接
     * @param unknown $longURL
     * @return mixed|boolean
     */
    public function getShortURL($longURL){
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token='.$token;
        $data = AppHttp::post($url, ['action' => 'long2short', 'long_url' => $longURL], true);
        $dataArr = json_decode($data, true);
        if(isset($dataArr['errcode']) && $dataArr['errcode'] == 0){
            return $dataArr['short_url'];
        }else{
            Log::record('获取短链接失败：url='.$longURL);
            return false;
        }
    }
    
    /**
     * 清空token缓存
     */
    public function clearTokenCache(){
        cache($this->wxCacheKey, NULL);
    }
    /**
     * 获取token
     * @return unknown|mixed|\think\cache\Driver|boolean
     */
    protected function getToken(){
        $token = AppRedis::instance()->get($this->wxCacheKey);
        if(!$token){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
            $res = AppHttp::get($url);
            $data = json_decode($res, true);
            if(isset($data['access_token'])){
                $token = $data['access_token'];
                $expire = 7200;
                AppRedis::instance()->set($this->wxCacheKey, $token, $expire);
            }
        }
        return $token;
    }
    /**
     * 获取js ticket
     * @return mixed|unknown
     */
    protected function getJsApiTicket(){
        $ticket = AppRedis::instance()->get($this->wxCacheTiket);
        if(!$ticket){
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$this->getToken().'&type=jsapi';
            $data = json_decode(AppHttp::get($url), true);
            if(isset($data['ticket'])){
                $ticket = $data['ticket'];
                $expire = $data['expires_in'] ? $data['expires_in'] : 7000;
                AppRedis::instance()->set($this->wxCacheTiket, $ticket, $expire);
            }
        }
        return $ticket;
    }
    protected function checkSignature($signature, $timestamp, $nonce, $token){
        // you must define TOKEN by yourself
        if (!$token) {
            return false;
        }
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 网页授权登录
     */
    public function authLogin(string $url, string $code){
        $ret = ['action' => 'info', 'data' => ''];
        $url = urlencode($url);
        if($code){
            $getUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='
                .$this->secret.'&code='.$code.'&grant_type=authorization_code';
                $accessToken = json_decode(AppHttp::get($getUrl), true);
                if(isset($accessToken['access_token']) && !isset($accessToken['errorcode'])){
                    $info = $this->getInfoByAuthOpenid($accessToken['openid'], $accessToken['access_token']);
                    if(!empty($info)){
                        $subInfo = $this->getInfoByOpenid($accessToken['openid']);
                        if(!empty($subInfo) && isset($subInfo['subscribe'])){
                            $info['issubscribe'] = $subInfo['subscribe'];
                        }else{
                            $info['issubscribe']  = 0;
                        }
                        $ret['data'] = $info;
                    }else{
                        $ret['action'] = 'redirect';
                        $ret['data'] = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                            .$this->appid."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo#wechat_redirect";
                    }
                }else{
                    $ret['action'] = 'redirect';
                    $ret['data'] = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                        .$this->appid."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo#wechat_redirect";
                }
        }else{
            $ret['action'] = 'redirect';
            $ret['data'] = "https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                .$this->appid."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo#wechat_redirect";
        }
        return $ret;
    }
    public function getInfoByAuthOpenid($openid, $access_token){
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN ';
        $data = AppHttp::get($url);
        $dataArr = json_decode($data, true);
        if(empty($dataArr) || isset($dataArr['errcode'])){
            Log::record('通过openid获取用户信息失败：'.$data, 'log');
            return false;
        }
        return $dataArr;
    }
    /**
     * 永久素材列表
     * 图文字段：
     "media_id": MEDIA_ID,
     "title": TITLE,
     "thumb_media_id": THUMB_MEDIA_ID,
     "show_cover_pic": SHOW_COVER_PIC(0 / 1),
     "author": AUTHOR,
     "digest": DIGEST,
     "content": CONTENT,
     "url": URL,
     "content_source_url": CONTETN_SOURCE_URL
     "update_time": UPDATE_TIME
     * @param int $offset
     * @param int $count
     * @param string $type
     * @throws WxException
     * @return string
     */
    public function materialList(int $offset, int $count, string $type = 'news'){
        $token = $this->getToken();
        if(!$token) throw new WxException('获取token失败');
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$token;
        $res = AppHttp::post($url, ['type' => $type, 'offset' => $offset, 'count' => $count], true);
        $res = json_decode($res, true);
        if(!isset($res['total_count'])) throw new WxException('获取素材列表失败');
        $data = [];
        if($type == 'news'){
            foreach($res['item'] as $v){
                foreach($v['content']['news_item'] as $sv){
                    $item = $sv;
                    $item['total_count'] = $res['total_count'];
                    $item['media_id'] = $v['media_id'];
                    $item['update_time'] = $v['update_time'];
                    array_push($data, $item);
                }
            }
        }else{
            foreach($res['item'] as $v){
                array_push($data, $v);
            }
        }
        return $data;
    }
    /**
     * 素材总数
     * @throws WxException
     * @return mixed
     */
    public function materialCount(){
        $token = $this->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token='.$token;
        $res = json_decode(AppHttp::get($url), true);
        if(isset($res['errcode'])) throw new WxException('获取素材总数失败');
        return $res;
    }
}
class WxException extends \Exception{
    
}
?>