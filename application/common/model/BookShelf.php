<?php
/**
 * 用户书架模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\model\concern\SoftDelete;
use think\model\Pivot;
class BookShelf extends Pivot{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $autoWriteTimestamp = true;
    protected $pk = 'id';
    protected $table = 'cbs_book_shelf';
    protected $fields = array(
        'id','userid','bookid'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
}
?>