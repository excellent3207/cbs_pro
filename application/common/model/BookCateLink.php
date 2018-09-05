<?php
/**
 * 图书分类关联表模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\model\concern\SoftDelete;
use think\model\Pivot;
class BookCateLink extends Pivot{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $autoWriteTimestamp = true;
    protected $pk = 'id';
    protected $table = 'cbs_book_cate_link';
    protected $fields = array(
        'id','catename'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
}
?>