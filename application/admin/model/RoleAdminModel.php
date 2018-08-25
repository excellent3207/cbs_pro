<?php 
/**
 * 角色模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\model;
use think\Model;
use think\model\concern\SoftDelete;
class RoleAdminModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
	protected $pk = 'id';
 	protected $table = 'cbs_role_admin';
 	protected $fields = array(
 		'id','name','navids'
 	);
 	
 	public function users(){
 	    return $this->hasMany('UserAdminUser', 'roleid', 'id');
 	}
 	public function hideField(){
 	    return $this->hidden(self::hiddenFields());
 	}
 	public static function hiddenFields(){
 	    return ['delete_time','create_time','update_time'];
 	}
 }
?>