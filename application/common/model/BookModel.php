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
        'id','name','book_no','author','price','has_ppt','plotter','ppt_img','ppt_source','demo_chapter','standard',
        'paper_img','paper_source','publishtime','is_show','show_time','is_recommend','recommend_time','description'
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
     * 设置发布时间转换
     * @param unknown $value
     * @return number
     */
    public function setPublishtimeAttr($value){
        return strtotime($value);
    }
}
?>