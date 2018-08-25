<?php 
/**
 * 搜索引擎
 * author：xjp
 * create：2017.4.8
 */
namespace app\common\sphinx;

class Sphinx{
	private $sphinx = '';
	static private $instance = null;
	public function __construct(){
		require(env('app_path').'common/sphinx/sphinxapi.php');
		$this->sphinx = new \SphinxClient();
		$config = config('sphinx');
		$this->sphinx->SetServer($config['host'], $config['port']);
		$this->sphinx = $this->sphinx;
	}
	static public function instance(){
		if(!self::$instance){
			self::$instance = new Sphinx();
		}
		return self::$instance;
	}
	/**
	 * sphinx搜索 --xjp  2016/02/23
	 * @param unknown $searchkey
	 * @param unknown $index
	 * @param unknown $options
	 * @param unknown $errors
	 * @return Ambigous <boolean, multitype:>
	 * options示例：array(
	 * 		'filter' => array(   //过滤
	 * 			array('field' => '属性字段', 'reverse' => '是否取反 1是 0否, 'data' => array(...)))
	 * 		),
	 * 		'filterRange' =>array(  //区间过滤
	 * 			array('field' => '属性字段', 'reverse' => '是否取反 1是 0否, 'data' => array('最小值', '最大值')))
	 * 		),
	 * //排序模式
	 * 		'sortMode' => array( '排序模式'
	 * 									 SPH_SORT_RELEVANCE（0） Sort by relevance in descending order (best matches first). 
										 SPH_SORT_ATTR_DESC（1） Sort by an attribute in descending order (bigger attribute values first). 
										 SPH_SORT_ATTR_ASC（2） Sort by an attribute in ascending order (smaller attribute values first). 
										 SPH_SORT_TIME_SEGMENTS（3） Sort by time segments (last hour/day/week/month) in descending order, and then by relevance in descending order. 
										 SPH_SORT_EXTENDED Sort（4） by SQL-like combination of columns in ASC/DESC order. 
										 SPH_SORT_EXPR（5） 
									,'排序字段'（可为空）
	 
	 * 		),
	 * 		'fieldWeights' => array(
	 * 			'字段名' => 权重值
	 * 		)
	 * 		'matchMode' => SPH_MATCH_EXTENDED2（6）, SPH_MATCH_ALL（0） 匹配所有查询词（默认模式）. 
													SPH_MATCH_ANY（1） 匹配查询词中的任意一个. 
													SPH_MATCH_PHRASE（2） 将整个查询看作一个词组，要求按顺序完整匹配. 
													SPH_MATCH_BOOLEAN（3） 将查询看作一个布尔表达式. 
													SPH_MATCH_EXTENDED（4） 将查询看作一个Sphinx内部查询语言的表达式. 
													SPH_MATCH_FULLSCAN（5） 使用完全扫描，忽略查询词汇. 
													SPH_MATCH_EXTENDED2（6） 类似 SPH_MATCH_EXTENDED ，并支持评分和权重. 

	 * 		'rankMode'  =>  SPH_RANK_PROXIMITY_BM25 0(默认)  
	 * 						SPH_RANK_BM25 1
	 * 						SPH_RANK_NONE2 
							SPH_RANK_WORDCOUNT 3 
							SPH_RANK_PROXIMITY 4 
							SPH_RANK_MATCHANY 5 
							SPH_RANK_FIELDMASK 6
	 * 		'weight_threshold' => 1000000, 权重阈值
	 
	 * )
	 */
	public function search($searchkey, $index, $options = array(),$pageNum = 1, $pageSize = 10){
		if($searchkey){
			$searchkey = str_replace(array_keys($this->thesaurusToNum), array_values($this->thesaurusToNum), $searchkey);
			$searchkey = str_replace(array_keys($this->numToThesaurus), array_values($this->numToThesaurus), $searchkey);
		}
		//设置匹配模式
		if(isset($options['matchMode']))
			$matchMode = $options['matchMode'];
		else 
			$matchMode = 6;
		$this->sphinx->SetMatchMode($matchMode);
		//设置返回结果类型
		$this->sphinx->setArrayResult(true);
		//设置过滤条件
		$this->sphinx->ResetFilters();
		if(isset($options['filter']) && $options['filter']){
			foreach($options['filter'] as $filter){
				if($filter['reverse'])
					$this->sphinx->setFilter($filter['field'], $filter['data'], true);
				else 
					$this->sphinx->setFilter($filter['field'], $filter['data']);
			}
		}
		//设置区间过滤条件
		if(isset($options['filterRange']) && $options['filterRange']){
			foreach($options['filterRange'] as $filterRange){
				if($filterRange['reverse'])
					$this->sphinx->setFilterRange($filterRange['field'], $filterRange['data'][0][1], true);
				else
					$this->sphinx->setFilterRange($filterRange['field'], $filterRange['data'][0][1]);
			}
		}
		//设置排序模式
		if(isset($options['rankMode']))
			$rankMode = $options['rankMode'];
		else
			$rankMode = 0;
		$this->sphinx->setRankingMode($rankMode);
		//设置排序
		if(isset($options['sortMode']) && $options['sortMode']){
			if(count($options['sortMode']) > 1){
				$this->sphinx->setSortMode ($options['sortMode'][0], $options['sortMode'][1]);
			}else{
				$this->sphinx->setSortMode ($options['sortMode'][0]);
			}
		}
		//设置字段权重
		if(isset($options['fieldWeights']) && $options['fieldWeights']){
			$this->sphinx->setFieldWeights($options['fieldWeights']);
		}
		if($pageNum != -1){
			$offset = ($pageNum - 1) * $pageSize;
			$this->sphinx->setLimits($offset, $pageSize);
		}
		$res = $this->sphinx->query($searchkey, $index);
		$data = array();
		if(isset($res['matches'])){
			$weight_threshold = $options['weight_threshold'] ?? 0;
			$ids = array();
			$weights = array();
			$attrs = array();
			foreach($res['matches'] as $match){
				if(intval($match['weight']) < intval($weight_threshold)) continue;
				$ids[] = $match['id'];
				$weights[$match['id']] = $match['weight'];
				$attrs[$match['id']] = $match['attrs'];
			}
			$data['weights'] = $weights;
			$data['ids'] = $ids;
			$data['attrs'] = $attrs;
		}else{
			//Log::write('Sphinx Error: '.$this->sphinx->getLastError().'------Warning: '.$this->sphinx->getLastWarning());
			//throw new SyhException('没有数据', ERROR_CODE_NODATA);
			$data['ids'] = [];
		}
		return $data;
	}
	/**
	 * 检查同义词 --xjp 2016/08/19
	 */
	private function checkThesaurus($word){
		
	}
	/**
	 * 同义词库 --xjp 2016/08/19
	 * @var unknown
	 */
	private $thesaurusToNum = array(
		'银屑病' => '__1__',
		'银屑' => '__1__',
	);
	private $numToThesaurus = array(
		'__1__' => '银屑病',
	);
}
?>