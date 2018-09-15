<?php
/**
 * 用户书架模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\model\Pivot;
class UserDraft extends Pivot{
    protected $pk = 'id';
    protected $table = 'cbs_user_draft_collect';
    protected $fields = array(
        'id','userid','draftid'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
}
?>