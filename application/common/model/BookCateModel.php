<?php
/**
 * 图书分类模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class BookCateModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_book_cate';
    protected $fields = array(
        'id','catename','type','orderid'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','update_time'];
    }
    /**
     * 分类下图书关联
     * @return \think\model\relation\BelongsToMany
     */
    public function books(){
        return $this->hasMany('BookModel', 'cateid', 'id');
    }
    /**
     * 教材类型文本
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getTypeTextAttr($value,$data){
        $status = [0=>'',1=>'本科精品',2=>'高职高专',3=>'本科精品+高职高专'];
        return $status[$data['type']];
    }
}
?>