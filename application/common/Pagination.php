<?php 
/**
 * 分页类
 * author：xjp
 * create：2017.4.8
 */
namespace app\common;

use think\facade\Request;

class Pagination{
	private $page,$pageSize,$total,$itemTotal,$preStatus,$nextStatus,$prePage,$nextPage;
	private $showSize = 5;
	
	function __construct(int $page, int $pageSize, int $total){
		$this->itemTotal = $total;
		$this->page = $page;
		$this->pageSize = $pageSize;
		$this->total = ceil($total/$pageSize);
	}
	public function render(){
		$this->renderBase();
		$pages = '';
		for($i = $this->pageMin; $i <= $this->pageMax; $i++){
			if($i == $this->page) $active = 'class="active"';else $active = '';
			$pages .= "<li {$active}><a href='{$this->url}&page={$i}'>{$i}</a></li>";
		}
		$str = <<<STR
<div class="cbs-page clearFix">
	<div class="pull-left item-total" style="padding:18px 0 0">
		共 <i>{$this->itemTotal}</i> 条记录
	</div>
	<div class="pull-right">
		<ul class="pagination">
			<li class="{$this->preStatus}">
				<a href="{$this->url}&page={$this->prePage}" aria-label="Previous"><span aria-hidden="true">&laquo;上一页</span></a>
			</li>
			{$pages}
			<li class="{$this->nextStatus}">
				<a href="{$this->url}&page={$this->nextPage}" aria-label="Next"><span aria-hidden="true">&raquo;下一页</span></a>
			</li>
		</ul>
	</div>
	<div class="pull-right goto-page" style="padding:18px 0 0; width:100px;">
		<div class="col-xs-5 text" style="line-height:32px;padding:0;">页码：</div>
		<div class="col-xs-3 inp" style="padding:0;">
			<input id="cbs-page" type="text" class="form-control" value="{$this->page}" style="padding:0px 4px;height:32px;width:40px;" />
		</div>
		<script>
			$('#cbs-page').bind('keypress', function(e){
				if(event.keyCode == 13){
					location.href="{$this->url}&page=" + $(this).val();
				}
			});
		</script>
	</div>
</div>
STR;
		return $str;
	}
	public function render2(){
		$this->renderBase();
		$pages = '';
		for($i = $this->pageMin; $i <= $this->pageMax; $i++){
			if($i == $this->page) $active = 'class="active"';else $active = '';
			$pages .= "<li {$active}><a data-page=\"{$i}\">{$i}</a></li>";
		}
		$str = <<<STR
<div class="clearFix">
	<div class="pull-left item-total" style="padding:18px 0 0">
		共 <i>{$this->itemTotal}</i> 条记录
	</div>
	<div class="pull-right">
		<ul class="pagination">
			<li class="{$this->preStatus}">
			<a data-page="{$this->prePage}" aria-label="Previous"><span aria-hidden="true">&laquo;上一页</span></a>
			</li>
			{$pages}
			<li class="{$this->nextStatus}">
			<a data-page="{$this->nextPage}" aria-label="Next"><span aria-hidden="true">&raquo;下一页</span></a>
			</li>
		</ul>
	</div>
</div>
STR;
			return $str;
	}
	private function renderBase(){
		$curPage = $this->page;
		$total = $this->total;
		$pageSize = $this->pageSize;
		$showSize = $this->showSize;
		$baseUrl = Request::baseUrl();
		$params = Request::param();
		if(isset($params['page'])){
			unset($params['page']);
		}
		$query = http_build_query($params);
		$this->url = $baseUrl.'?'.$query;
		$this->preStatus = $this->page == 1 ? 'disabled' : '';
		$this->nextStatus = $this->page == $this->total ? 'disabled' : '';
		if($total <=  $showSize){
			$min = 1;
			$max = $total;
		}else{
			$mid = floor($showSize/2);
			if($curPage <= $mid){
				$min = 1;
				if($total > $showSize){
					$max = $showSize;
				}else{
					$max = $total;
				}
			}else{
				if($total - $curPage < $mid){
					$max = $total;
					$min = $total - $showSize + 1;
				}else{
					$min = $curPage - $mid;
					$max = $curPage + $mid;
				}
			}
		}
		$this->pageMin = $min;
		$this->pageMax = $max;
		$this->pages = '';
		if($curPage > 1){
			$this->prePage = $curPage - 1;
		}else{
			$this->prePage = 1;
		}
		if($curPage >= $total){
			$this->nextPage = $total;
		}else{
			$this->nextPage = $curPage + 1;
		}
	}
}
?>