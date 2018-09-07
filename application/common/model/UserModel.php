<?php
/**
 * 用户模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;
class UserModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $table = 'cbs_user';
    protected $fields = array(
        'id','wx_openid','alias','face','sex','birth','school','professional'
    );
    
    public function hideField(){
        return $this->hidden(self::hiddenFields());
    }
    public static function hiddenFields(){
        return ['delete_time','create_time','update_time'];
    }
    /**
     * 图书所属分类关联
     * @return \think\model\relation\BelongsToMany
     */
    public function books(){
        return $this->belongsToMany('BookModel', '\\app\\common\\model\\BookShelf', 'bookid', 'userid');
    }
}
?>