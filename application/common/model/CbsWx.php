<?php
/**
 * 微信模型（逻辑模型）
 * Author xjp
 * CreateTime 2017/08/31
 */
namespace app\common\model;

use think\Db;
use think\Log;
use app\common\AppRedis;
use app\common\AppTool;

class CbsWx extends AbsWx{
    /**
     * 返回公众号appid，直接返回一个字符串
     */
    protected function appid():string{
        //正式
        return 'wx88a762bd437e65b6';
        //测试
        //return 'wx41f78158b9c6a1ea';
    }
    /**
     * 返回公众号密钥，直接返回一个字符串
     * @return string
     */
    protected function secret():string{
        //正式
        return '4db966319b8ca4908ec97f3c61507d29';
        //测试
        //return 'b3e0c5158be753b6eda2f9233c22c68e';
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
        $wxuser = $this->getInfoByOpenid($fromUser);
        if(isset($wxuser['errcode'])){
            Log::write('关注回调：openid:'.$fromUser.',获取微信信息失败，'.$wxuser['errmsg']);
            return ;
        }
        $unionid = $wxuser['unionid'];
        $time = $_SERVER['REQUEST_TIME'];
        $res = Db::table('syh_subscribe')->where(['unionid' => $unionid])->order('iscancel asc')->find();
        if(empty($res)){
            $res = Db::table('syh_subscribe')->insert(['openid' => $fromUser, 'unionid' => $unionid,
                'addtime' => $time, 'updatetime' => $time]);
            if(!$res) Log::write('关注回调：存储用户关注表：失败，openid:'.$fromUser.',unionid:'.$unionid,',time:'.$time);
        }else{
            if($res['iscancel'] != 0){
                $res = Db::table('syh_subscribe')->where('id', $res['id'])->update(['iscancel' => 0, 'updatetime' => $time]);
                if(!$res) Log::write('关注回调：修改用户关注表：失败，openid:'.$fromUser.',unionid:'.$unionid,',time:'.$time);
            }
        }
        $user = Db::table('syh_user_login')->where(['wxopenid' => $fromUser, 'status' => 0])->find();
        if(!empty($user) && $user['issubscribe'] != 1){
            $res = Db::table('syh_user_login')->where('userid', $user['userid'])->update(['issubscribe' => 1, 'updatetime' => $time]);
            if(!$res){
                Log::write('关注回调：修改用户表：失败,userid:'.$user['userid'].',wxopneid:'.$fromUser,',time:'.$time);
            }
        }
        return;
    }
    /**
     * 用户取消关注事件回调
     * @param string $toUser 开发者微信号
     * @param string $fromUser 用户openid
     * @param int $createtime 消息时间
     */
    protected function unsubscribeCallback($toUser, $fromUser, $createtime){
        $time = $_SERVER['REQUEST_TIME'];
        $res = Db::table('syh_subscribe')->where('openid', $fromUser)->order('iscancel asc')->find();
        if(!empty($res) && $res['iscancel'] != 1){
            $res = Db::table('syh_subscribe')->where('id', $res['id'])
            ->update(['iscancel' => 1, 'updatetime' => $time]);
            if(!$res){
                Log::write('取消关注回调：存储用户关注表：失败,openid:'.$fromUser.',wxopenid:'.$fromUser,',time:'.$time);
            }
        }
        $user = Db::table('syh_user_login')->where(['wxopenid' => $fromUser, 'status' => 0])->find();
        if(!empty($user) && $user['issubscribe'] != 0){
            $res = Db::table('syh_user_login')->where(['userid' => $user['userid'], 'status' => 0])->update(['issubscribe' => 0,
                'updatetime' => $time]);
            if(!$res){
                Log::write('取消关注回调：修改用户表：失败,userid:'.$user['userid'].',wxopenid:'.$fromUser,',time:'.$time);
            }
        }
        return;
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
        $code = Db::table('syh_wx_code')->where(['scene' => $param, 'status' => 0])->find();
        if(!empty($code)){
            if($code['partyid']){
                $party = Db::table('syh_third_party')->where(['id' => $code['partyid'], 'status' => 0])->find();
                if(!empty($party)){
                    if(strpos($code['url'], '?') === false){
                        $code['url'] .= '?third_party='.$party['tag'];
                    }else{
                        $code['url'] .= '&third_party='.$party['tag'];
                    }
                    $res = Db::table('syh_third_party_subscribe')->insert(['openid' => $fromUser, 'hassubscribe' => $hasScribe ? 1 : 0,
                        'codeid' => $code['id'], 'addtime' => $_SERVER['REQUEST_TIME'], 'updatetime' => $_SERVER['REQUEST_TIME']]);
                    if(!$res) Log::write("创建扫描二维码事件失败，openid={$fromUser},codeid={$code['id']}");
                }
            }
            try{
                $this->sendKfMsgNews($fromUser,
                    [['title' => $code['title'],
                        'description' => $code['descr'],
                        'url' => $code['url'],
                        'picurl' => AppTool::formatImgUrl($code['img'])]]);
            }catch(WxException $e){
                Log::write('扫描二维码回调失败，'.$e->getMessage());
            }
        }
        /*if($param == '311801PRC_20180125111801_6039'){
         $course = Db::table('syh_course')->where('courseid', 18)->find();
         try{
         $this->sendKfMsgNews($fromUser,
         [['title' => '新年大课 | 左常波：疼痛治疗的终极之道',
         'description' => '超值网课，突破时空，从董氏奇穴入门，无招胜有招！......',
         'url' => 'https://h5.zhongyishuyou.com/wx_h5/Course/info?courseid=18',
         'picurl' => AppTool::formatImgUrl('/app/20171221/zcb_course.png')]]);
         }catch(WxException $e){
         Log::write('扫描二维码回调失败，'.$e->getMessage());
         }
         }*/
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
        /*switch($status){
         case 'success':
         $statusCode = 20;
         break;
         case 'userblock':
         $statusCode = 21;
         break;
         case 'systemfail':
         $statusCode = 22;
         break;
         default:
         $statusCode = 23;
         break;
         }
         $res = Db::table('syh_course_update_send_user')->where('msgid', $msgid)->update(['status' => $statusCode,
         'updatetime' => $_SERVER['REQUEST_TIME']]);
         if(!$res) Log::write('更新推送状态失败， msgid='.$msgid);*/
    }
    /**
     * 客服消息提醒
     * @param string $alias
     * @param string $suggest
     * @param string $reply
     * @return string
     */
    public function sendKefuReply(int $userid, string $suggest, string $reply, string $url){
        $user = Db::table('syh_user_login a')->field('a.userid,a.wxopenid,a.issubscribe,b.alias')
        ->join('syh_user b', 'a.userid=b.userid', 'LEFT')
        ->where(['a.userid' => $userid])->find();
        if(!empty($user) && $user['wxopenid'] && $user['issubscribe']){
            $item = [
                'first' => [
                    'value' => '意见反馈回复通知',
                    'color' => '#000000'
                ],
                'keyword1' => [
                    'value' => $user['alias'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $suggest,
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $reply,
                    'color' => '#173177'
                ],
                'keyword4' => [
                    'value' => '灵兰中医客服',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '点击进入查看回复内容',
                    'color' => '#173177'
                ]
            ];
            $res = $this->sendTemplateMsgBase($user['wxopenid'], 'GF0jjFcdHZfrTYo4tf4mirUYek0Rrjkh0SjZlgo7KDE', $url, $item);
        }
        return true;
    }
    /**
     * 客服消息提醒
     * @param string $alias
     * @param string $suggest
     * @param string $reply
     * @return string
     */
    public function sendYearVipLast(int $partition){
        if(!$partition){
            echo '分区不能为空';exit;
        }
        $sucessFile = RUNTIME_PATH.'success_send_part_'.$partition.'.txt';
        $failFile = RUNTIME_PATH.'fail_send_part_'.$partition.'.txt';
        $key = 'sendYearVipLast_part_'.$partition;//'sendYearVipLast_page_5';//'sendYearVipLast_page_4';//'sendYearVipLast_page_3';//'sendYearVipLast_page_2';//'sendYearVipLast_page_1';//'sendYearVipLast_page';
        $redis = AppRedis::instance();
        $page = $redis->get($key);
        $minPage = ($partition-1) * 72 + 1;
        $maxPage = $partition * 72;
        if(!$page){
            if($partition == 11){
                $page = 730;
            }else{
                $page = $minPage;
            }
        }else{
            if($page >= $maxPage){
                echo  'all';
                exit;
            }
            $page++;
        }
        $redis->set($key, $page);
        $list = Db::table('syh_subscribe')->page($page, 50)->where('iscancel', 0)->select();
        foreach($list as $info){
            $openid = $info['openid'];
            $user = $this->getInfoByOpenid($openid);
            if(isset($user['nickname'])){
                $name = $user['nickname'].' 您好，';
            }else{
                $name = '';
            }
            $isSend = false;
            $userinfo = Db::table('syh_user_login')->where(['unionid' => $info['unionid'], 'status' => 0])->find();
            if(!empty($userinfo)){
                $roleids = explode(',', $userinfo['roleids']);
                if(!in_array(11, $roleids)){
                    $isSend = true;
                }
            }else{
                $isSend = true;
            }
            if($isSend){
                $item = [
                    'first' => [
                        'value' => $name.'灵兰邀请20位明师讲授的中医精品课程，6月8日24:00前参加最划算，之后将变更价格，抓紧喽！',
                        'color' => '#990000'
                    ],
                    'keyword1' => [
                        'value' => '灵兰年度会员课程',
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => '一年内20节精品课，学习中医临床思维和技法。',
                        'color' => '#173177'
                    ],
                    'remark' => [
                        'value' => '若有打扰，敬请谅解!',
                        'color' => '#000000'
                    ]
                ];
                try{
                    $res = $this->sendTemplateMsgBase($openid, 'bY08wr7-N3XlGeGTn7T68V2rGrTN5tbyOjfpW66so0w',
                        'http://h5.zhongyishuyou.com/wx_h5/User/vippage?third_party=NDQxYWI3MGExN2YyM2YxYWMzMmRkYzBjMWFiYzk3NTA', $item);
                    file_put_contents($sucessFile, $info['id']."\n", FILE_APPEND);
                }catch(WxException $e){
                    file_put_contents($failFile, $info['id'].'---'.$e->getMessage()."\n", FILE_APPEND);
                }
            }
        }
        return true;
    }
    
    /**
     * 客服消息提醒
     * @param string $alias
     * @param string $suggest
     * @param string $reply
     * @return string
     */
    public function sendYearVipLastFile($part = 12){
        ini_set('max_execution_time', 0);
        $sucessFile = RUNTIME_PATH.'success_send_new1_'.$part.'.txt';
        $failFile = RUNTIME_PATH.'fail_send_new1_'.$part.'.txt';
        $path = RUNTIME_PATH.'notemplate'.$part.'.txt';
        $ids = [];
        $file = fopen($path,"r");
        while(!feof($file)){
            array_push($ids, trim(fgets($file)));
        }
        fclose($file);
        $ids = array_filter($ids);
        $list = Db::table('syh_subscribe')->where(['id' => ['in', $ids]])->select();
        Db::getConnection()->closeConnect();
        foreach($list as $info){
            $openid = $info['openid'];
            $user = $this->getInfoByOpenid($openid);
            if(isset($user['nickname'])){
                $name = $user['nickname'].' 您好，';
            }else{
                $name = '';
            }
            $item = [
                'first' => [
                    'value' => $name.'灵兰邀请20位明师讲授的中医精品课程，6月8日24:00前参加最划算，之后将变更价格，抓紧喽！',
                    'color' => '#990000'
                ],
                'keyword1' => [
                    'value' => '灵兰年度会员课程',
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => '一年内20节精品课，学习中医临床思维和技法。',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '若有打扰，敬请谅解!',
                    'color' => '#000000'
                ]
            ];
            try{
                $res = $this->sendTemplateMsgBase($openid, 'bY08wr7-N3XlGeGTn7T68V2rGrTN5tbyOjfpW66so0w',
                    'http://h5.zhongyishuyou.com/wx_h5/User/vippage?third_party=NDQxYWI3MGExN2YyM2YxYWMzMmRkYzBjMWFiYzk3NTA', $item);
                file_put_contents($sucessFile, $info['id']."\n", FILE_APPEND);
            }catch(WxException $e){
                file_put_contents($failFile, $info['id'].'---'.$e->getMessage()."\n", FILE_APPEND);
            }
        }
    }
    /**
     * 异步执行课程更新任务
     */
    public function sendCourseUpdateTask(){
        $msg = AppRedis::instance()->lpop('send_course_update');
        if($msg){
            $msg = json_decode($msg, true);
        }
        if(!empty($msg)){
            if($msg['type'] == 'course'){
                $users = Db::table('syh_user_course')->alias('a')
                ->where(['a.courseid' => $msg['courseid'], 'a.status' => 30, 'b.issubscribe' => 1])
                ->where('a.endtime', ['=', -1], ['>', $_SERVER['REQUEST_TIME']], 'or')
                ->join('syh_user_login b', 'a.userid=b.userid', 'LEFT')->field('b.userid,b.wxopenid')->select();
            }else{
                $users = Db::table('syh_user_role_app')->alias('a')
                ->where('a.roleid', 11)
                ->where('a.endtime', ['=', -1], ['>', $msg['time']], 'or')
                ->where(['b.status' => 0, 'issubscribe' => 1])
                ->join('syh_user_login b', 'a.userid=b.userid', 'LEFT')->field('b.userid,b.wxopenid')->select();
            }
            $item = [
                'first' => [
                    'value' => $msg['msg_top'],
                    'color' => '#000000'
                ],
                'keyword1' => [
                    'value' => $msg['course_title'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $msg['course_type'],
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $msg['course_author'],
                    'color' => '#173177'
                ],
                'keyword4' => [
                    'value' => date('Y-m-d H:i:s', $msg['time']),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $msg['msg_bottom'],
                    'color' => '#173177'
                ]
            ];
            foreach($users as $user){
                if($user['wxopenid']){
                    try{
                        $res = $this->sendTemplateMsgBase($user['wxopenid'], $msg['templateId'], $msg['url'], $item);
                    }catch(WxException $e){
                    }
                }
            }
        }
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