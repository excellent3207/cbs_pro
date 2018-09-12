<?php 
/**
 * Redis缓存
 * author：xjp
 * create：2017.4.8
 */
namespace app\common;

use think\facade\Config;

class AppRedis{
	private $options = [];
	private static $instance = null;
	private $handler;
	/**
	 * 构造函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct(array $options = []){
		
		if (extension_loaded('redis')) {
		    $this->handler = new \Redis;
		    if(!empty($options)){
		        $this->options = array_merge(config()['cache'], $options);
		    }else{
		        $this->options = config()['cache'];
		    }
		    if ($this->options['persistent']) {
		        $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
		    } else {
		        $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
		    }
		    
		    if ('' != $this->options['password']) {
		        $this->handler->auth($this->options['password']);
		    }
		    
		    /*if (0 != $this->options['select']) {
		        $this->handler->select($this->options['select']);
		    }*/
		} elseif (class_exists('\Predis\Client')) {
		    $params = [];
		    foreach ($this->options as $key => $val) {
		        if (in_array($key, ['aggregate', 'cluster', 'connections', 'exceptions', 'prefix', 'profile', 'replication'])) {
		            $params[$key] = $val;
		            unset($this->options[$key]);
		        }
		    }
		    $this->handler = new \Predis\Client($this->options, $params);
		} else {
		    throw new \BadFunctionCallException('not support: redis');
		}
	}
	
	public static function instance():AppRedis{
		if(is_null(self::$instance)){
			self::$instance = new AppRedis();
		}
		return self::$instance;
	}
	
	/**
	 * 判断缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @return int 存在返回true，否则返回false
	 */
	public function has(string $name){
		return $this->handler->exists($this->getCacheKey($name));
	}
	
	/**
	 * 读取缓存
	 * @access public
	 * @param string $name 缓存变量名
	 * @param mixed  $default 默认值
	 * @return mixed 如果值不存在，返回default，默认false
	 */
	public function get($name, $default = false){
		$value = $this->handler->get($this->getCacheKey($name));
		if (is_null($value)) {
			return $default;
		}
		return $this->formatReturn($value);
	}
	/**
	 * 返回key中字符串值的子字符串，字符串的截取范围由start和end两个偏移量决定(包括start和end在内)。
	 * 负数偏移量表示从字符串最后开始计数，-1表示最后一个字符，-2表示倒数第二个，以此类推。
	 * GETRANGE通过保证子字符串的值域(range)不超过实际字符串的值域来处理超出范围的值域请求。
	 * @param unknown $name
	 * @param unknown $start
	 * @param unknown $end
	 * @return unknown 截取得出的子字符串。
	 */
	public function getrange($name, $start, $end){
		$key = $this->getCacheKey($name);
		return $this->handler->getRange($key, $start, $end);
	}
	/**
	 * 将给定key的值设为value，并返回key的旧值。
	 * 当key存在但不是字符串类型时，返回错误。
	 * @param unknown $name
	 * @param unknown $value
	 * @return unknown
	 * 		返回给定key的旧值(old value)。
	 * 		当key没有旧值时，返回nil。
	 */
	public function getset($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->getSet($key, $value);
	}
	/**
	 * 将key改名为newkey。
	 * 当key和newkey相同或者key不存在时，返回一个错误。
	 * 当newkey已经存在时，RENAME命令将覆盖旧值。
	 * @param unknown $name
	 * @param unknown $newname
	 * @return unknown 改名成功 true，失败 false。
	 */
	public function rename($name, $newname){
		$name= $this->getCacheKey($name);
		$newname= $this->getCacheKey($newname);
		return $this->handler->rename($name, $newname);
	}
	/**
	 * 当且仅当newkey不存在时，将key改为newkey。
	 * 出错的情况和RENAME一样(key不存在时报错)。
	 * @param unknown $name
	 * @param unknown $newname
	 * @return unknown
	 * 		修改成功时，返回1。
	 * 		如果newkey已经存在，返回0。
	 */
	public function renamenx($name, $newname){
		$name= $this->getCacheKey($name);
		$newname= $this->getCacheKey($newname);
		return $this->handler->renameNx($name, $newname);
	}
	/**
	 * 为给定key设置生存时间。
	 * 当key过期时，它会被自动删除。
	 * @param unknown $name
	 * @param unknown $seconds
	 * @return unknown
	 * 		设置成功返回1。
	 * 		当key不存在或者不能为key设置生存时间时(比如在低于2.1.3中你尝试更新key的生存时间)，返回0。
	 */
	public function expire($name, $seconds){
		$key = $this->getCacheKey($name);
		return $this->handler->expire($key, $seconds);
	}
	/**
	 * 移除给定key的生存时间。
	 * @param unknown $name
	 * @return unknown
	 * 		当生存时间移除成功时，返回1.
	 * 		如果key不存在或key没有设置生存时间，返回0。
	 */
	public function persist($name){
		$key = $this->getCacheKey($name);
		return $this->handler->persist($key);
	}
	/**
	 * 排序，分页等
	参数
		array(
			‘by’ => ‘some_pattern_*’,
			‘limit’ => array(0, 1),
			‘get’ => ‘some_other_pattern_*’ or an array of patterns,
			‘sort’ => ‘asc’ or ‘desc’,
			‘alpha’ => TRUE,
			‘store’ => ‘external-key’
		)
		返回或保存给定列表、集合、有序集合key中经过排序的元素。
		排序默认以数字作为对象，值被解释为双精度浮点数，然后进行比较。
		具体查看网站http://www.cnblogs.com/zcy_soft/archive/2012/09/21/2697006.htm#string_SETNX
	 * @param unknown $name
	 * @param unknown $cond
	 * @return unknown
	 */
	public function sort($name, $cond){
		$key = $this->getCacheKey($name);
		return $this->handler->sort($key);
	}
	
	/**
	 * 写入缓存
	 * @access public
	 * @param string    $name 缓存变量名
	 * @param mixed     $value  存储数据
	 * @param integer   $expire  有效时间（秒）
	 * @return boolean expire不合法，返回false
	 */
	public function set($name, $value, $expire = null){
		if (is_null($expire)) {
			$expire = $this->options['expire'];
		}
		$key = $this->getCacheKey($name);
		//对数组/对象数据进行缓存处理，保证数据完整性  byron sampson<xiaobo.sun@qq.com>
		$value = $this->formatStore($value);
		if (is_int($expire) && $expire) {
			$result = $this->handler->setex($key, $expire, $value);
		} else {
			$result = $this->handler->set($key, $value);
		}
		return $result ? true : false;
	}
	/***
	 * 如果key已经存在并且是一个字符串，APPEND命令将value追加到key原来的值之后。
	 * 如果key不存在，APPEND就简单地将给定key设为value，就像执行SET key value一样。
	 * @param unknown $name
	 * @param unknown $value
	 * @return int 追加value之后，key中字符串的长度
	 */
	public function append($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->append($key, $value);
	}
	/**
	 * 写入缓存
	 * @param unknown $name 如果不存在则设置成value，存在不做任何操作
	 * @param unknown $value
	 * @return boolean
	 */
	public function setnx($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->setnx($key, $value);
	}
	
	/**
	 * 自增缓存（针对数值缓存）
	 * @access public
	 * @param string    $name 缓存变量名 缓存变量名 name不存在，以0开始减去数值
	 * @param int       $step 步长
	 * @return false|int 如果值不能表示成数字返回false，否则返回递增后的值
	 */
	public function inc($name, $step = 1){
		$key = $this->getCacheKey($name);
		return $this->handler->incrby($key, $step);
	}
	
	/**
	 * 自减缓存（针对数值缓存）
	 * @access public
	 * @param string    $name 缓存变量名 name不存在，以0开始减去数值
	 * @param int       $step 步长
	 * @return false|int 如果值不能表示成数字返回false，否则返回减去后的值
	 */
	public function dec($name, $step = 1){
		$key = $this->getCacheKey($name);
		return $this->handler->decrby($key, $step);
	}
	
	/**
	 * 删除缓存
	 * @access public
	 * @param string $keys 缓存变量名 可以为字符串，也可以为数组，数组表示移除多个键
	 * @return 移除key的数量
	 */
	public function rm($names){
		if(is_array($names)){
			$names = array_map([$this, 'getCacheKey'], $names);
		}else{
			$names = $this->getCacheKey($names);
		}
		return $this->handler->del($names);
	}
	/**
	 * 返回给定key的剩余生存时间(time to live)(以秒为单位)。
	 * @param unknown $name
	 * @return unknown 
	 * 		key的剩余生存时间(以秒为单位)。
			当key不存在或没有设置生存时间时，返回-1 。
	 */
	public function ttl($name){
		$key = $this->getCacheKey($name);
		return $this->handler->ttl($key);
	}
	/**
	 * 用value参数覆写(Overwrite)给定key所储存的字符串值，从偏移量offset开始。
	 * 不存在的key当作空白字符串处理。
	 * SETRANGE命令会确保字符串足够长以便将value设置在指定的偏移量上，如果给定key原来储存的字符串长度比偏移量小
	 * (比如字符串只有5个字符长，但你设置的offset是10)，那么原字符和偏移量之间的空白将用零比特(zerobytes,"\x00")来填充。
	 * 注意你能使用的最大偏移量是2^29-1(536870911)，因为Redis的字符串被限制在512兆(megabytes)内。如果你需要使用比这更大的空间，你得使用多个key。
	 * @param unknown $name
	 * @param unknown $offset
	 * @param unknown $value
	 * @return unknown
	 */
	public function setrange($name, $offset, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->setRange($key, $offset, $value);
	}
	
	/**
	 * 清除缓存
	 * @access public
	 * @return 此命令从不失败，返回true
	 */
	public function clear(){
		return $this->handler->flushDB();
	}
	/**
	 * 将hash表key中的域field的值设为value，不存在创建，存在修改
	 * @param unknown $key
	 * @param unknown $field
	 * @param unknown $value
	 * @return 如果是新建域，并且设置成功，返回1，如果field已经存在且旧值已被新值覆盖，返回0
	 */
	public function hset($name, $field, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->hSet($key, $field, $value);
	}
	/**
	 * 设置hash表key的域field的值为value，存在不做任何操作，不存在设置
	 * @param unknown $name
	 * @param unknown $field
	 * @param unknown $value
	 * @return boolean 设置成功返回0，设置域已经存在且没有操作，返回0
	 */
	public function hsetnx($name, $field, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->hSetNx($key, $field, $value);
	}
	/**
	 * 批量设置hash表的值
	 * @param unknown $name
	 * @param array $data
	 * @return boolean 成功返回true，key不是hash表类型是返回false
	 */
	public function hmset($name, array $data){
		$key = $this->getCacheKey($name);
		return $this->handler->hMset($key, $data);
	}
	/**
	 * 获取hash表域的值
	 * @param unknown $name
	 * @param unknown $field
	 * @param string $default
	 * @return mixed 域不存在返回default，默认false
	 */
	public function hget($name, $field, $default = false){
		$key = $this->getCacheKey($name);
		$value = $this->handler->hGet($key, $field);
		if(is_null($value)){
			return $default;
		}
		return $this->formatReturn($value);
	}
	/**
	 * 批量获取域的值
	 * @param unknown $name
	 * @param array $fields 多个field数组
	 * @return 返回关联表的值
	 */
	public function hmget($name, array $fields){
		$key = $this->getCacheKey($name);
		return $this->handler->hMget($key, $fields);
	}
	/**
	 * 获取hash表所有的值
	 * @param unknown $name
	 * @return 不存在返回空列表
	 */
	public function hgetall($name){
		$key = $this->getCacheKey($name);
		return $this->handler->hGetAll($key);
	}
	/**
	 * 删除一个或多个指定域，不存在忽略
	 * @param  string $name
	 * @param string $field
	 * @return int 被成功移除的数量，不包括被忽略的域
	 */
	public function hdel($name, string $field){
		$key = $this->getCacheKey($name);
		return $this->handler->hDel($key, $field);
	}
	/**
	 * 返回hash表中域的数量
	 * @param unknown $name
	 * @return int key不存在是返回0
	 */
	public function hlen($name){
		$key = $this->getCacheKey($name);
		return $this->handler->hLen($key);
	}
	/**
	 * hash域的值递增
	 * @param unknown $name
	 * @param unknown $field
	 * @param number $step
	 * @return mixed hash表中计算后的值
	 */
	public function hinc($name, $field, $step = 1){
		$key = $this->getCacheKey($name);
		return $this->handler->hIncrBy($key, $field, $step);
	}
	/**
	 * 返回所有域
	 * @param unknown $name
	 * @return key不存在返回空表
	 */
	public function hkeys($name){
		$key = $this->getCacheKey($name);
		return $this->handler->hKeys($key);
	}
	/**
	 * 返回所有域的值
	 * @param unknown $name
	 * @return key不存在返回空表
	 */
	public function hvals($name){
		$key = $this->getCacheKey($name);
		return $this->handler->hVals($key);
	}
	/**
	 * 将一个值插入到列表key的表头
	 * @param unknown $name
	 * @param string  $value
	 * @return mixed key不存在创建，key不是列表类型时返回false，否则返回列表长度 
	 */
	public function lpush($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->lPush($key, $value);
	}
	/**
	 * 将值插入到列表key的表头，key存在是执行，不存在不做任何操作
	 * @param unknown $name
	 * @param unknown $value
	 * @return 返回执行后表的长度,key不存在返回false
	 */
	public function lpushx($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->lPushx($key, $value);
	}
	/**
	 * 将一个值插入到列表key的末尾
	 * @param unknown $name
	 * @param unknown $value
	 * @return key不存在创建，key不是列表类型时返回false，否则返回列表长度 
	 */
	public function rpush($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->rPush($key, $value);
	}
	/**
	 * 将值插入到列表key的末尾，key存在是执行，不存在不做任何操作
	 * @param unknown $name
	 * @param unknown $value
	 * @return 返回执行后表的长度,key不存在返回false
	 */
	public function rpushx($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->rPushx($key, $value);
	}
	/**
	 * 移除并返回列表key的头元素
	 * @param unknown $name
	 * @return key不存在返回null
	 */
	public  function lpop($name){
		$key = $this->getCacheKey($name);
		return $this->handler->lPop($key);
	}
	/**
	 * 移除并返回列表key的尾元素
	 * @param unknown $name
	 * @return key不存在返回null
	 */
	public function rpop($name){
		$key = $this->getCacheKey($name);
		return $this->handler->rPop($key);
	}
	/**
	 * 阻塞式弹出头元素，列表有值输出，没值阻塞等待输入
	 * @param unknown $name 
	 * @param number $timeout 超时时间
	 * @return unknown 返回key的头元素
	 */
	public function blpop($name, $timeout = 30){
		$key = $this->getCacheKey($name);
		return $this->handler->blPop($key, $timeout);
	}
	/**
	 * 阻塞式弹出尾部=元素，列表有值输出，没值阻塞等待输入
	 * @param unknown $name
	 * @param number $timeout 超时时间
	 * @return unknown 返回key的尾元素
	 */
	public function brpop($name, $timeout = 30){
		$key = $this->getCacheKey($name);
		return $this->handler->brPop($key, $timeout);
	}
	/**
	 * 获取key的长度
	 * @param unknown $name
	 * @return key不存在返回0，否则返回key的长度
	 */
	public function llen($name){
		$key = $this->getCacheKey($name);
		return $this->handler->lLen($key);
	}
	/**
	 * 获取偏移量为start到end的元素
	 * @param unknown $name
	 * @param unknown $start -1最后一个元素 -2倒数第二个元素 以此类推
	 * @param unknown $end -1最后一个元素 -2倒数第二个元素 以此类推
	 * @return 如果start 比end大，返回空列表，end比列表size大返回size-1元素
	 */
	public function lrange($name, $start, $end){
		$key = $this->getCacheKey($name);
		return $this->handler->lrange($key, $start, $end);
	}
	/**
	 * 根据count移除与value相等的元素
	 * @param unknown $name
	 * @param unknown $count 
	 * 		count > 0 从表头搜索，移除与value相等的元素，数量为count
	 * 		count < 0 从表尾搜索，移除与value相等的元素，数量为count
	 * 		count = 0 移除表中所有与value相等的值
	 * @param unknown $value
	 * @return unknown
	 */
	public function lrem($name, $count, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->lrem($key, $count, $value);
	}
	/**
	 * 将列表key下标为index的元素的值设置为value
	 * @param unknown $name
	 * @param unknown $index
	 * @param unknown $value
	 * @return 当index参数超出范围，或对一个空列表(key不存在)进行LSET时，返回false。
	 */
	public function lset($name, $index, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->lSet($key, $index, $value);
	}
	/**
	 * 对一个列表进行修剪(trim)，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
	 * @param unknown $name
	 * @param unknown $start -1倒数第一个，-2倒数第二个，以此类推
	 * @param unknown $end -1倒数第一个，-2倒数第二个，以此类推
	 * @return unknown 如果start下标比列表的最大下标end(LLEN list减去1)还要大，
	 * 		或者start > stop，LTRIM返回一个空列表(因为LTRIM已经将整个列表清空)。
	 * 		如果stop下标比end下标还要大，Redis将stop的值设置为end。
	 */
	public function ltrim($name, $start, $end){
		$key = $this->getCacheKey($name);
		return $this->handler->ltrim($key, $start, $end);
	}
	/**
	 * 返回列表key中，下标为index的元素
	 * @param unknown $name
	 * @param unknown $index -1倒数第一个，-2倒数第二个，以此类推
	 * @return unknown 
	 * 		如果key不是列表类型,返回false
	 * 		列表中下标为index的元素。
	 * 		如果index参数的值不在列表的区间范围内(out of range)，返回null。
	 */
	public function lindex($name, $index){
		$key = $this->getCacheKey($name);
		return $this->handler->lindex($key, $index);
	}
	/**
	 * 将值value插入到列表key当中，位于值pivot之前。
	 * @param unknown $name
	 * @param unknown $pivot
	 * @param unknown $value
	 * @return unknown 
	 * 		如果命令执行成功，返回插入操作完成之后，列表的长度。
	 * 		如果没有找到pivot，返回-1。
	 * 		如果key不存在或为空列表，返回0。
	 */
	public function linsert_before($name, $pivot, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->linsert($key, \Redis::BEFORE, $pivot, $value);
	}
	/**
	 * 将值value插入到列表key当中，位于值pivot之后。
	 * @param unknown $name
	 * @param unknown $pivot
	 * @param unknown $value
	 * @return unknown
	 * 		如果命令执行成功，返回插入操作完成之后，列表的长度。
	 * 		如果没有找到pivot，返回-1。
	 * 		如果key不存在或为空列表，返回0。
	 */
	public function linsert_after($name, $pivot, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->linsert($key, \Redis::AFTER, $pivot, $value);
	}
	/**
	 * 命令RPOPLPUSH在一个原子时间内，执行以下两个动作：
	 * 		将列表source中的最后一个元素(尾元素)弹出，并返回给客户端。
	 * 		将source弹出的元素插入到列表destination，作为destination列表的的头元素。
	 * @param unknown $source
	 * @param unknown $destination
	 * @return 
	 * 		如果source不存在，返回null，并且不执行其他动作。
			如果source和destination相同，则列表中的表尾元素被移动到表头，并返回该元素，可以把这种特殊情况视作列表的旋转(rotation)操作。
	 */
	public function rpoplpush($source, $destination){
		$source= $this->getCacheKey($source);
		$destination= $this->getCacheKey($destination);
		return $this->handler->rpoplpush($source, $destination);
	}
	/**
	 * 阻塞式
	 * 假如在指定时间内没有任何元素被弹出，则返回一个null和等待时长。
	 * 反之，返回一个含有两个元素的列表，第一个元素是被弹出元素的值，第二个元素是等待时长。
	 * @param unknown $source
	 * @param unknown $destination
	 * @param number $timeout
	 * @return unknown
	 */
	public function brpoplpush($source, $destination, $timeout = 30){
		$source= $this->getCacheKey($source);
		$destination= $this->getCacheKey($destination);
		return $this->handler->brpoplpush($source, $destination, $timeout);
	}
	/**
	 * 将元素插入到集合中，已经存在于集合的value元素将被忽略
	 * 假如key不存在，则创建一个只包含member元素作成员的集合。
	 * @param unknown $name
	 * @param unknown $value 可以是数组也可以是字符串
	 * @return unknown 
	 * 		当key不是集合类型时，返回false
	 * 		被添加到集合中的新元素的数量，不包括被忽略的元素。
	 */
	public function sadd($name, $value){
		$key = $this->getCacheKey($name);
		if(is_array($value))
			return $this->handler->sAdd($key, ...$value);
		else
			return $this->handler->sAdd($key, $value);
	}
	/**
	 * 移除集合key中的value元素，不存在的value元素会被忽略。
	 * @param unknown $name
	 * @param unknown $value可以是数组也可以是字符串
	 * @return unknown
	 * 		当key不是集合类型，返回false。
	 * 		被成功移除的元素的数量，不包括被忽略的元素。
	 */
	public function srem($name, $value){
		$key = $this->getCacheKey($name);
		if(is_array($value)){
			return $this->handler->srem($key, ...$value);
		}else{
			return $this->handler->srem($key, $value);
		}
	}
	/**
	 * 返回集合key中的所有成员。
	 * @param unknown $name
	 * @return unknown
	 */
	public function smembers($name){
		$key = $this->getCacheKey($name);
		return $this->handler->sMembers();
	}
	/**
	 * 判断value元素是否是集合key的成员
	 * @param unknown $name
	 * @param unknown $value
	 * @return 
	 * 		如果member元素是集合的成员，返回1。
	 * 		如果member元素不是集合的成员，或key不存在，返回0。
	 */
	public function sismember($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->sismember($key, $value);
	}
	/**
	 * 返回集合key的基数(集合中元素的数量)。
	 * @param unknown $name
	 * @return unknown 当key不存在时，返回0。
	 */
	public function scard($name){
		$key = $this->getCacheKey($name);
		return $this->handler->scard($key);
	}
	/**
	 * 将value元素从source集合移动到destination集合。
	 * SMOVE是原子性操作。
	 * @param unknown $source
	 * @param unknown $destination
	 * @param unknown $value
	 * @return unknown
	 * 		如果member元素被成功移除，返回1。
	 * 		如果member元素不是source集合的成员，并且没有任何操作对destination集合执行，那么返回0。
	 */
	public function smove($source, $destination, $value){
		$source= $this->getCacheKey($source);
		$destination= $this->getCacheKey($destination);
		return $this->handler->sMove($source, $destination, $value);
	}
	/**
	 * 移除并返回集合中的一个随机元素。
	 * @param unknown $name
	 * @return unknown 当key不存在或key是空集时，返回null
	 */
	public function spop($name){
		$key = $this->getCacheKey($name);
		return $this->handler->sPop($key);
	}
	/**
	 * 返回集合中的一个随机元素。
	 * 该操作和SPOP相似，但SPOP将随机元素从集合中移除并返回，而SRANDMEMBER则仅仅返回随机元素，而不对集合进行任何改动。
	 * @param unknown $name
	 * @return unknown 被选中的随机元素。 当key不存在或key是空集时，返回null。
	 */
	public function srandmember($name){
		$key = $this->getCacheKey($name);
		return $this->handler->sRandMember($key);
	}
	/**
	 * 返回集合的交集
	 * @param array $names 
	 * @return unknown
	 */
	public function sinter($names){
		$names = array_map([$this, 'getCacheKey'], $names);
		return $this->handler->sInter(...$names);
	}
	/**
	 * 此命令等同于SINTER，但它将结果保存到destination集合，而不是简单地返回结果集。
	 * 如果destination集合已经存在，则将其覆盖。
	 * destination可以是key本身。
	 * @param unknown $destination
	 * @param array $names
	 * @return int 结果集中的成员数量。
	 */
	public function sinterstore($destination, $names){
		$destination= $this->getCacheKey($destination);
		$names = array_map([$this, 'getCacheKey'], $names);
		return $this->handler->sInterStore($destination, ...$names);
	}
	/**
	 * 返回集合的合集
	 * @param unknown $names
	 * @return unknown
	 */
	public function sunion($names){
		$names = array_map([$this, 'getCacheKey'], $names);
		return $this->handler->sUnion(...$names);
	}
	/**
	 * 此命令等同于SUNION，但它将结果保存到destination集合，而不是简单地返回结果集。
	 * 如果destination已经存在，则将其覆盖。
	 * destination可以是key本身。
	 * @param unknown $destination
	 * @param unknown $names
	 * @return unknown 结果集中的元素数量。
	 */
	public function sunionstore($destination, $names){
		$destination= $this->getCacheKey($destination);
		$names = array_map([$this, 'getCacheKey'], $names);
		return $this->handler->sUnionStore($destination, ...$names);
	}
	/**
	 * 返回集合的差集  
	 * 不存在的key被视为空集。
	 * @param unknown $names
	 * @return unknown
	 */
	public function sdiff($names){
		$names = array_map([$this, 'getCacheKey'], $names);
		return $this->handler->sDiff(...$names);
	}
	/**
	 * 此命令等同于SDIFF，但它将结果保存到destination集合，而不是简单地返回结果集。
	 * 如果destination集合已经存在，则将其覆盖。
	 * destination可以是key本身。
	 * @param unknown $destination
	 * @param unknown $names
	 * @return unknown 结果集中的元素数量。
	 */
	public function sdiffstore($destination, $names){
		$destination= $this->getCacheKey($destination);
		$names = array_map([$this, 'getCacheKey'], $names);
		return $this->handler->sDiffStore($destination, ...$names);
	}
	/**
	 * 将一个或多个member元素及其score值加入到有序集key当中。
	 * 如果某个member已经是有序集的成员，那么更新这个member的score值，并通过重新插入这个member元素，来保证该member在正确的位置上。
	 * score值可以是整数值或双精度浮点数。
	 * 如果key不存在，则创建一个空的有序集并执行ZADD操作。
	 * 当key存在但不是有序集类型时，返回一个false。
	 * @param unknown $name
	 * @param unknown $score
	 * @param unknown $memeber
	 * @return unknown 被成功添加的新成员的数量，不包括那些被更新的、已经存在的成员。
	 */
	public function zadd($name, $score, $value){
		$score = $this->formatScore($score);
		$key = $this->getCacheKey($name);
		return $this->handler->zAdd($key, $score, $value);
	}
	/**
	 * 移除有序集key中的一个或多个成员，不存在的成员将被忽略。
	 * @param unknown $name
	 * @param unknown $values 可以为数组也可以为值
	 * @return unknown 被成功移除的成员的数量，不包括被忽略的成员。
	 */
	public function zrem($name, $values){
		$key = $this->getCacheKey($name);
		if(is_array($values))
			return $this->handler->zRem($key, ...$values);
		else
			return $this->handler->zRem($key, $values);
	}
	/**
	 * 返回有序集key的基数。(数量)
	 * @param unknown $name
	 * @return unknown 当key不存在时，返回0。
	 */ 
	public function zcard($name){
		$key = $this->getCacheKey($name);
		return $this->handler->zCard($key);
	}
	/**
	 * 返回有序集key中，score值在min和max之间(默认包括score值等于min或max)的成员。
	 * @param unknown $name
	 * @param unknown $min
	 * @param unknown $max
	 * @return int score值在min和max之间的成员的数量。
	 */
	public function zcount($name, $min, $max){
		$min = $this->formatScore($min);
		$max = $this->formatScore($max);
		$key = $this->getCacheKey($name);
		return $this->handler->zCount($key, $min, $max);
	}
	/**
	 * 返回有序集key中，成员value的score值。
	 * @param unknown $name
	 * @param unknown $value
	 * @return unknown
	 * 		如果member元素不是有序集key的成员，或key不存在，返回null。
	 * 		member成员的score值，以字符串形式表示。
	 */
	public function zscore($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->zScore($key, $value);
	}
	/**
	 * 为有序集key的成员value的score值加上增量step。
	 * 你也可以通过传递一个负数值step，让score减去相应的值
	 * @param unknown $name
	 * @param unknown $step 可以是整数值或双精度浮点数
	 * @param unknown $value 
	 * @return unknown member成员的新score值，以字符串形式表示。
	 */
	public function zinc($name, $step, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->zIncrBy($key, $step, $value);
	}
	/**
	 * 返回有序集key中，指定区间内的成员。
	 * 其中成员的位置按score值递增(从小到大)来排序。
	 * 具有相同score值的成员按字典序(lexicographical order)来排列。
	 * @param unknown $name
	 * @param unknown $start -1倒数第一个，-2倒数第二个，以此类推
	 * @param unknown $end -1倒数第一个，-2倒数第二个，以此类推
	 * @param string $withScore 返回值是否带有score的值
	 * @return unknown 指定区间内，带有score值(可选)的有序集成员的列表。
	 */
	public function zrange($name, $start, $end, $withScore = false){
		$key = $this->getCacheKey($name);
		return $this->handler->zRange($key, $start, $end, $withScore);
	}
	/**
	 * 返回有序集key中，指定区间内的成员。
	 * 其中成员的位置按score值递减(从大到小)来排列。
	 * @param unknown $name
	 * @param unknown $start
	 * @param unknown $end
	 * @param string $withScore
	 * @return unknown 指定区间内，带有score值(可选)的有序集成员的列表。
	 */
	public function zrevrange($name, $start, $end, $withScore = false){
		$key = $this->getCacheKey($name);
		return $this->handler->zrevrange($key, $start, $end, $withScore);
	}
	/**
	 * 返回有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。有序集成员按score值递增(从小到大)次序排列。
	 * @param unknown $name
	 * @param unknown $min
	 * @param unknown $max
	 * @param string $withScore
	 * @param string $offset
	 * @param number $count
	 * @return unknown 指定区间内，带有score值(可选)的有序集成员的列表。
	 */
	public function zrangebyscore($name, $min, $max, $withScore = false, $offset = false, $count = 0){
		$min = $this->formatScore($min);
		$max = $this->formatScore($max);
		$key = $this->getCacheKey($name);
		if($offset === false)
			return $this->handler->zRangeByScore($key, $min, $max, ['withscores' => $withScore]);
		else 
			return $this->handler->zRangeByScore($key, $min, $max, ['withscores' => $withScore], $offset, $count);
	}
	/**
	 * 返回有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。有序集成员按score值递增(从大到小)次序排列。
	 * @param unknown $name
	 * @param unknown $min
	 * @param unknown $max
	 * @param string $withScore
	 * @param string $offset
	 * @param number $count
	 * @return unknown 指定区间内，带有score值(可选)的有序集成员的列表。
	 */
	public function zrevrangebyscore($name, $min, $max, $withScore = false, $offset = false, $count = 0){
		$min = $this->formatScore($min);
		$max = $this->formatScore($max);
		$key = $this->getCacheKey($name);
		if($offset === false)
			return $this->handler->zRangeByScore($key, $min, $max, $withScore);
			else
				return $this->handler->zRevRangeByScore($key, $max, $min, $withScore, $offset, $count);
	}
	/**
	 * 返回有序集key中成员member的排名。其中有序集成员按score值递增(从小到大)顺序排列。
	 * 排名以0为底，也就是说，score值最小的成员排名为0。
	 * @param unknown $name
	 * @param unknown $value
	 * @return 
	 * 		如果member是有序集key的成员，返回member的排名。
	 * 		如果member不是有序集key的成员，返回null。
	 */
	public function zrank($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->zRank($key, $value);
	}
	/**
	 * 返回有序集key中成员member的排名。其中有序集成员按score值递减(从大到小)排序。
	 * 排名以0为底，也就是说，score值最大的成员排名为0。
	 * @param unknown $name
	 * @param unknown $value
	 * @return unknown 
	 * 		如果member是有序集key的成员，返回member的排名。
	 * 		如果member不是有序集key的成员，返回null。
	 */
	public function zrevrank($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->zRevRank($key, $value);
	}
	/**
	 * 删除有序集的值
	 * @param unknown $name
	 * @param unknown $value
	 * @return unknown
	 */
	public function zdel($name, $value){
		$key = $this->getCacheKey($name);
		return $this->handler->zDelete($key, $value);
	}
	/**
	 * 移除有序集key中，指定排名(rank)区间内的所有成员。
	 * 区间分别以下标参数start和stop指出，包含start和stop在内。
	 * 下标参数start和stop都以0为底，也就是说，以0表示有序集第一个成员，以1表示有序集第二个成员，以此类推。
	 * 你也可以使用负数下标，以-1表示最后一个成员，-2表示倒数第二个成员，以此类推。
	 * @param unknown $name
	 * @param unknown $start
	 * @param unknown $end 
	 * @return unknown 被移除成员的数量。
	 */
	public function zremrangebyrank($name, $start, $end){
		$key = $this->getCacheKey($name);
		return $this->handler->zRemRangeByRank($key, $start, $end);
	}
	/**
	 * 移除有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。
	 * @param unknown $name
	 * @param unknown $min
	 * @param unknown $max
	 * @return unknown 被移除成员的数量。
	 */
	public function zremrangebyscore($name, $min, $max){
		$min = $this->formatScore($min);
		$max = $this->formatScore($max);
		$key = $this->getCacheKey($name);
		return $this->handler->zRemRangeByScore($key, $min, $max);
	}
	/**
	 * 计算给定的一个或多个有序集的交集，其中给定key的数量必须以numkeys参数指定，并将该交集(结果集)储存到destination。
	 * 默认情况下，结果集中某个成员的score值是所有给定集下该成员score值之和。
	 * @param unknown $destination
	 * @param unknown $names
	 * @param array $weights
	 * @param string $aggregate
	 * @return unknown
	 */
	public function zinterstore($destination, $names, $weights = [], $aggregate = 'sum'){
		$destination= $this->getCacheKey($destination);
		$names = array_map([$this, 'getCacheKey'], $names);
		if(empty($weights)){
			$weights = array_pad($weights, count($names), 1);
		}
		return $this->handler->zinterstore($destination, $names, $weights, $aggregate);
	}
	/**
	 * 计算给定的一个或多个有序集的并集，其中给定key的数量必须以numkeys参数指定，并将该并集(结果集)储存到destination。
	 * 默认情况下，结果集中某个成员的score值是所有给定集下该成员score值之和。
	 * WEIGHTS
	 * 		使用WEIGHTS选项，你可以为每个给定有序集分别指定一个乘法因子(multiplication factor)，
	 * 			每个给定有序集的所有成员的score值在传递给聚合函数(aggregation function)之前都要先乘以该有序集的因子。
	 * 		如果没有指定WEIGHTS选项，乘法因子默认设置为1。
	 * AGGREGATE
	 * 		使用AGGREGATE选项，你可以指定并集的结果集的聚合方式。
	 * 		默认
	 * 			使用的参数SUM，可以将所有集合中某个成员的score值之和作为结果集中该成员的score值；
	 * 			使用参数MIN，可以将所有集合中某个成员的最小score值作为结果集中该成员的score值；
	 * 			而参数MAX则是将所有集合中某个成员的最大score值作为结果集中该成员的score值。
	 * @param unknown $destination
	 * @param unknown $names
	 * @param array $weights
	 * @param string $aggregate
	 * @return unknown 保存到destination的结果集的基数。
	 */
	public function zunionstore($destination, $names, $weights = [], $aggregate = 'sum'){
		$destination= $this->getCacheKey($destination);
		$names = array_map([$this, 'getCacheKey'], $names);
		if(empty($weights)){
			$weights = array_pad($weights, count($names), 1);
		}
		return $this->handler->zunionstore($destination, $names, $weights, $aggregate);
	}
	
	
	private function getCacheKey(string $name){
		return $this->options['prefix'] . $name;
	}
	private function formatReturn($value){
		$jsonData = json_decode($value, true);
		// 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据 byron sampson<xiaobo.sun@qq.com>
		return (null === $jsonData) ? $value : $jsonData;
	}
	private function formatStore($value){
		return (is_object($value) || is_array($value)) ? json_encode($value) : $value;;
	}
	private function formatScore($score){
		if($score >= 1000000000){
			$score = intval($score%1000000000);
		}
		return $score;
	}
}
?>