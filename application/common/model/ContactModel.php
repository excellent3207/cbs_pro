<?php
/**
 * 联系方式模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class ContactModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_contact';
    protected $fields = array(
        'id','name','office_phone','phone','qq','addr','orderid'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
}
?>