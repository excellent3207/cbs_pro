<?php 
/**
 * 后台用户模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\model;
use think\Model;
use think\model\concern\SoftDelete;
class UserAdminModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
	protected $pk = 'id';
 	protected $table = 'cbs_user_admin';
 	protected $fields = array(
 		'id','username','pass','roleid','remark','logintime'
 	);
 	
 	public function setPassAttr($value){
 	    return md5($value);
 	}
 	public function role(){
 	    return $this->belongsTo('RoleAdminModel', 'roleid', 'id');
 	}
 	public function hideField(){
 	    return $this->hidden(self::hiddenFields());
 	}
 	public static function hiddenFields(){
 	    return ['pass','delete_time','update_time'];
 	}
 }
?>