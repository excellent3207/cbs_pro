<?php
/**
 * 微信模型（逻辑模型）
 * Author xjp
 * CreateTime 2017/08/31
 */
namespace app\common\model;

class CbsWxModel extends AbsWx{
    /**
     * 返回公众号appid，直接返回一个字符串
     */
    protected function appid():string{
        return 'wx7bcc326890668c0f';
    }
    /**
     * 返回公众号密钥，直接返回一个字符串
     * @return string
     */
    protected function secret():string{
        return '5a94687ecf32ff28d38246aa68f41f4a';
    }
    /**
     * 普通消息事件，即用户输入文本消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $content 用户输入内容
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    protected function textCallback($toUser, $fromUser, $content, $msgid, $createtime){
        
    }
    /**
     * 图片消息事件，即用户输入图片消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $picUrl 图片链接（由微信系统生成）
     * @param string $mediaid 图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
     * @param string $msgid 消息ID 消息id，64位整型
     * @param int $createtime 消息时间
     */
    protected function imageCallback($toUser, $fromUser, $picUrl, $mediaid, $msgid, $createtime){
        
    }
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
    protected function voiceCallback($toUser, $fromUser, $mediaid, $format, $recognition, $msgid, $createtime){
        
    }
    /**
     * 视频消息事件，即用户输入视频消息
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $mediaid 视频mediaid，可以调用多媒体文件下载接口拉取数据。
     * @param string $thumbMediaId 视频缩略图
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    protected  function videoCallback($toUser, $fromUser, $mediaid, $thumbMediaId, $msgid, $createtime){
        
    }
    /**
     * 小视频消息事件，即用户输入小视频
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $mediaid 小视频mediaid，可以调用多媒体文件下载接口拉取数据。
     * @param string $thumbMediaId 视频缩略图
     * @param string $msgid 消息ID，64位整型
     * @param int $createtime 消息时间
     */
    protected function shortvideoCallback($toUser, $fromUser, $mediaid, $thumbMediaId, $msgid, $createtime){
        
    }
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
    protected function locationCallback($toUser, $fromUser, $locationX, $locationY, $scale, $label, $msgid, $createtime){
        
    }
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
    protected function linkCallback($toUser, $fromUser, $title, $description, $url, $msgid, $createtime){
        
    }
    /**
     * 用户关注事件回调
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param int $createtime 消息时间
     */
    protected function subscribeCallback($toUser, $fromUser, $createtime){
        
    }
    /**
     * 用户取消关注事件回调
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param int $createtime 消息时间
     */
    protected function unsubscribeCallback($toUser, $fromUser, $createtime){
        
    }
    /**
     * 用户扫描二维码未关注用户 关注公众号后事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $param 带参二维码的参数值，即：scene_id或scene_str
     * @param string $tiket 二维码的ticket，可用来换取二维码图片
     * @param int $createtime 消息时间
     */
    protected function scanUnsubscribeCallback($toUser, $fromUser,$param,$tiket, $createtime){
        $this->scanCode($toUser, $fromUser, $param, $tiket, $createtime, false);
    }
    /**
     * 用户扫描二维码已关注用户 直接进入公众号 事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $param 带参二维码的参数值，即：scene_id或scene_str
     * @param string $tiket 二维码的ticket，可用来换取二维码图片
     * @param int $createtime 消息时间
     */
    protected function scanSubscribeCallback($toUser, $fromUser,$param,$tiket, $createtime){
        $this->scanCode($toUser, $fromUser, $param, $tiket, $createtime, true);
    }
    private function scanCode($toUser, $fromUser,$param,$tiket, $createtime, $hasScribe){
        
    }
    /**
     * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $latitude 地理位置纬度
     * @param string $longitude 地理位置经度
     * @param string $precision 地理位置精度
     * @param int $createtime 消息时间
     */
    protected function getLocationCallback($toUser, $fromUser,$latitude, $longitude, $precision, $createtime){
        
    }
    /**
     * 点击菜单拉取消息时的事件推送
     * @param unknown $toUser 开发者微信号
     * @param unknown $fromUser 用户openid
     * @param unknown $eventKey 事件KEY值，与自定义菜单接口中KEY值对应
     * @param int $createtime 消息时间
     */
    protected function clickMenuMsgCallback($toUser, $fromUser, $eventKey, $createtime){
        
    }
    /**
     * 点击菜单跳转链接时的事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $eventKey 事件KEY值，与自定义菜单接口中KEY值对应
     * @param int $createtime 消息时间
     */
    protected function clickMenuViewCallback($toUser, $fromUser, $url, $createtime){
        
    }
    /**
     * 推送模版消息 事件推送
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param string $msgid 消息ID
     * @param string $status 消息状态 success 成功  userblock 用户拒绝 systemfail 发送状态为发送失败（非用户拒绝）
     * @param int $createtime 消息时间
     */
    protected function templateSendMsgCallback($toUser, $fromUser, $msgid, $status, $createtime){
        
    }
    /**
     * 推送通用模版消息
     * @param string $toUser
     * @param string $templateid
     * @param unknown $url
     * @param unknown $data
     */
    public function sendCommonMsg(string $toUser, string $templateid, string $url, array $data, string $appid = '', string $pagepath = ''){
        $this->sendTemplateMsgBase($toUser, $templateid, $url, $data, $appid, $pagepath);
    }
}
?>