<?php
/**
 * 图书模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class BookModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_book';
    protected $fields = array(
        'id','name','cateid','type','book_no','author','img_list','img_info','price','plotter','ppt_img','ppt_source','demo_chapter','standard',
        'paper_img','paper_source','publishtime','show_time','recommend_time','description','count_view'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
    /**
     * 设置价格转换
     * @param unknown $value
     * @return mixed
     */
    public function setPriceAttr($value){
        return floor($value*100);
    }
    /**
     * 获取价格
     * @param unknown $value
     * @return string
     */
    public function getPriceAttr($value){
        if($value%100 !== 0){
            return sprintf("%.2f",$value/100);
        }else{
            return intval($value/100).'';
        }
    }
    /**
     * 设置发布时间转换
     * @param unknown $value
     * @return number
     */
    public function setPublishtimeAttr($value){
        return strtotime($value);
    }
    /**
     * 获取发布时间
     * @param unknown $value
     * @return number
     */
    public function getPublishtimeAttr($value){
        if(!$value) return '';
        return date('Y-m-d', $value);
    }
    /**
     * 图书所属分类关联
     * @return \think\model\relation\BelongsToMany
     */
    public function cate(){
        return $this->belongsTo('BookCateModel', 'cateid', 'id');
    }
    /**
     * 教材类型文本
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getTypeTextAttr($value,$data){
        $status = [1=>'本科精品',2=>'高职高专',0=>''];
        return $status[$data['type']];
    }
    /**
     * 音频列表
     * @return \think\model\relation\BelongsToMany
     */
    public function videos(){
        return $this->hasMany('BookVideoModel', 'bookid', 'id');
    }
}
?>