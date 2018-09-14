<?php
/**
 * 图书视频模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class BookVideoModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_book_video';
    protected $fields = array(
        'id','title','bookid','ali_vid','show_time'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','update_time'];
    }
    public function getCreateTimeAttr($value, $data){
        return $value?date('Y-m-d H:i:s'): '';
    }
}
?>