<?php 
/**
 * 后台操作导航模型
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\model;
use think\Model;
use think\model\concern\SoftDelete;
class NavAdminModel extends Model{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
	protected $pk = 'navid';
 	protected $table = 'cbs_nav_admin';
 	protected $fields = array(
 		'navid','title','parentid','uri','tag','level','orderid'
 	);
 	
 	public function hideField(){
 	    return $this->hidden(self::hiddenFields());
 	}
 	public static function hiddenFields(){
 	    return ['delete_time','create_time','update_time'];
 	}
 }
?>